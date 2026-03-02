<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributionController extends Controller
{
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();

        $anneeSelectionnee = $request->input('annee_id')
            ? AnneeAcademique::findOrFail($request->input('annee_id'))
            : $anneeActive;

        if (!$anneeSelectionnee) {
            $anneeSelectionnee = AnneeAcademique::orderBy('date_debut', 'desc')->first();
        }

        $enseignants = Enseignant::with([
            'user',
            'attributions' => function ($q) use ($anneeSelectionnee) {
                $q->where('annee_academique_id', $anneeSelectionnee?->id)
                  ->with(['matiere', 'classe']);
            }
        ])
        ->whereHas('user', fn($q) => $q->where('est_actif', true))
        ->get()
        ->sortBy(fn($e) => $e->user->nom . ' ' . $e->user->prenom)
        ->values();

        // Pagination manuelle

        $page = request()->get('page', 1);
        $perPage = 10;
        $enseignants = new \Illuminate\Pagination\LengthAwarePaginator(
            $enseignants->forPage($page, $perPage),
            $enseignants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $annees   = AnneeAcademique::where('statut', '!=', 'a_venir')->orderBy('date_debut', 'desc')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $classes  = Classe::where('annee_academique_id', $anneeSelectionnee?->id)->orderBy('nom')->get();

        return view('admin.attributions', compact('enseignants', 'annees', 'matieres', 'classes', 'anneeSelectionnee'));
    }

    public function store(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();

        if (!$anneeActive) {
            return back()->with('error', 'Aucune année académique active.');
        }

        $request->validate([
            'enseignant_id'    => 'required|exists:enseignants,id',
            'matiere_id'       => 'required|exists:matieres,id',
            'classe_id'        => 'required|exists:classes,id',
            'date_attribution' => 'required|date',
        ], [
            'enseignant_id.required'    => 'L\'enseignant est obligatoire.',
            'matiere_id.required'       => 'La matière est obligatoire.',
            'classe_id.required'        => 'La classe est obligatoire.',
            'date_attribution.required' => 'La date d\'attribution est obligatoire.',
        ]);

        $enseignant = Enseignant::with(['user', 'attributions'])->findOrFail($request->enseignant_id);
        $matiere    = Matiere::findOrFail($request->matiere_id);
        $classe     = Classe::findOrFail($request->classe_id);

        // Règle 1 — Enseignant inactif
        if (!$enseignant->user->est_actif) {
            return back()->with('error', 'Impossible d\'assigner un enseignant inactif.');
        }

        // Règle 2 — Un enseignant = une seule matière
        // Vérifier si l'enseignant a déjà des attributions avec une matière différente
        $matiereExistante = Attribution::where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where('matiere_id', '!=', $request->matiere_id)
            ->first();

        if ($matiereExistante) {
            $nomMatiereExistante = $matiereExistante->matiere->nom ?? 'une autre matière';
            return back()->with('error',
                "{$enseignant->user->prenom} {$enseignant->user->nom} enseigne déjà \"{$nomMatiereExistante}\". Un enseignant ne peut enseigner qu'une seule matière."
            );
        }

        // Règle 3 — Même enseignant déjà assigné à cette classe
        $dejaAssigneClasse = Attribution::where('enseignant_id', $enseignant->id)
            ->where('classe_id', $request->classe_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->exists();

        if ($dejaAssigneClasse) {
            return back()->with('error',
                "{$enseignant->user->prenom} {$enseignant->user->nom} est déjà assigné à la classe \"{$classe->nom}\" pour cette année."
            );
        }

        // Règle 4 — Matière déjà enseignée dans cette classe par quelqu'un d'autre
        $matiereDejaEnClasse = Attribution::where('matiere_id', $request->matiere_id)
            ->where('classe_id', $request->classe_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->with('enseignant.user')
            ->first();

        if ($matiereDejaEnClasse) {
            $autreProf = $matiereDejaEnClasse->enseignant->user;
            return back()->with('error',
                "\"{$matiere->nom}\" est déjà enseignée en \"{$classe->nom}\" par {$autreProf->prenom} {$autreProf->nom}."
            );
        }

        $estProfPrincipal = $request->boolean('est_prof_principal');

        if ($estProfPrincipal) {
            // Règle 5 — Cette classe a déjà un prof principal
            $classeDejaProfPrincipal = Attribution::where('classe_id', $request->classe_id)
                ->where('annee_academique_id', $anneeActive->id)
                ->where('est_prof_principal', true)
                ->with('enseignant.user')
                ->first();

            if ($classeDejaProfPrincipal) {
                $profActuel = $classeDejaProfPrincipal->enseignant->user;
                return back()->with('error',
                    "La classe \"{$classe->nom}\" a déjà un professeur principal : {$profActuel->prenom} {$profActuel->nom}. Retirez-le d'abord avant d'en désigner un nouveau."
                );
            }

            // Règle 6 — Cet enseignant est déjà prof principal dans une autre classe
            $dejaProfPrincipalAilleurs = Attribution::where('enseignant_id', $enseignant->id)
                ->where('annee_academique_id', $anneeActive->id)
                ->where('est_prof_principal', true)
                ->with('classe')
                ->first();

            if ($dejaProfPrincipalAilleurs) {
                $autreClasse = $dejaProfPrincipalAilleurs->classe;
                return back()->with('error',
                    "{$enseignant->user->prenom} {$enseignant->user->nom} est déjà professeur principal de la classe \"{$autreClasse->nom}\". Un enseignant ne peut être professeur principal que dans une seule classe."
                );
            }
        }

        DB::transaction(function () use ($request, $anneeActive, $enseignant, $matiere, $classe, $estProfPrincipal) {
            if ($estProfPrincipal) {
                Classe::where('id', $request->classe_id)
                    ->update(['prof_principal_id' => $enseignant->id]);
            }

            Attribution::create([
                'enseignant_id'       => $enseignant->id,
                'matiere_id'          => $request->matiere_id,
                'classe_id'           => $request->classe_id,
                'annee_academique_id' => $anneeActive->id,
                'est_prof_principal'  => $estProfPrincipal,
                'date_attribution'    => $request->date_attribution,
            ]);
        });

        $messageSucces = "{$enseignant->user->prenom} {$enseignant->user->nom} ({$matiere->nom}) a été assigné à la classe \"{$classe->nom}\" pour l'année {$anneeActive->libelle}.";
        if ($estProfPrincipal) {
            $messageSucces .= " Désigné comme professeur principal.";
        }

        return redirect()->route('admin.attributions.index')
            ->with('success', $messageSucces);
    }

    public function destroy(Attribution $attribution)
    {
        $enseignant = $attribution->enseignant->user;
        $matiere    = $attribution->matiere->nom;
        $classe     = $attribution->classe->nom;

        if ($attribution->est_prof_principal) {
            Classe::where('id', $attribution->classe_id)
                ->update(['prof_principal_id' => null]);
        }

        $attribution->delete();

        return redirect()->route('admin.attributions.index')
            ->with('success', "{$enseignant->prenom} {$enseignant->nom} a été retiré de \"{$matiere}\" en \"{$classe}\".");
    }
}