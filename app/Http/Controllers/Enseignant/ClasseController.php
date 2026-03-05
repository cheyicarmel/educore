<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;

        $attributions = Attribution::where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with(['classe.serie', 'matiere'])
            ->get();

        $matiere = $attributions->first()?->matiere->nom ?? '—';

        // Stats par classe
        $statsParClasse = [];
        foreach ($attributions as $attribution) {
            $classe = $attribution->classe;

            $effectif = $classe->inscriptions()
                ->where('annee_academique_id', $anneeActive?->id)
                ->count();

            $inscriptionIds = $classe->inscriptions()
                ->where('annee_academique_id', $anneeActive?->id)
                ->pluck('id');

            $notesSaisies = Note::whereIn('inscription_id', $inscriptionIds)
                ->where('enseignant_id', $enseignant->id)
                ->count();

            // 5 notes × 2 semestres × effectif
            $notesAttendues = $effectif * 5 * 2;

            $statsParClasse[$classe->id] = [
                'effectif'        => $effectif,
                'notes_saisies'   => $notesSaisies,
                'notes_attendues' => $notesAttendues,
                'complet'         => $notesSaisies >= $notesAttendues && $notesAttendues > 0,
            ];
        }

        return view('enseignant.classes', compact(
            'attributions', 'matiere', 'anneeActive', 'statsParClasse'
        ));
    }
}