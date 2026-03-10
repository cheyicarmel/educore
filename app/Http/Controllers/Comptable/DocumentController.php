<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Paiement;
use App\Models\SuiviFinancier;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();

        return view('comptable.documents', compact('anneeActive'));
    }

    public function rapportGlobal()
    {
        $anneeActive = AnneeAcademique::active()->first();

        $totalDu          = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('total_du');
        $totalPaye        = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('total_paye');
        $totalSolde       = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('solde_restant');
        $tauxRecouvrement = $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0;

        $elevesAJour  = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->where('statut', 'solde')->count();
        $elevesRetard = SuiviFinancier::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->where('statut', 'en_retard')->count();
        $totalEleves  = $elevesAJour + $elevesRetard;

        $parClasse = Classe::withCount(['inscriptions as nb_eleves' => fn($q) => $q->where('annee_academique_id', $anneeActive?->id)])
            ->whereHas('inscriptions', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))
            ->with(['inscriptions' => fn($q) => $q->where('annee_academique_id', $anneeActive?->id)->with('suiviFinancier')])
            ->orderBy('nom')
            ->get()
            ->map(function ($classe) {
                $suivis = $classe->inscriptions->map(fn($i) => $i->suiviFinancier)->filter();
                return (object) [
                    'nom'        => $classe->nom,
                    'nb_eleves'  => $classe->nb_eleves,
                    'total_du'   => $suivis->sum('total_du'),
                    'total_paye' => $suivis->sum('total_paye'),
                    'solde'      => $suivis->sum('solde_restant'),
                    'taux'       => $suivis->sum('total_du') > 0
                                    ? round(($suivis->sum('total_paye') / $suivis->sum('total_du')) * 100, 1)
                                    : 0,
                ];
            });

        $nombrePaiements = Paiement::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('comptable.pdf.rapport-global', compact(
            'anneeActive', 'totalDu', 'totalPaye', 'totalSolde', 'tauxRecouvrement',
            'elevesAJour', 'elevesRetard', 'totalEleves', 'parClasse', 'nombrePaiements'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('rapport-financier-' . now()->format('Y-m-d') . '.pdf');
    }
}