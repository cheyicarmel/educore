<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\SuiviFinancier;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index()
    {
        $anneeActive = AnneeAcademique::active()->first();

        // KPIs disponibles
        $totalEleves = Inscription::where('annee_academique_id', $anneeActive?->id)->count();

        $totalEnseignants = User::where('role', 'enseignant')->where('est_actif', true)->count();

        $retardsPaiement = SuiviFinancier::whereHas('inscription', fn($q) =>
            $q->where('annee_academique_id', $anneeActive?->id)
        )->where('statut', 'en_retard')->count();

        $totalClasses = Classe::where('annee_academique_id', $anneeActive?->id)->count();

        return view('admin.dashboard', compact(
            'anneeActive',
            'totalEleves',
            'totalEnseignants',
            'retardsPaiement',
            'totalClasses'
        ));
    }
}
