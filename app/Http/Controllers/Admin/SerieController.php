<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Serie;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    public function index()
    {
        $series = Serie::orderBy('code')->get();
        return view('admin.series', compact('series'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:10|unique:series,code',
            'libelle'     => 'required|string|max:50|unique:series,libelle',
            'type'        => 'required|in:litteraire,scientifique',
            'description' => 'nullable|string|max:150',
        ], [
            'code.required'    => 'Le code est obligatoire.',
            'code.unique'      => 'Ce code existe déjà.',
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique'   => 'Ce libellé existe déjà.',
            'type.required'    => 'Le type est obligatoire.',
            'type.in'          => 'Le type doit être littéraire ou scientifique.',
        ]);

        Serie::create($request->only('code', 'libelle', 'type', 'description'));

        return redirect()->route('admin.series.index')
            ->with('success', 'Série créée avec succès.');
    }

    public function update(Request $request, Serie $serie)
    {
        $request->validate([
            'code'        => 'required|string|max:10|unique:series,code,' . $serie->id,
            'libelle'     => 'required|string|max:50|unique:series,libelle,' . $serie->id,
            'type'        => 'required|in:litteraire,scientifique',
            'description' => 'nullable|string|max:150',
        ], [
            'code.required'    => 'Le code est obligatoire.',
            'code.unique'      => 'Ce code existe déjà.',
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique'   => 'Ce libellé existe déjà.',
            'type.required'    => 'Le type est obligatoire.',
            'type.in'          => 'Le type doit être littéraire ou scientifique.',
        ]);

        $serie->update($request->only('code', 'libelle', 'type', 'description'));

        return redirect()->route('admin.series.index')
            ->with('success', 'Série modifiée avec succès.');
    }

    public function destroy(Serie $serie)
    {
        $serie->delete();

        return redirect()->route('admin.series.index')
            ->with('success', 'Série supprimée avec succès.');
    }
}