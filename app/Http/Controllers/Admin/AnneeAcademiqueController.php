<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class AnneeAcademiqueController extends Controller
{
    // Afficher la liste
    public function index()
    {
        $annees = AnneeAcademique::orderBy('date_debut', 'desc')->get();
        return view('admin.annees', compact('annees'));
    }

    // Créer une nouvelle année
    public function store(Request $request)
    {
        $request->validate([
            'libelle'    => 'required|string|max:20|unique:annees_academiques,libelle',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ], [
            'libelle.required'    => 'Le libellé est obligatoire.',
            'libelle.unique'      => 'Cette année académique existe déjà.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required'   => 'La date de fin est obligatoire.',
            'date_fin.after'      => 'La date de fin doit être après la date de début.',
        ]);

        AnneeAcademique::create([
            'libelle'    => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'est_active' => false,
        ]);

        return redirect()->route('admin.annees.index')
            ->with('success', 'Année académique créée avec succès.');
    }

    // Modifier une année
    public function update(Request $request, AnneeAcademique $anneeAcademique)
    {
        $request->validate([
            'libelle'    => 'required|string|max:20|unique:annees_academiques,libelle,' . $anneeAcademique->id,
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ], [
            'libelle.required'    => 'Le libellé est obligatoire.',
            'libelle.unique'      => 'Cette année académique existe déjà.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required'   => 'La date de fin est obligatoire.',
            'date_fin.after'      => 'La date de fin doit être après la date de début.',
        ]);

        $anneeAcademique->update([
            'libelle'    => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
        ]);

        return redirect()->route('admin.annees.index')
            ->with('success', 'Année académique modifiée avec succès.');
    }

    // Changer le statut (activer / désactiver)
    public function toggleStatus(AnneeAcademique $anneeAcademique)
    {
        if ($anneeAcademique->est_active) {
            // Désactiver simplement
            $anneeAcademique->update(['est_active' => false]);
            $message = 'Année académique désactivée.';
        } else {
            // Désactiver toutes les autres d'abord
            AnneeAcademique::where('est_active', true)
                ->update(['est_active' => false]);
            // Activer celle-ci
            $anneeAcademique->update(['est_active' => true]);
            $message = 'Année académique activée avec succès.';
        }

        return redirect()->route('admin.annees.index')
            ->with('success', $message);
    }

    // Supprimer une année
    public function destroy(AnneeAcademique $anneeAcademique)
    {
        if ($anneeAcademique->est_active) {
            return redirect()->route('admin.annees.index')
                ->with('error', 'Impossible de supprimer l\'année active. Désactivez-la d\'abord.');
        }

        $anneeAcademique->delete();

        return redirect()->route('admin.annees.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }
}