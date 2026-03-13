<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\MoyenneAnnuelle;
use App\Models\Paiement;
use App\Models\SuiviFinancier;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();

        $totalEleves      = Inscription::where('annee_academique_id', $anneeActive?->id)->count();
        $totalEnseignants = User::where('role', 'enseignant')->where('est_actif', true)->count();
        $totalClasses     = Classe::where('annee_academique_id', $anneeActive?->id)->count();
        $retardsPaiement  = SuiviFinancier::whereHas('inscription', fn($q) =>
            $q->where('annee_academique_id', $anneeActive?->id)
        )->where('statut', 'en_retard')->count();

        // Performances par classe
        $classes = Classe::where('annee_academique_id', $anneeActive?->id)
            ->with('serie')
            ->orderBy('nom')
            ->get();

        $performancesClasses = $classes->map(function ($classe) {
            $inscriptions   = Inscription::where('classe_id', $classe->id)->get();
            $inscriptionIds = $inscriptions->pluck('id');
            $effectif       = $inscriptions->count();

            $moyennesCalculees = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)->count();
            $passants          = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)
                ->where('decision', 'passant')->count();

            $tauxReussite = $moyennesCalculees > 0
                ? round(($passants / $moyennesCalculees) * 100)
                : null;

            return [
                'nom'             => $classe->nom,
                'effectif'        => $effectif,
                'passants'        => $passants,
                'taux_reussite'   => $tauxReussite,
                'moyennes_calcs'  => $moyennesCalculees,
            ];
        })->filter(fn($c) => $c['taux_reussite'] !== null)->sortByDesc('taux_reussite')->values();

        // Derniers paiements
        $derniersPaiements = Paiement::with(['inscription.eleve.user', 'inscription.classe'])
            ->whereHas('inscription', fn($q) =>
                $q->where('annee_academique_id', $anneeActive?->id)
            )
            ->orderByDesc('date_paiement')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'anneeActive',
            'totalEleves',
            'totalEnseignants',
            'retardsPaiement',
            'totalClasses',
            'performancesClasses',
            'derniersPaiements'
        ));
    }
}