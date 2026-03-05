<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;

        // Attributions de l'enseignant pour l'année active
        $attributions = Attribution::where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with(['classe.inscriptions', 'matiere'])
            ->get();

        $totalClasses = $attributions->count();

        // Total élèves distincts dans toutes ses classes
        $totalEleves = $attributions->sum(fn($a) =>
            $a->classe->inscriptions->where('annee_academique_id', $anneeActive?->id)->count()
        );

        // Matière de l'enseignant
        $matiere = $attributions->first()?->matiere->nom ?? '—';

        // Notes saisies par cet enseignant cette année
        $inscriptionIds = $attributions->flatMap(fn($a) =>
            $a->classe->inscriptions->where('annee_academique_id', $anneeActive?->id)->pluck('id')
        )->unique()->values();

        $totalNotes = Note::whereIn('inscription_id', $inscriptionIds)
            ->where('enseignant_id', $enseignant->id)
            ->count();

        // Notes manquantes — pour chaque classe, 2 notes attendues par élève par semestre (devoir + interro)
        // 2 semestres × 2 types × total élèves = notes attendues
        $notesAttendues = $totalEleves * 5 * 2;
        $notesManquantes = max(0, $notesAttendues - $totalNotes);

        // Dernières notes saisies
        $dernieresNotes = Note::whereIn('inscription_id', $inscriptionIds)
            ->where('enseignant_id', $enseignant->id)
            ->with(['inscription.eleve.user', 'inscription.classe'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('enseignant.dashboard', compact(
            'attributions', 'totalClasses', 'totalEleves',
            'matiere', 'totalNotes', 'notesManquantes', 'dernieresNotes',
            'anneeActive'
        ));
    }
}