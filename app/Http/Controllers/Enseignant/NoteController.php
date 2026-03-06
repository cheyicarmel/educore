<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\MoyenneMatiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = Auth::user()->enseignant;
        $semestre    = $request->input('semestre', 1);

        $classeId = $request->input('classe_id');
        if (!$classeId) {
            $premiere = Attribution::where('enseignant_id', $enseignant->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->first();
            $classeId = $premiere?->classe_id;
        }

        if (!$classeId) {
            return redirect()->route('enseignant.classes.index')
                ->with('error', 'Aucune classe assignée.');
        }

        $attribution = Attribution::where('enseignant_id', $enseignant->id)
            ->where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('matiere')
            ->firstOrFail();

        $classe  = Classe::findOrFail($classeId);
        $matiere = $attribution->matiere;

        $inscriptions = Inscription::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('eleve.user')
            ->get()
            ->sortBy(fn($i) => $i->eleve->user->nom . ' ' . $i->eleve->user->prenom)
            ->values();

        $inscriptionIds = $inscriptions->pluck('id');

        // Notes existantes
        $notesExistantes = Note::whereIn('inscription_id', $inscriptionIds)
            ->where('matiere_id', $matiere->id)
            ->where('numero_semestre', $semestre)
            ->get();

        $notesParInscription = [];
        foreach ($notesExistantes as $note) {
            $notesParInscription[$note->inscription_id][$note->type] = $note->valeur;
        }

        // Moyennes existantes en base pour affichage au chargement
        $moyennesExistantes = MoyenneMatiere::whereIn('inscription_id', $inscriptionIds)
            ->where('matiere_id', $matiere->id)
            ->where('numero_semestre', $semestre)
            ->get()
            ->keyBy('inscription_id');

        $moyennesParInscription = [];
        foreach ($moyennesExistantes as $inscriptionId => $moy) {
            $moyennesParInscription[$inscriptionId] = [
                'moyenne_interrogations' => $moy->moyenne_interrogations,
                'moyenne_generale'       => $moy->moyenne_generale,
            ];
        }

        // KPIs
        $effectif       = $inscriptions->count();
        $notesAttendues = $effectif * 5;
        $notesSaisies   = $notesExistantes->count();
        $tauxSaisie     = $notesAttendues > 0 ? round(($notesSaisies / $notesAttendues) * 100) : 0;

        $elevesIncomplets = $inscriptions->filter(function ($inscription) use ($notesParInscription) {
            return count($notesParInscription[$inscription->id] ?? []) < 5;
        })->count();

        // Moyenne de classe uniquement si toutes les notes sont saisies
        $moyenneClasse = null;
        if ($notesSaisies === $notesAttendues && $notesAttendues > 0) {
            $moyennes = [];
            foreach ($inscriptions as $inscription) {
                $notes = $notesParInscription[$inscription->id] ?? [];
                if (count($notes) === 5) {
                    $moyInterro = ($notes['interrogation1'] + $notes['interrogation2'] + $notes['interrogation3']) / 3;
                    $moyGen     = ($moyInterro + $notes['devoir1'] + $notes['devoir2']) / 3;
                    $moyennes[] = $moyGen;
                }
            }
            if (!empty($moyennes)) {
                $moyenneClasse = array_sum($moyennes) / count($moyennes);
            }
        }

        return view('enseignant.notes', compact(
            'classe', 'matiere', 'semestre', 'inscriptions',
            'notesParInscription', 'moyennesParInscription',
            'notesSaisies', 'notesAttendues',
            'tauxSaisie', 'elevesIncomplets', 'moyenneClasse', 'anneeActive'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inscription_id'  => 'required|exists:inscriptions,id',
            'matiere_id'      => 'required|exists:matieres,id',
            'numero_semestre' => 'required|in:1,2',
            'type'            => 'required|in:interrogation1,interrogation2,interrogation3,devoir1,devoir2',
            'valeur'          => 'required|numeric|min:0|max:20',
        ]);

        $enseignant  = Auth::user()->enseignant;
        $inscription = Inscription::findOrFail($request->inscription_id);
        $anneeActive = AnneeAcademique::active()->first();

        $attribution = Attribution::where('enseignant_id', $enseignant->id)
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->where('matiere_id', $request->matiere_id)
            ->first();

        if (!$attribution) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $existe = Note::where([
            'inscription_id'  => $request->inscription_id,
            'matiere_id'      => $request->matiere_id,
            'numero_semestre' => $request->numero_semestre,
            'type'            => $request->type,
        ])->exists();

        Note::updateOrCreate(
            [
                'inscription_id'  => $request->inscription_id,
                'matiere_id'      => $request->matiere_id,
                'numero_semestre' => $request->numero_semestre,
                'type'            => $request->type,
            ],
            [
                'enseignant_id' => $enseignant->id,
                'valeur'        => $request->valeur,
                'date_saisie'   => now(),
            ]
        );

        return response()->json([
            'success'    => true,
            'estNouvelle' => !$existe,
        ]);
    }

    public function validerMoyennes(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'semestre'   => 'required|in:1,2',
            'moyennes'   => 'required|array',
        ]);

        $enseignant = Auth::user()->enseignant;

        foreach ($request->moyennes as $item) {
            MoyenneMatiere::updateOrCreate(
                [
                    'inscription_id'  => $item['inscription_id'],
                    'matiere_id'      => $request->matiere_id,
                    'numero_semestre' => $request->semestre,
                ],
                [
                    'moyenne_interrogations'   => $item['moyenne_interrogations'],
                    'moyenne_generale'         => $item['moyenne_generale'],
                    'moyenne_avec_coefficient' => null, // sera calculé par le prof principal
                ]
            );
        }

        return response()->json(['success' => true]);
    }
}