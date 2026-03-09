<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Paiement;
use App\Models\SuiviFinancier;
use App\Models\Inscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();

        // KPIs principaux
        $totalDu         = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('total_du');
        $totalPaye       = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('total_paye');
        $totalSolde      = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('solde_restant');
        $tauxRecouvrement = $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0;

        $paiementsAujourdhui = Paiement::whereDate('date_paiement', today())->count();
        $encaisseAujourdhui  = Paiement::whereDate('date_paiement', today())->sum('montant');

        $encaisseMois = Paiement::whereYear('date_paiement', now()->year)
            ->whereMonth('date_paiement', now()->month)->sum('montant');

        // Statuts élèves
        $elevesAJour   = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->where('statut', 'a_jour')->count();
        $elevesPartiel = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->where('statut', 'partiel')->count();
        $elevesRetard  = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->where('statut', 'en_retard')->count();
        $totalEleves   = $elevesAJour + $elevesPartiel + $elevesRetard;

        // Encaissements mensuels (12 derniers mois)
        $encaissementsParMois = Paiement::select(
                DB::raw('YEAR(date_paiement) as annee'),
                DB::raw('MONTH(date_paiement) as mois'),
                DB::raw('SUM(montant) as total')
            )
            ->where('date_paiement', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('annee', 'mois')
            ->orderBy('annee')->orderBy('mois')
            ->get();

        $moisLabels = [];
        $moisData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->translatedFormat('M');
            $found = $encaissementsParMois->first(fn($r) => $r->annee == $date->year && $r->mois == $date->month);
            $moisLabels[] = $label;
            $moisData[]   = $found ? (float) $found->total : 0;
        }

        // Top classes par encaissement
        $topClasses = Paiement::select('classes.nom as classe', DB::raw('SUM(paiements.montant) as total'))
            ->join('inscriptions', 'paiements.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->where('inscriptions.annee_academique_id', $anneeActive?->id)
            ->groupBy('classes.nom')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Répartition par mode de paiement
        $parMode = Paiement::select('mode_paiement', DB::raw('COUNT(*) as nb'), DB::raw('SUM(montant) as total'))
            ->whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))
            ->groupBy('mode_paiement')
            ->get();

        // Derniers retards
        $retards = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))
            ->where('statut', 'en_retard')
            ->with(['inscription.eleve.user', 'inscription.classe'])
            ->orderByDesc('solde_restant')
            ->limit(5)
            ->get();

        // Derniers paiements
        $derniersPaiements = Paiement::with(['inscription.eleve.user', 'inscription.classe'])
            ->whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))
            ->orderByDesc('date_paiement')
            ->limit(8)
            ->get();

        return view('comptable.dashboard', compact(
            'anneeActive',
            'totalDu', 'totalPaye', 'totalSolde', 'tauxRecouvrement',
            'paiementsAujourdhui', 'encaisseAujourdhui', 'encaisseMois',
            'elevesAJour', 'elevesPartiel', 'elevesRetard', 'totalEleves',
            'moisLabels', 'moisData',
            'topClasses', 'parMode',
            'retards', 'derniersPaiements'
        ));
    }
}