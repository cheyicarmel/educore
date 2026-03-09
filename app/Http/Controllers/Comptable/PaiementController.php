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

        // Générer la référence automatiquement
        $reference = $request->reference;
        if (empty($reference)) {
            $count     = Paiement::whereDate('created_at', today())->count() + 1;
            $reference = 'PMT-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        // Créer le paiement et l'assigner à $paiement
        $paiement = Paiement::create([
            'inscription_id' => $request->inscription_id,
            'comptable_id'   => Auth::user()->comptable->id,
            'montant'        => $request->montant,
            'mode_paiement'  => $request->mode_paiement,
            'date_paiement'  => $request->date_paiement,
            'reference'      => $reference, // <-- $reference et non $request->reference
        ]);

        // Mettre à jour le suivi financier
        $suivi = SuiviFinancier::where('inscription_id', $request->inscription_id)->first();
        if ($suivi) {
            $nouveauPaye  = $suivi->total_paye + $request->montant;
            $nouveauSolde = max(0, $suivi->total_du - $nouveauPaye);

            $statut = $nouveauSolde <= 0 ? 'solde' : 'en_retard';

            $suivi->update([
                'total_paye'    => $nouveauPaye,
                'solde_restant' => $nouveauSolde,
                'statut'        => $statut,
            ]);
        }

        return redirect()->route('comptable.paiements.create')
            ->with('success', 'Paiement enregistré avec succès.')
            ->with('paiement_id', $paiement->id);
    }

    public function telechargerRecu(Paiement $paiement)
    {
        $paiement->load(['inscription.eleve.user', 'inscription.classe.serie']);
        $inscription = $paiement->inscription;
        $eleve       = $inscription->eleve;
        $suivi       = SuiviFinancier::where('inscription_id', $inscription->id)->first();
        $comptable   = \App\Models\User::find($paiement->comptable->user_id);
        $anneeActive = AnneeAcademique::active()->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('comptable.recu-pdf', compact(
            'paiement', 'inscription', 'eleve', 'suivi', 'comptable', 'anneeActive'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('recu-' . $paiement->reference . '.pdf');
    }


    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();

        $query = Paiement::with(['inscription.eleve.user', 'inscription.classe'])
            ->whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inscription.eleve.user', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('classe')) {
            $query->whereHas('inscription', fn($q) => $q->where('classe_id', $request->classe));
        }

        $paiements    = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $totalGeneral = Paiement::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->sum('montant');
        $nombreTotal  = Paiement::whereHas('inscription', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))->count();

        $classes = \App\Models\Classe::whereHas('inscriptions', fn($q) => $q->where('annee_academique_id', $anneeActive?->id))
            ->orderBy('nom')
            ->get();

        return view('comptable.paiements.index', compact(
            'anneeActive', 'paiements', 'totalGeneral', 'nombreTotal', 'classes'
        ));
    }
}