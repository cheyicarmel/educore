<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\Inscription;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BulletinsController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $anneeActive = AnneeAcademique::active()->first();
        $eleve       = $user->eleve;

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('classe.serie')
            ->first();

        $classe   = $inscription?->classe;
        $effectif = $classe
            ? Inscription::where('classe_id', $classe->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->count()
            : 0;

        $bulletins = collect();

        if ($inscription) {
            $moyS1 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 1)->first();
            $moyS2 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 2)->first();
            $moyAn = MoyenneAnnuelle::where('inscription_id', $inscription->id)->first();

            // Bulletins stockés pour cet élève
            $bulletinsStockes = Bulletin::where('inscription_id', $inscription->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->get();

            $bulletinS1 = $bulletinsStockes->where('type', 'semestriel')->where('numero_semestre', 1)->first();
            $bulletinS2 = $bulletinsStockes->where('type', 'semestriel')->where('numero_semestre', 2)->first();
            $bulletinAn = $bulletinsStockes->where('type', 'annuel')->first();

            // S1
            $bulletins->push([
                'id'          => $bulletinS1?->id,
                'type'        => 'semestre',
                'periode'     => 'Semestre 1',
                'annee'       => $anneeActive->libelle,
                'moyenne'     => $moyS1 ? (float) $moyS1->valeur : null,
                'rang'        => $moyS1?->rang,
                'effectif'    => $effectif,
                'disponible'  => $classe?->bulletins_publies_s1 && $bulletinS1 !== null,
                'chemin'      => $bulletinS1?->chemin_fichier_pdf,
            ]);

            // S2
            $bulletins->push([
                'id'          => $bulletinS2?->id,
                'type'        => 'semestre',
                'periode'     => 'Semestre 2',
                'annee'       => $anneeActive->libelle,
                'moyenne'     => $moyS2 ? (float) $moyS2->valeur : null,
                'rang'        => $moyS2?->rang,
                'effectif'    => $effectif,
                'disponible'  => $classe?->bulletins_publies_s2 && $bulletinS2 !== null,
                'chemin'      => $bulletinS2?->chemin_fichier_pdf,
            ]);

            // Annuel
            $bulletins->push([
                'id'          => $bulletinAn?->id,
                'type'        => 'annuel',
                'periode'     => 'Annuel',
                'annee'       => $anneeActive->libelle,
                'moyenne'     => $moyAn ? (float) $moyAn->valeur : null,
                'rang'        => $moyAn?->rang,
                'effectif'    => $effectif,
                'decision'    => $moyAn?->decision,
                'disponible'  => $classe?->bulletins_publies_annuel && $bulletinAn !== null,
                'chemin'      => $bulletinAn?->chemin_fichier_pdf,
            ]);
        }

        return view('eleve.bulletins', compact('classe', 'anneeActive', 'bulletins'));
    }

    public function download($id)
    {
        $user        = Auth::user();
        $anneeActive = AnneeAcademique::active()->first();
        $eleve       = $user->eleve;

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->first();

        // Vérifier que le bulletin appartient bien à cet élève
        $bulletin = Bulletin::where('id', $id)
            ->where('inscription_id', $inscription->id)
            ->firstOrFail();

        // Vérifier que le bulletin est publié
        $classe   = $inscription->classe;
        $publie   = match($bulletin->type) {
            'semestriel' => $bulletin->numero_semestre == 1
                ? $classe->bulletins_publies_s1
                : $classe->bulletins_publies_s2,
            'annuel'     => $classe->bulletins_publies_annuel,
            default      => false,
        };

        if (!$publie) {
            return back()->with('error', 'Ce bulletin n\'est pas encore disponible.');
        }

        if (!Storage::disk('public')->exists($bulletin->chemin_fichier_pdf)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $bulletin->chemin_fichier_pdf,
            'bulletin-' . $bulletin->type . '-' . now()->format('Y') . '.pdf'
        );
    }
}