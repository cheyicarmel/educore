<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class AnneeAcademiqueController extends Controller
{
    public function index()
    {
        $annees = AnneeAcademique::orderBy('date_debut', 'desc')->get();
        return view('admin.annees', compact('annees'));
    }

    public function store(Request $request)
    {
        $anneeActive = AnneeAcademique::active()->first();
        $totalAnnees = AnneeAcademique::count();

        // S'il y a des années en base mais aucune active, bloquer
        if ($totalAnnees > 0 && !$anneeActive) {
            return back()->with('error', 'Aucune année académique active. Veuillez d\'abord activer une année existante avant d\'en ajouter une nouvelle.');
        }

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

        // Si une année active existe, vérifier que la nouvelle est postérieure
        if ($anneeActive) {
            if ($request->date_debut <= $anneeActive->date_debut->format('Y-m-d')) {
                return back()->withErrors([
                    'date_debut' => 'La date de début doit être postérieure à celle de l\'année active (' . $anneeActive->libelle . ').'
                ])->withInput();
            }
        }

        AnneeAcademique::create([
            'libelle'    => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'statut'     => 'a_venir',
        ]);

        return redirect()->route('admin.annees.index')
            ->with('success', 'Année académique créée avec succès.');
    }

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

    public function toggleStatus(AnneeAcademique $anneeAcademique)
    {
        $anneeActive = AnneeAcademique::active()->first();

        // Cas 1 — Marquer l'année active comme terminée
        if ($anneeAcademique->estActive()) {
            $anneeAcademique->update(['statut' => 'terminee']);
            return redirect()->route('admin.annees.index')
                ->with('success', 'Année académique marquée comme terminée.');
        }

        // Cas 2 — Activer une année à venir
        if ($anneeAcademique->estAVenir()) {
            // Vérifier qu'il n'y a plus d'année active
            if ($anneeActive) {
                return redirect()->route('admin.annees.index')
                    ->with('error', 'Une année est déjà active. Terminez-la d\'abord avant d\'en activer une nouvelle.');
            }

            // Vérifier que cette année n'est pas antérieure à une année terminée récente
            $derniereTerminee = AnneeAcademique::terminee()
                ->orderBy('date_debut', 'desc')
                ->first();

            if ($derniereTerminee && $anneeAcademique->date_debut <= $derniereTerminee->date_debut) {
                return redirect()->route('admin.annees.index')
                    ->with('error', 'Cette année ne peut pas être activée car elle est antérieure ou égale à une année déjà terminée.');
            }

            $anneeAcademique->update(['statut' => 'active']);
            return redirect()->route('admin.annees.index')
                ->with('success', 'Année académique activée avec succès.');
        }

        // Cas 3 — Année terminée, on ne peut plus rien faire
        return redirect()->route('admin.annees.index')
            ->with('error', 'Une année terminée ne peut pas changer de statut.');
    }

    public function destroy(AnneeAcademique $anneeAcademique)
    {
        if ($anneeAcademique->estActive()) {
            return redirect()->route('admin.annees.index')
                ->with('error', 'Impossible de supprimer l\'année active. Terminez-la d\'abord.');
        }

        $anneeAcademique->delete();

        return redirect()->route('admin.annees.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }
}