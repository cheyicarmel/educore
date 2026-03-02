<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\SuiviFinancier;
use Illuminate\Http\Request;

class FinanceController extends Controller
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

        // IDs des inscriptions de l'année sélectionnée
        $inscriptionIds = Inscription::where('annee_academique_id', $anneeSelectionnee?->id)
            ->pluck('id');

        // KPIs globaux
        $totalAttendu  = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->sum('total_du');
        $totalEncaisse = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->sum('total_paye');
        $soldeRestant  = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->sum('solde_restant');
        $tauxRecouvrement = $totalAttendu > 0 ? round(($totalEncaisse / $totalAttendu) * 100, 1) : 0;

        // Répartition par statut
        $nbEnRetard = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->where('statut', 'en_retard')->count();
        $nbAJour    = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->where('statut', 'a_jour')->count();
        $nbSolde    = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)->where('statut', 'solde')->count();
        $totalEleves = $nbEnRetard + $nbAJour + $nbSolde;

        // Recouvrement par classe
        $classes = Classe::where('annee_academique_id', $anneeSelectionnee?->id)
            ->orderBy('nom')
            ->get()
            ->map(function ($classe) use ($anneeSelectionnee) {
                $ids = Inscription::where('classe_id', $classe->id)
                    ->where('annee_academique_id', $anneeSelectionnee->id)
                    ->pluck('id');

                $du     = SuiviFinancier::whereIn('inscription_id', $ids)->sum('total_du');
                $paye   = SuiviFinancier::whereIn('inscription_id', $ids)->sum('total_paye');
                $taux   = $du > 0 ? round(($paye / $du) * 100, 1) : 0;

                return [
                    'nom'      => $classe->nom,
                    'total_du' => $du,
                    'total_paye' => $paye,
                    'taux'     => $taux,
                ];
            })
            ->sortByDesc('taux')
            ->values();

        // Tableau élèves avec filtre
        $query = SuiviFinancier::whereIn('inscription_id', $inscriptionIds)
            ->with([
                'inscription.eleve.user',
                'inscription.classe',
            ]);

        if ($request->filled('classe_id')) {
            $query->whereHas('inscription', fn($q) => $q->where('classe_id', $request->classe_id));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $suivis = $query->paginate(20)->appends(request()->query());

        $classesFiltre = Classe::where('annee_academique_id', $anneeSelectionnee?->id)->orderBy('nom')->get();
        $annees        = AnneeAcademique::where('statut', '!=', 'a_venir')->orderBy('date_debut', 'desc')->get();

        return view('admin.finances', compact(
            'anneeSelectionnee', 'annees',
            'totalAttendu', 'totalEncaisse', 'soldeRestant', 'tauxRecouvrement',
            'nbEnRetard', 'nbAJour', 'nbSolde', 'totalEleves',
            'classes', 'classesFiltre', 'suivis'
        ));
    }
}