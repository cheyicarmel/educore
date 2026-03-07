<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Inscription;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneSemestre;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $user        = Auth::user();
        $anneeActive = AnneeAcademique::active()->first();
        $eleve       = $user->eleve;
        $semestre    = (int) $request->input('semestre', 1);

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('classe.serie')
            ->first();

        $classe = $inscription?->classe;

        // Toutes les attributions de la classe = toutes les matières
        $attributions = $classe
            ? Attribution::where('classe_id', $classe->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->with(['matiere', 'enseignant.user'])
                ->get()
            : collect();

        // Notes de l'élève pour ce semestre
        $notes = $inscription
            ? Note::where('inscription_id', $inscription->id)
                ->where('numero_semestre', $semestre)
                ->get()
                ->groupBy('matiere_id')
            : collect();

        // Moyennes validées en base pour ce semestre
        $moyennes = $inscription
            ? MoyenneMatiere::where('inscription_id', $inscription->id)
                ->where('numero_semestre', $semestre)
                ->get()
                ->keyBy('matiere_id')
            : collect();

        // Construire le tableau par matière
        $types = ['interrogation1', 'interrogation2', 'interrogation3', 'devoir1', 'devoir2'];

        $matieresAvecNotes = $attributions->map(function ($attr) use ($notes, $moyennes, $types) {
            $notesMatiere = $notes[$attr->matiere_id] ?? collect();
            $notesParType = $notesMatiere->keyBy('type');

            $valeursParType = [];
            foreach ($types as $type) {
                $valeursParType[$type] = isset($notesParType[$type])
                    ? (float) $notesParType[$type]->valeur
                    : null;
            }

            // Calcul en temps réel si pas encore validé en base
            $moy = $moyennes[$attr->matiere_id] ?? null;

            $moyInterro = null;
            $moyGen     = null;

            if ($moy) {
                $moyInterro = (float) $moy->moyenne_interrogations;
                $moyGen     = (float) $moy->moyenne_generale;
            } else {
                // Calcul à la volée si les notes sont là
                $i1 = $valeursParType['interrogation1'];
                $i2 = $valeursParType['interrogation2'];
                $i3 = $valeursParType['interrogation3'];
                $d1 = $valeursParType['devoir1'];
                $d2 = $valeursParType['devoir2'];

                if ($i1 !== null && $i2 !== null && $i3 !== null) {
                    $moyInterro = ($i1 + $i2 + $i3) / 3;
                    if ($d1 !== null && $d2 !== null) {
                        $moyGen = ($moyInterro + $d1 + $d2) / 3;
                    }
                }
            }

            $complet = count(array_filter($valeursParType, fn($v) => $v !== null)) === 5;

            return [
                'matiere'     => $attr->matiere->nom,
                'enseignant'  => $attr->enseignant->user->prenom . ' ' . $attr->enseignant->user->nom,
                'notes'       => $valeursParType,
                'moy_interro' => $moyInterro !== null ? round($moyInterro, 2) : null,
                'moy_gen'     => $moyGen !== null ? round($moyGen, 2) : null,
                'complet'     => $complet,
            ];
        });

        // KPIs
        $totalMatieres   = $attributions->count();
        $matieresCompletes = $matieresAvecNotes->filter(fn($m) => $m['complet'])->count();
        $notesRecues     = $notes->flatten()->count();
        $notesAttendues  = $totalMatieres * 5;
        $moyenneSemestre = $inscription
            ? MoyenneSemestre::where('inscription_id', $inscription->id)
                ->where('numero_semestre', $semestre)
                ->first()
            : null;
        $moyenneGenerale = $moyenneSemestre ? round((float) $moyenneSemestre->valeur, 2) : null;

        return view('eleve.notes', compact(
            'classe', 'anneeActive', 'semestre',
            'matieresAvecNotes', 'totalMatieres', 'matieresCompletes',
            'notesRecues', 'notesAttendues', 'moyenneGenerale'
        ));
    }
}