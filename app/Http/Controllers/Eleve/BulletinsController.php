<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Inscription;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use Illuminate\Support\Facades\Auth;

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
            // Semestres
            $moyS = MoyenneSemestre::where('inscription_id', $inscription->id)
                ->orderBy('numero_semestre')
                ->get();

            foreach ($moyS as $ms) {
                $bulletins->push([
                    'id'       => 'semestre-' . $ms->numero_semestre,
                    'type'     => 'semestre',
                    'periode'  => 'Semestre ' . $ms->numero_semestre,
                    'annee'    => $anneeActive->libelle,
                    'moyenne'  => (float) $ms->valeur,
                    'rang'     => $ms->rang,
                    'effectif' => $effectif,
                    'disponible' => true,
                ]);
            }

            // Si S1 pas encore calculé, on l'affiche quand même en "non disponible"
            if (!$moyS->where('numero_semestre', 1)->first()) {
                $bulletins->prepend([
                    'id'         => 'semestre-1',
                    'type'       => 'semestre',
                    'periode'    => 'Semestre 1',
                    'annee'      => $anneeActive->libelle,
                    'moyenne'    => null,
                    'rang'       => null,
                    'effectif'   => $effectif,
                    'disponible' => false,
                ]);
            }
            if (!$moyS->where('numero_semestre', 2)->first()) {
                $bulletins->push([
                    'id'         => 'semestre-2',
                    'type'       => 'semestre',
                    'periode'    => 'Semestre 2',
                    'annee'      => $anneeActive->libelle,
                    'moyenne'    => null,
                    'rang'       => null,
                    'effectif'   => $effectif,
                    'disponible' => false,
                ]);
            }

            // Annuel
            $moyAn = MoyenneAnnuelle::where('inscription_id', $inscription->id)->first();
            $bulletins->push([
                'id'         => 'annuel',
                'type'       => 'annuel',
                'periode'    => 'Annuel',
                'annee'      => $anneeActive->libelle,
                'moyenne'    => $moyAn ? (float) $moyAn->valeur : null,
                'rang'       => $moyAn?->rang,
                'effectif'   => $effectif,
                'decision'   => $moyAn?->decision,
                'disponible' => $moyAn !== null,
            ]);
        }

        return view('eleve.bulletins', compact('classe', 'anneeActive', 'bulletins'));
    }
}