<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\Inscription;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use App\Models\SuiviFinancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BulletinsController extends Controller
{
    public function index(Request $request)
    {
        $user        = Auth::user();
        $eleve       = $user->eleve;
        $anneeActive = AnneeAcademique::active()->first();

        // Toutes les années où l'élève a une inscription (pour le sélecteur)
        $annees = AnneeAcademique::whereIn('id',
            Inscription::where('eleve_id', $eleve->id)->pluck('annee_academique_id')
        )->orderBy('created_at', 'desc')->get();

        // Année consultée — paramètre URL ou année active par défaut
        $anneeConsultee = $request->annee_id
            ? AnneeAcademique::findOrFail($request->annee_id)
            : $anneeActive;

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeConsultee?->id)
            ->with(['classe.serie', 'suiviFinancier'])
            ->first();

        $classe   = $inscription?->classe;
        $effectif = $classe
            ? Inscription::where('classe_id', $classe->id)
                ->where('annee_academique_id', $anneeConsultee?->id)
                ->count()
            : 0;

        // Logique de déblocage :
        // - Année active → on vérifie le solde de cette inscription
        // - Année terminée → on vérifie le solde de l'inscription de l'année ACTIVE
        $estAnneeTerminee = $anneeConsultee?->statut === 'terminee';

        if ($estAnneeTerminee) {
            $inscriptionActive = Inscription::where('eleve_id', $eleve->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->with('suiviFinancier')
                ->first();
            $soldeRestant = $inscriptionActive?->suiviFinancier?->solde_restant ?? 0;
        } else {
            $soldeRestant = $inscription?->suiviFinancier?->solde_restant ?? 0;
        }

        $bulletins = collect();

        if ($inscription) {
            $moyS1 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 1)->first();
            $moyS2 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 2)->first();
            $moyAn = MoyenneAnnuelle::where('inscription_id', $inscription->id)->first();

            $bulletinsStockes = Bulletin::where('inscription_id', $inscription->id)
                ->where('annee_academique_id', $anneeConsultee?->id)
                ->get();

            $bulletinS1 = $bulletinsStockes->where('type', 'semestriel')->where('numero_semestre', 1)->first();
            $bulletinS2 = $bulletinsStockes->where('type', 'semestriel')->where('numero_semestre', 2)->first();
            $bulletinAn = $bulletinsStockes->where('type', 'annuel')->first();

            $bulletins->push([
                'id'         => $bulletinS1?->id,
                'type'       => 'semestre',
                'periode'    => 'Semestre 1',
                'annee'      => $anneeConsultee->libelle,
                'moyenne'    => $moyS1 ? (float) $moyS1->valeur : null,
                'rang'       => $moyS1?->rang,
                'effectif'   => $effectif,
                'disponible' => $classe?->bulletins_publies_s1 && $bulletinS1 !== null,
                'chemin'     => $bulletinS1?->chemin_fichier_pdf,
            ]);

            $bulletins->push([
                'id'         => $bulletinS2?->id,
                'type'       => 'semestre',
                'periode'    => 'Semestre 2',
                'annee'      => $anneeConsultee->libelle,
                'moyenne'    => $moyS2 ? (float) $moyS2->valeur : null,
                'rang'       => $moyS2?->rang,
                'effectif'   => $effectif,
                'disponible' => $classe?->bulletins_publies_s2 && $bulletinS2 !== null,
                'chemin'     => $bulletinS2?->chemin_fichier_pdf,
            ]);

            $bulletins->push([
                'id'         => $bulletinAn?->id,
                'type'       => 'annuel',
                'periode'    => 'Annuel',
                'annee'      => $anneeConsultee->libelle,
                'moyenne'    => $moyAn ? (float) $moyAn->valeur : null,
                'rang'       => $moyAn?->rang,
                'effectif'   => $effectif,
                'decision'   => $moyAn?->decision,
                'disponible' => $classe?->bulletins_publies_annuel && $bulletinAn !== null,
                'chemin'     => $bulletinAn?->chemin_fichier_pdf,
            ]);
        }

        return view('eleve.bulletins', compact(
            'classe', 'anneeConsultee', 'anneeActive',
            'bulletins', 'soldeRestant', 'annees', 'estAnneeTerminee'
        ));
    }

    public function download(Request $request, $id)
    {
        $user        = Auth::user();
        $eleve       = $user->eleve;
        $anneeActive = AnneeAcademique::active()->first();

        // Récupérer le bulletin
        $bulletin = Bulletin::findOrFail($id);

        // Vérifier que le bulletin appartient bien à cet élève
        $inscription = Inscription::where('id', $bulletin->inscription_id)
            ->where('eleve_id', $eleve->id)
            ->with(['classe', 'suiviFinancier'])
            ->firstOrFail();

        $anneeConsultee = AnneeAcademique::findOrFail($bulletin->annee_academique_id);
        $estAnneeTerminee = $anneeConsultee->statut === 'terminee';

        // Vérifier le solde selon l'année
        if ($estAnneeTerminee) {
            $inscriptionActive = Inscription::where('eleve_id', $eleve->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->with('suiviFinancier')
                ->first();
            $soldeRestant = $inscriptionActive?->suiviFinancier?->solde_restant ?? 0;
        } else {
            $soldeRestant = $inscription->suiviFinancier?->solde_restant ?? 0;
        }

        if ($soldeRestant > 0) {
            return back()->with('error', 'Vous ne pouvez pas télécharger votre bulletin tant que vous n\'avez pas soldé l\'intégralité de vos frais de scolarité.');
        }

        // Vérifier que le bulletin est publié
        $classe = $inscription->classe;
        $publie = match($bulletin->type) {
            'semestriel' => $bulletin->numero_semestre == 1
                ? $classe->bulletins_publies_s1
                : $classe->bulletins_publies_s2,
            'annuel' => $classe->bulletins_publies_annuel,
            default  => false,
        };

        if (!$publie) {
            return back()->with('error', 'Ce bulletin n\'est pas encore disponible.');
        }

        if (!Storage::disk('public')->exists($bulletin->chemin_fichier_pdf)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $bulletin->chemin_fichier_pdf,
            'bulletin-' . $bulletin->type . '-' . $anneeConsultee->libelle . '.pdf'
        );
    }
}