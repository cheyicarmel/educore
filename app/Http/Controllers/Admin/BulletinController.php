<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\CoefficientMatiere;
use App\Models\Inscription;
use App\Models\MoyenneAnnuelle;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneSemestre;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BulletinController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();

        $classes = Classe::where('annee_academique_id', $anneeActive?->id)
            ->with('serie')
            ->orderBy('nom')
            ->get();

        return view('admin.bulletins.index', compact('classes', 'anneeActive'));
    }

    public function publier(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre'  => 'required|in:1,2,annuel',
        ]);

        $anneeActive = AnneeAcademique::active()->first();
        $classe      = Classe::findOrFail($request->classe_id);
        $semestre    = $request->semestre;

        // Vérifier que les moyennes sont bien calculées
        $inscriptions   = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->get();
        $inscriptionIds = $inscriptions->pluck('id');
        $effectif       = $inscriptions->count();

        if ($semestre === 'annuel') {
            $nbMoyennes = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)->count();
        } else {
            $nbMoyennes = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', (int) $semestre)
                ->count();
        }

        if ($nbMoyennes < $effectif) {
            return back()->with('error', 'Les moyennes ne sont pas encore toutes calculées pour cette classe.');
        }

        // Générer les PDFs pour chaque élève
        $this->genererBulletinsClasse($classe, $inscriptions, $semestre, $anneeActive);

        // Marquer comme publié
        $colonne = $semestre === 'annuel' ? 'bulletins_publies_annuel' : "bulletins_publies_s{$semestre}";
        $classe->update([$colonne => true]);

        return back()->with('success', "Bulletins publiés et générés pour {$classe->nom}.");
    }

    public function depublier(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre'  => 'required|in:1,2,annuel',
        ]);

        $classe   = Classe::findOrFail($request->classe_id);
        $semestre = $request->semestre;
        $colonne  = $semestre === 'annuel' ? 'bulletins_publies_annuel' : "bulletins_publies_s{$semestre}";

        $classe->update([$colonne => false]);

        return back()->with('success', "Bulletins dépubliés pour {$classe->nom}.");
    }

    private function genererBulletinsClasse($classe, $inscriptions, $semestre, $anneeActive)
    {
        $attributions = Attribution::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive->id)
            ->with('matiere')
            ->get();

        $parametres     = Parametre::instance();
        $inscriptionIds = $inscriptions->pluck('id');

        // Stats classe pour le bulletin
        if ($semestre === 'annuel') {
            $moyennesClasse = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)->get();
        } else {
            $moyennesClasse = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', (int) $semestre)
                ->get();
        }

        $moyenneClasse  = round($moyennesClasse->avg('valeur'), 2);
        $effectif       = $inscriptions->count();

        foreach ($inscriptions as $inscription) {
            $inscription->load('eleve.user');

            if ($semestre === 'annuel') {
                $data = $this->preparerDonneesAnnuel($inscription, $inscriptionIds, $parametres);
            } else {
                $data = $this->preparerDonneesSemestre($inscription, (int) $semestre, $attributions, $classe, $parametres);
            }

            $data = array_merge($data, [
                'classe'         => $classe,
                'anneeActive'    => $anneeActive,
                'semestre'       => $semestre,
                'parametres'     => $parametres,
                'moyenneClasse'  => $moyenneClasse,
                'effectif'       => $effectif,
            ]);

            $view = $semestre === 'annuel'
                ? 'admin.bulletins.pdf.bulletin-annuel'
                : 'admin.bulletins.pdf.bulletin-semestriel';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');

            $dossier  = "bulletins/{$anneeActive->id}/{$classe->id}";
            $nomFichier = $semestre === 'annuel'
                ? "bulletin-annuel-{$inscription->id}.pdf"
                : "bulletin-s{$semestre}-{$inscription->id}.pdf";

            $chemin = "{$dossier}/{$nomFichier}";

            Storage::disk('public')->put($chemin, $pdf->output());

            $type = $semestre === 'annuel' ? 'annuel' : 'semestriel';
            $numeroSemestre = $semestre === 'annuel' ? null : (int) $semestre;

            Bulletin::updateOrCreate(
                [
                    'inscription_id'     => $inscription->id,
                    'annee_academique_id'=> $anneeActive->id,
                    'type'               => $type,
                    'numero_semestre'    => $numeroSemestre,
                ],
                [
                    'genere_par'         => Auth::id(),
                    'chemin_fichier_pdf' => $chemin,
                    'date_generation'    => now(),
                ]
            );
        }
    }

    private function preparerDonneesSemestre($inscription, $semestre, $attributions, $classe, $parametres)
    {
        $estPremierCycle = in_array($classe->niveau, ['6ème', '5ème']);

        $moyMatieres = MoyenneMatiere::where('inscription_id', $inscription->id)
            ->where('numero_semestre', $semestre)
            ->get()->keyBy('matiere_id');

        $moyS = MoyenneSemestre::where('inscription_id', $inscription->id)
            ->where('numero_semestre', $semestre)
            ->first();

        $detailMatieres = $attributions->map(function ($attr) use ($moyMatieres, $estPremierCycle) {
            $m     = $moyMatieres[$attr->matiere_id] ?? null;
            $coeff = $estPremierCycle ? null : (CoefficientMatiere::where('matiere_id', $attr->matiere_id)
                ->where('classe_id', $attr->classe_id)
                ->first()?->coefficient ?? 1);

            return [
                'matiere'                 => $attr->matiere->nom,
                'coefficient'             => $coeff,
                'moyenne_interrogations'  => $m?->moyenne_interrogations,
                'moyenne_generale'        => $m?->moyenne_generale,
                'moyenne_avec_coefficient'=> $m?->moyenne_avec_coefficient,
            ];
        });

        return [
            'eleve'          => $inscription->eleve->user,
            'detailMatieres' => $detailMatieres,
            'moyenne'        => $moyS?->valeur,
            'rang'           => $moyS?->rang,
            'mention'        => $moyS ? $parametres->getMention((float) $moyS->valeur) : null,
            'estPremierCycle'=> $estPremierCycle,
        ];
    }

    private function preparerDonneesAnnuel($inscription, $inscriptionIds, $parametres)
    {
        $moyS1    = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 1)->first();
        $moyS2    = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 2)->first();
        $annuelle = MoyenneAnnuelle::where('inscription_id', $inscription->id)->first();

        return [
            'eleve'        => $inscription->eleve->user,
            'moy_s1'       => $moyS1?->valeur,
            'moy_s2'       => $moyS2?->valeur,
            'moy_annuelle' => $annuelle?->valeur,
            'rang'         => $annuelle?->rang,
            'decision'     => $annuelle?->decision,
            'mention'      => $annuelle ? $parametres->getMention((float) $annuelle->valeur) : null,
        ];
    }
}