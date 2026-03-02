<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\SuiviFinancier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EleveController extends Controller
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

        $query = Eleve::with([
            'user',
            'inscriptions' => function ($q) use ($anneeSelectionnee) {
                $q->where('annee_academique_id', $anneeSelectionnee?->id)->with('classe');
            }
        ]);

        // Filtrer par classe
        if ($request->filled('classe_id')) {
            $query->whereHas('inscriptions', function ($q) use ($request, $anneeSelectionnee) {
                $q->where('annee_academique_id', $anneeSelectionnee?->id)
                  ->where('classe_id', $request->input('classe_id'));
            });
        }

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->whereHas('inscriptions', function ($q) use ($request, $anneeSelectionnee) {
                $q->where('annee_academique_id', $anneeSelectionnee?->id)
                  ->where('statut', $request->input('statut'));
            });
        }

        // Si pas de filtre, on affiche les élèves qui ont une inscription cette année
        if (!$request->filled('classe_id') && !$request->filled('statut')) {
            $query->whereHas('inscriptions', function ($q) use ($anneeSelectionnee) {
                $q->where('annee_academique_id', $anneeSelectionnee?->id);
            });
        }

        $eleves = $query->get()
            ->sortBy(fn($e) => $e->user->nom . ' ' . $e->user->prenom)
            ->values();

        // Pagination manuelle
        $page = request()->get('page', 1);
        $perPage = 20;
        $eleves = new \Illuminate\Pagination\LengthAwarePaginator(
            $eleves->forPage($page, $perPage),
            $eleves->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $annees  = AnneeAcademique::where('statut', '!=', 'a_venir')->orderBy('date_debut', 'desc')->get();
        $classes = Classe::where('annee_academique_id', $anneeSelectionnee?->id)->orderBy('nom')->get();

        return view('admin.eleves', compact('eleves', 'annees', 'classes', 'anneeSelectionnee'));
    }

    public function store(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();

        if (!$anneeActive) {
            return back()->with('error', 'Aucune année académique active. Impossible d\'inscrire un élève.');
        }

        $request->validate([
            'nom'              => 'required|string|max:100',
            'prenom'           => 'required|string|max:100',
            'sexe'             => 'required|in:M,F',
            'date_naissance'   => 'nullable|date',
            'classe_id'        => 'required|exists:classes,id',
            'frais_annuels'    => 'required|numeric|min:0',
            'email_parent'     => 'required|email|max:150',
            'telephone_parent' => 'nullable|string|max:20',
        ], [
            'nom.required'           => 'Le nom est obligatoire.',
            'prenom.required'        => 'Le prénom est obligatoire.',
            'sexe.required'          => 'Le sexe est obligatoire.',
            'classe_id.required'     => 'La classe est obligatoire.',
            'frais_annuels.required' => 'Les frais annuels sont obligatoires.',
            'email_parent.required'  => 'L\'email du parent est obligatoire.',
        ]);

        $motDePasse      = Str::random(10);
        $numeroMatricule = Eleve::genererMatricule();

        DB::transaction(function () use ($request, $anneeActive, $motDePasse, $numeroMatricule) {
            // Créer le compte utilisateur
            $user = User::create([
                'nom'       => $request->nom,
                'prenom'    => $request->prenom,
                'email'     => $request->email_parent,
                'password'  => Hash::make($motDePasse),
                'role'      => 'eleve',
                'est_actif' => true,
            ]);

            // Créer le profil élève
            $eleve = Eleve::create([
                'user_id'          => $user->id,
                'numero_matricule' => $numeroMatricule,
                'date_naissance'   => $request->date_naissance,
                'sexe'             => $request->sexe,
                'email_parent'     => $request->email_parent,
                'telephone_parent' => $request->telephone_parent,
            ]);

            // Créer l'inscription
            $inscription = Inscription::create([
                'eleve_id'            => $eleve->id,
                'classe_id'           => $request->classe_id,
                'annee_academique_id' => $anneeActive->id,
                'statut'              => 'actif',
                'frais_annuels'       => $request->frais_annuels,
            ]);

            // Créer le suivi financier automatiquement
            SuiviFinancier::create([
                'inscription_id' => $inscription->id,
                'total_du'       => $request->frais_annuels,
                'total_paye'     => 0,
                'solde_restant'  => $request->frais_annuels,
                'statut'         => 'en_retard',
            ]);

            // Envoyer les identifiants par email au parent
            Mail::raw(
                "Bonjour,\n\n" .
                "Le compte EduCore de votre enfant {$request->prenom} {$request->nom} a été créé.\n\n" .
                "Matricule : {$numeroMatricule}\n" .
                "Email de connexion : {$request->email_parent}\n" .
                "Mot de passe : {$motDePasse}\n\n" .
                "Veuillez vous connecter sur la plateforme pour suivre la scolarité de votre enfant.\n\n" .
                "Cordialement,\nL'équipe EduCore",
                function ($message) use ($request) {
                    $message->to($request->email_parent)
                            ->subject('Compte EduCore — ' . $request->prenom . ' ' . $request->nom);
                }
            );
        });

        return redirect()->route('admin.eleves.index')
            ->with('success', 'Élève inscrit avec succès. Les identifiants ont été envoyés au parent.');
    }

    public function update(Request $request, Eleve $eleve)
    {
        $request->validate([
            'nom'              => 'required|string|max:100',
            'prenom'           => 'required|string|max:100',
            'sexe'             => 'required|in:M,F',
            'date_naissance'   => 'nullable|date',
            'email_parent'     => 'required|email|max:150',
            'telephone_parent' => 'nullable|string|max:20',
        ], [
            'nom.required'          => 'Le nom est obligatoire.',
            'prenom.required'       => 'Le prénom est obligatoire.',
            'sexe.required'         => 'Le sexe est obligatoire.',
            'email_parent.required' => 'L\'email du parent est obligatoire.',
        ]);

        DB::transaction(function () use ($request, $eleve) {
            $eleve->user->update([
                'nom'    => $request->nom,
                'prenom' => $request->prenom,
                'email'  => $request->email_parent,
            ]);

            $eleve->update([
                'date_naissance'   => $request->date_naissance,
                'sexe'             => $request->sexe,
                'email_parent'     => $request->email_parent,
                'telephone_parent' => $request->telephone_parent,
            ]);
        });

        return redirect()->route('admin.eleves.index')
            ->with('success', 'Élève modifié avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        DB::transaction(function () use ($eleve) {
            $user = $eleve->user;
            $eleve->delete();
            $user->delete();
        });

        return redirect()->route('admin.eleves.index')
            ->with('success', 'Élève supprimé avec succès.');
    }
}