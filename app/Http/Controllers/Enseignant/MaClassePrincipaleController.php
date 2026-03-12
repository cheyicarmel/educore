<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaClassePrincipaleController extends Controller
{
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;
        $vue         = $request->input('vue', '1');

        $attribution = Attribution::where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->where('est_prof_principal', true)
            ->with(['classe.serie'])
            ->firstOrFail();

        $classe   = $attribution->classe;
        $effectif = $classe->inscriptions()
            ->where('annee_academique_id', $anneeActive?->id)
            ->count();

        $attributionsClasse = Attribution::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with(['matiere', 'enseignant.user'])
            ->get();

        $totalMatieres = $attributionsClasse->count();

        $inscriptions = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('eleve.user')
            ->get()
            ->sortBy(fn($i) => $i->eleve->user->nom . ' ' . $i->eleve->user->prenom)
            ->values();

        $inscriptionIds = $inscriptions->pluck('id');
        $parametres     = Parametre::instance();

        // Valeurs par défaut pour TOUTES les variables
        // Nécessaire car le compact() final a besoin de toutes les variables
        // peu importe quelle branche (semestrielle ou annuelle) est active
        $semestre                  = null;
        $statutsParMatiere         = [];
        $matieresSaisieComplete    = 0;
        $toutesNotesSaisies        = false;
        $elevesAvecMoyenne         = 0;
        $moyenneClasse             = null;
        $elevesAvecStats           = collect();
        $elevesPassants            = 0;
        $elevesDoublants           = 0;
        $moyenneAnnuelleClasse     = null;
        $elevesAvecMoyenneAnnuelle = 0;
        $peutCalculerAnnuel        = false;
        $elevesAnnuels             = collect();

        // VUE SEMESTRE 1 ou 2 
        if ($vue === '1' || $vue === '2') {
            $semestre = (int) $vue;

            foreach ($attributionsClasse as $attr) {
                $notesSaisies   = Note::whereIn('inscription_id', $inscriptionIds)
                    ->where('matiere_id', $attr->matiere_id)
                    ->where('numero_semestre', $semestre)
                    ->count();
                $notesAttendues = $effectif * 5;
                $taux           = $notesAttendues > 0 ? round(($notesSaisies / $notesAttendues) * 100) : 0;
                $complet        = $notesSaisies >= $notesAttendues && $notesAttendues > 0;

                if ($complet) $matieresSaisieComplete++;

                $statutsParMatiere[] = [
                    'matiere'         => $attr->matiere->nom,
                    'enseignant'      => $attr->enseignant->user->prenom . ' ' . $attr->enseignant->user->nom,
                    'notes_saisies'   => $notesSaisies,
                    'notes_attendues' => $notesAttendues,
                    'taux'            => $taux,
                    'complet'         => $complet,
                ];
            }

            $toutesNotesSaisies = $matieresSaisieComplete === $totalMatieres && $totalMatieres > 0;

            $moyennesSemestre = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', $semestre)
                ->get()
                ->keyBy('inscription_id');

            $elevesAvecMoyenne = $moyennesSemestre->count();
            $moyenneClasse     = $elevesAvecMoyenne > 0
                ? round($moyennesSemestre->avg('valeur'), 2)
                : null;

            // Moyennes par matière pour chaque élève
            $moyennesParMatiere = MoyenneMatiere::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', $semestre)
                ->get()
                ->groupBy('inscription_id');

            $elevesAvecStats = $inscriptions->map(function ($inscription) use ($moyennesSemestre, $parametres, $moyennesParMatiere, $attributionsClasse) {
                $moy        = $moyennesSemestre[$inscription->id] ?? null;
                $moyMatieres = $moyennesParMatiere[$inscription->id] ?? collect();

                $detailMatieres = $attributionsClasse->map(function ($attr) use ($moyMatieres) {
                    $m = $moyMatieres->firstWhere('matiere_id', $attr->matiere_id);
                    return [
                        'matiere_id'              => $attr->matiere_id,
                        'matiere'                 => $attr->matiere->nom,
                        'moyenne_generale'        => $m?->moyenne_generale,
                        'moyenne_avec_coefficient'=> $m?->moyenne_avec_coefficient,
                    ];
                });

                return [
                    'eleve'          => $inscription->eleve->user,
                    'moyenne'        => $moy?->valeur,
                    'rang'           => $moy?->rang,
                    'mention'        => $moy ? $parametres->getMention((float) $moy->valeur) : null,
                    'detail_matieres'=> $detailMatieres,
                ];
            })->sortBy('rang')->values();
        }

        // VUE ANNUELLE
        elseif ($vue === 'annuel') {
            $moyennesAnnuelles = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)
                ->get()
                ->keyBy('inscription_id');

            $elevesAvecMoyenneAnnuelle = $moyennesAnnuelles->count();
            $elevesPassants            = $moyennesAnnuelles->where('decision', 'passant')->count();
            $elevesDoublants           = $moyennesAnnuelles->where('decision', 'doublant')->count();
            $moyenneAnnuelleClasse     = $elevesAvecMoyenneAnnuelle > 0
                ? round($moyennesAnnuelles->avg('valeur'), 2)
                : null;

            $s1Count = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)->where('numero_semestre', 1)->count();
            $s2Count = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)->where('numero_semestre', 2)->count();
            $peutCalculerAnnuel = $s1Count === $effectif && $s2Count === $effectif && $effectif > 0;

            $moyS1 = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)->where('numero_semestre', 1)->get()->keyBy('inscription_id');
            $moyS2 = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)->where('numero_semestre', 2)->get()->keyBy('inscription_id');

            $elevesAnnuels = $inscriptions->map(function ($inscription) use ($moyennesAnnuelles, $moyS1, $moyS2) {
                $annuelle = $moyennesAnnuelles[$inscription->id] ?? null;
                return [
                    'eleve'        => $inscription->eleve->user,
                    'moy_s1'       => $moyS1[$inscription->id]?->valeur ?? null,
                    'moy_s2'       => $moyS2[$inscription->id]?->valeur ?? null,
                    'moy_annuelle' => $annuelle?->valeur,
                    'rang'         => $annuelle?->rang,
                    'decision'     => $annuelle?->decision,
                ];
            })->sortBy('rang')->values();
        }

        return view('enseignant.ma-classe-principale', compact(
            'classe', 'effectif', 'anneeActive', 'vue', 'semestre',
            'statutsParMatiere', 'totalMatieres', 'matieresSaisieComplete',
            'toutesNotesSaisies', 'elevesAvecMoyenne', 'moyenneClasse', 'elevesAvecStats',
            'elevesPassants', 'elevesDoublants', 'moyenneAnnuelleClasse',
            'elevesAvecMoyenneAnnuelle', 'peutCalculerAnnuel', 'elevesAnnuels'
        ));
    }

    public function calculerMoyennes(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre'  => 'required|in:1,2,annuel',
        ]);

        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;
        $classe      = \App\Models\Classe::findOrFail($request->classe_id);

        $estProfPrincipal = Attribution::where('enseignant_id', $enseignant->id)
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->where('est_prof_principal', true)
            ->exists();

        if (!$estProfPrincipal) {
            return back()->with('error', 'Action non autorisée.');
        }

        $inscriptions = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->get();

        $attributionsClasse = Attribution::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('matiere')
            ->get();

        $estPremierCycle = in_array($classe->niveau, ['6ème', '5ème']);

        DB::transaction(function () use ($request, $inscriptions, $attributionsClasse, $estPremierCycle) {

            if ($request->semestre === 'annuel') {
                $rangs = [];
                foreach ($inscriptions as $inscription) {
                    $s1 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 1)->first();
                    $s2 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 2)->first();
                    if ($s1 && $s2) {
                        $valeur = (($s2->valeur * 2) + $s1->valeur) / 3;
                        MoyenneAnnuelle::updateOrCreate(
                            ['inscription_id' => $inscription->id],
                            ['valeur' => round($valeur, 2), 'decision' => $valeur >= 10 ? 'passant' : 'doublant']
                        );
                        $rangs[$inscription->id] = $valeur;
                    }
                }
                arsort($rangs);
                $rang = 1;
                foreach ($rangs as $inscriptionId => $valeur) {
                    MoyenneAnnuelle::where('inscription_id', $inscriptionId)->update(['rang' => $rang++]);
                }

            } else {
                $semestre = (int) $request->semestre;
                $rangs    = [];

                foreach ($inscriptions as $inscription) {
                    $moyennesMatiere = [];
                    $sommeCoeff      = 0;
                    $sommePonderee   = 0;

                    foreach ($attributionsClasse as $attr) {
                        $notes = Note::where('inscription_id', $inscription->id)
                            ->where('matiere_id', $attr->matiere_id)
                            ->where('numero_semestre', $semestre)
                            ->get()
                            ->keyBy('type');

                        if ($notes->count() < 5) continue;

                        $moyInterro = ($notes['interrogation1']->valeur + $notes['interrogation2']->valeur + $notes['interrogation3']->valeur) / 3;
                        $moyGen     = ($moyInterro + $notes['devoir1']->valeur + $notes['devoir2']->valeur) / 3;

                        if ($estPremierCycle) {
                            $moyAvecCoeff      = null;
                            $moyennesMatiere[] = $moyGen;
                        } else {
                            $coeff = \App\Models\CoefficientMatiere::where('matiere_id', $attr->matiere_id)
                                ->where('classe_id', $inscription->classe_id)
                                ->first()?->coefficient ?? 1;
                            $moyAvecCoeff   = $moyGen * $coeff;
                            $sommeCoeff    += $coeff;
                            $sommePonderee += $moyAvecCoeff;
                        }

                        MoyenneMatiere::updateOrCreate(
                            ['inscription_id' => $inscription->id, 'matiere_id' => $attr->matiere_id, 'numero_semestre' => $semestre],
                            [
                                'moyenne_interrogations'  => round($moyInterro, 2),
                                'moyenne_generale'        => round($moyGen, 2),
                                'moyenne_avec_coefficient'=> $moyAvecCoeff ? round($moyAvecCoeff, 2) : null,
                            ]
                        );
                    }

                    if ($estPremierCycle && count($moyennesMatiere) > 0) {
                        $moyenneSemestre = array_sum($moyennesMatiere) / count($moyennesMatiere);
                    } elseif (!$estPremierCycle && $sommeCoeff > 0) {
                        $moyenneSemestre = $sommePonderee / $sommeCoeff;
                    } else {
                        continue;
                    }

                    MoyenneSemestre::updateOrCreate(
                        ['inscription_id' => $inscription->id, 'numero_semestre' => $semestre],
                        ['valeur' => round($moyenneSemestre, 2)]
                    );

                    $rangs[$inscription->id] = $moyenneSemestre;
                }

                arsort($rangs);
                $rang = 1;
                foreach ($rangs as $inscriptionId => $valeur) {
                    MoyenneSemestre::where('inscription_id', $inscriptionId)
                        ->where('numero_semestre', $semestre)
                        ->update(['rang' => $rang++]);
                }
            }
        });

        $vue = $request->semestre === 'annuel' ? 'annuel' : $request->semestre;
        return redirect()->route('enseignant.ma-classe', ['vue' => $vue])
            ->with('success', 'Moyennes calculées et classement effectué avec succès.');
    }

    public function genererReleve(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre'  => 'required|in:1,2,annuel',
        ]);

        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;
        $classe      = \App\Models\Classe::with('serie')->findOrFail($request->classe_id);

        // Vérifier que c'est bien le prof principal
        $estProfPrincipal = Attribution::where('enseignant_id', $enseignant->id)
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->where('est_prof_principal', true)
            ->exists();

        if (!$estProfPrincipal) {
            return back()->with('error', 'Action non autorisée.');
        }

        $inscriptions = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('eleve.user')
            ->get();

        $inscriptionIds = $inscriptions->pluck('id');

        $attributions = Attribution::where('classe_id', $classe->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('matiere')
            ->get();

        $parametres = \App\Models\Parametre::instance();

        if ($request->semestre === 'annuel') {

            $moyS1 = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', 1)->get()->keyBy('inscription_id');
            $moyS2 = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', 2)->get()->keyBy('inscription_id');
            $moyAnnuelles = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)
                ->get()->keyBy('inscription_id');

            $eleves = $inscriptions->map(function ($inscription) use ($moyS1, $moyS2, $moyAnnuelles) {
                $annuelle = $moyAnnuelles[$inscription->id] ?? null;
                return [
                    'eleve'        => $inscription->eleve->user,
                    'moy_s1'       => $moyS1[$inscription->id]?->valeur ?? null,
                    'moy_s2'       => $moyS2[$inscription->id]?->valeur ?? null,
                    'moy_annuelle' => $annuelle?->valeur,
                    'rang'         => $annuelle?->rang,
                    'decision'     => $annuelle?->decision,
                ];
            })->sortBy(fn($i) => $i['eleve']->nom . ' ' . $i['eleve']->prenom)->values();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('enseignant.pdf.releve-annuel', compact(
                'classe', 'anneeActive', 'eleves', 'parametres'
            ));

            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('releve-annuel-' . $classe->nom . '.pdf');

        } else {

            $semestre = (int) $request->semestre;

            $moyennesSemestre = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', $semestre)
                ->get()->keyBy('inscription_id');

            $moyennesParMatiere = MoyenneMatiere::whereIn('inscription_id', $inscriptionIds)
                ->where('numero_semestre', $semestre)
                ->get()->groupBy('inscription_id');

            $parametresApp = \App\Models\Parametre::instance();

            $eleves = $inscriptions->map(function ($inscription) use ($moyennesSemestre, $moyennesParMatiere, $attributions, $parametresApp) {
                $moy         = $moyennesSemestre[$inscription->id] ?? null;
                $moyMatieres = $moyennesParMatiere[$inscription->id] ?? collect();

                $detailMatieres = $attributions->map(function ($attr) use ($moyMatieres) {
                    $m = $moyMatieres->firstWhere('matiere_id', $attr->matiere_id);
                    return [
                        'matiere'                 => $attr->matiere->nom,
                        'moyenne_generale'        => $m?->moyenne_generale,
                        'moyenne_avec_coefficient'=> $m?->moyenne_avec_coefficient,
                    ];
                });

                return [
                    'eleve'           => $inscription->eleve->user,
                    'moyenne'         => $moy?->valeur,
                    'rang'            => $moy?->rang,
                    'mention'         => $moy ? $parametresApp->getMention((float) $moy->valeur) : null,
                    'detail_matieres' => $detailMatieres,
                ];
            })->sortBy(fn($i) => $i['eleve']->nom . ' ' . $i['eleve']->prenom)->values();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('enseignant.pdf.releve-semestre', compact(
                'classe', 'anneeActive', 'semestre', 'eleves', 'attributions', 'parametres'
            ));

            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('releve-S' . $semestre . '-' . $classe->nom . '.pdf');
        }
    }
}