<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\SuiviFinancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    public function create(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();
        $eleve       = null;
        $inscription = null;
        $suivi       = null;

        if ($request->filled('eleve_id')) {
            $inscription = Inscription::where('eleve_id', $request->eleve_id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->with(['eleve.user', 'classe.serie'])
                ->first();

            if ($inscription) {
                $eleve = $inscription->eleve;
                $suivi = SuiviFinancier::where('inscription_id', $inscription->id)->first();
            }
        }

        return view('comptable.paiements.create', compact('anneeActive', 'eleve', 'inscription', 'suivi'));
    }

    public function search(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();
        $query       = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $inscriptions = Inscription::where('annee_academique_id', $anneeActive?->id)
            ->whereHas('eleve.user', function ($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%")
                  ->orWhere('prenom', 'like', "%{$query}%");
            })
            ->with(['eleve.user', 'classe'])
            ->limit(8)
            ->get();

        return response()->json($inscriptions->map(fn($i) => [
            'eleve_id' => $i->eleve_id,
            'nom'      => $i->eleve->user->prenom . ' ' . $i->eleve->user->nom,
            'classe'   => $i->classe->nom,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'montant'        => 'required|numeric|min:1',
            'mode_paiement'  => 'required|string',
            'date_paiement'  => 'required|date',
            'reference'      => 'nullable|string|max:100',
        ]);

        $anneeActive = AnneeAcademique::active()->first();

        // Créer le paiement

        $reference = $request->reference;
        if (empty($reference)) {
            $count     = Paiement::whereDate('created_at', today())->count() + 1;
            $reference = 'PMT-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        Paiement::create([
            'inscription_id' => $request->inscription_id,
            'comptable_id'   => Auth::user()->comptable->id,
            'montant'        => $request->montant,
            'mode_paiement'  => $request->mode_paiement,
            'date_paiement'  => $request->date_paiement,
            'reference'      => $request->reference,
        ]);

        // Mettre à jour le suivi financier
        $suivi = SuiviFinancier::where('inscription_id', $request->inscription_id)->first();
        if ($suivi) {
            $nouveauPaye   = $suivi->total_paye + $request->montant;
            $nouveauSolde  = $suivi->total_du - $nouveauPaye;
            $nouveauSolde  = max(0, $nouveauSolde);

            $statut = 'en_retard';
            if ($nouveauSolde <= 0) {
                $statut = 'a_jour';
            } elseif ($nouveauPaye > 0) {
                $statut = 'partiel';
            }

            $suivi->update([
                'total_paye'    => $nouveauPaye,
                'solde_restant' => $nouveauSolde,
                'statut'        => $statut,
            ]);
        }

        return redirect()->route('comptable.paiements.create')
            ->with('success', 'Paiement enregistré avec succès.');
    }
}