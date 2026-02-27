<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.matieres', compact('matieres'));
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

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }
}