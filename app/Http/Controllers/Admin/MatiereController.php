<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\CoefficientMatiere;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres    = Matiere::orderBy('nom')->get();
        $anneeActive = AnneeAcademique::active()->first();

        // Classes par matière — uniquement celles où la matière est attribuée
        $classesByMatiere = Attribution::where('annee_academique_id', $anneeActive?->id)
            ->with('classe')
            ->get()
            ->groupBy('matiere_id')
            ->map(fn($items) => $items->pluck('classe')->unique('id')->values());

        // Coefficients existants indexés par [matiere_id][classe_id]
        $coefficients = CoefficientMatiere::all()
            ->groupBy('matiere_id')
            ->map(fn($items) => $items->keyBy('classe_id'));

        return view('admin.matieres', compact('matieres', 'classesByMatiere', 'coefficients', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:matieres,nom',
            'categorie'   => 'required|in:litteraire,scientifique',
            'sous_groupe' => 'required|in:maths_physique,svt,litteraire,autre',
        ], [
            'nom.required'         => 'Le nom est obligatoire.',
            'nom.unique'           => 'Cette matière existe déjà.',
            'categorie.required'   => 'La catégorie est obligatoire.',
            'sous_groupe.required' => 'Le sous-groupe est obligatoire.',
        ]);

        Matiere::create([
            'nom'              => $request->nom,
            'est_litteraire'   => $request->categorie === 'litteraire',
            'est_scientifique' => $request->categorie === 'scientifique',
            'sous_groupe'      => $request->sous_groupe,
        ]);

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    public function update(Request $request, Matiere $matiere)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:matieres,nom,' . $matiere->id,
            'categorie'   => 'required|in:litteraire,scientifique',
            'sous_groupe' => 'required|in:maths_physique,svt,litteraire,autre',
        ], [
            'nom.required'         => 'Le nom est obligatoire.',
            'nom.unique'           => 'Cette matière existe déjà.',
            'categorie.required'   => 'La catégorie est obligatoire.',
            'sous_groupe.required' => 'Le sous-groupe est obligatoire.',
        ]);

        $matiere->update([
            'nom'              => $request->nom,
            'est_litteraire'   => $request->categorie === 'litteraire',
            'est_scientifique' => $request->categorie === 'scientifique',
            'sous_groupe'      => $request->sous_groupe,
        ]);

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière modifiée avec succès.');
    }


    public function updateCoefficients(Request $request, Matiere $matiere)
    {
        $request->validate([
            'coefficients'          => 'required|array',
            'coefficients.*'        => 'required|integer|min:1|max:10',
        ], [
            'coefficients.*.required' => 'Le coefficient est obligatoire.',
            'coefficients.*.integer'  => 'Le coefficient doit être un nombre entier.',
            'coefficients.*.min'      => 'Le coefficient minimum est 1.',
            'coefficients.*.max'      => 'Le coefficient maximum est 10.',
        ]);

        foreach ($request->coefficients as $classeId => $coefficient) {
            CoefficientMatiere::updateOrCreate(
                ['matiere_id' => $matiere->id, 'classe_id' => $classeId],
                ['coefficient' => $coefficient]
            );
        }

        return redirect()->route('admin.matieres.index')
            ->with('success', "Coefficients de \"{$matiere->nom}\" mis à jour avec succès.");
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }
}