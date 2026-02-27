<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Serie;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function index(Request $request)
    {
        // Année sélectionnée — par défaut l'année active
        $anneeSelectionnee = $request->input('annee_id')
            ? AnneeAcademique::findOrFail($request->input('annee_id'))
            : AnneeAcademique::active()->first();

        // Si aucune année active, prendre la plus récente
        if (!$anneeSelectionnee) {
            $anneeSelectionnee = AnneeAcademique::orderBy('date_debut', 'desc')->first();
        }

        $query = Classe::with(['serie', 'inscriptions'])
            ->where('annee_academique_id', $anneeSelectionnee?->id);

        // Filtre niveau
        if ($request->filled('niveau')) {
            $query->where('niveau', $request->input('niveau'));
        }

        // Filtre cycle
        if ($request->filled('cycle')) {
            $query->where('cycle', $request->input('cycle'));
        }

        $classes = $query->orderBy('niveau')->orderBy('nom')->get();
        $annees  = AnneeAcademique::orderBy('date_debut', 'desc')->get();
        $series  = Serie::orderBy('code')->get();

        return view('admin.classes', compact(
            'classes',
            'annees',
            'series',
            'anneeSelectionnee'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'                  => 'required|string|max:20',
            'niveau'               => 'required|string|max:20',
            'cycle'                => 'required|in:premier,second',
            'serie_id'             => 'required|exists:series,id',
            'annee_academique_id'  => 'required|exists:annees_academiques,id',
        ], [
            'nom.required'                 => 'Le nom de la classe est obligatoire.',
            'niveau.required'              => 'Le niveau est obligatoire.',
            'cycle.required'               => 'Le cycle est obligatoire.',
            'serie_id.required'            => 'La série est obligatoire.',
            'annee_academique_id.required' => 'L\'année académique est obligatoire.',
        ]);

        // Vérifier unicité nom + année
        $existe = Classe::where('nom', $request->nom)
            ->where('annee_academique_id', $request->annee_academique_id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['nom' => 'Cette classe existe déjà pour cette année académique.'])->withInput();
        }

        Classe::create($request->only('nom', 'niveau', 'cycle', 'serie_id', 'annee_academique_id'));

        return redirect()->route('admin.classes.index')
            ->with('success', 'Classe créée avec succès.');
    }

    public function update(Request $request, Classe $classe)
    {
        $request->validate([
            'nom'                 => 'required|string|max:20',
            'niveau'              => 'required|string|max:20',
            'cycle'               => 'required|in:premier,second',
            'serie_id'            => 'required|exists:series,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
        ], [
            'nom.required'                 => 'Le nom de la classe est obligatoire.',
            'niveau.required'              => 'Le niveau est obligatoire.',
            'cycle.required'               => 'Le cycle est obligatoire.',
            'serie_id.required'            => 'La série est obligatoire.',
            'annee_academique_id.required' => 'L\'année académique est obligatoire.',
        ]);

        // Vérifier unicité nom + année en excluant la classe actuelle
        $existe = Classe::where('nom', $request->nom)
            ->where('annee_academique_id', $request->annee_academique_id)
            ->where('id', '!=', $classe->id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['nom' => 'Cette classe existe déjà pour cette année académique.'])->withInput();
        }

        $classe->update($request->only('nom', 'niveau', 'cycle', 'serie_id', 'annee_academique_id'));

        return redirect()->route('admin.classes.index')
            ->with('success', 'Classe modifiée avec succès.');
    }

    public function destroy(Classe $classe)
    {
        // Bloquer si des inscriptions existent
        if ($classe->inscriptions()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Impossible de supprimer cette classe car elle contient des élèves inscrits.');
        }

        $classe->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }
}