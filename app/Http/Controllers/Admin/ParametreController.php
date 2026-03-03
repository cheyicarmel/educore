<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::instance();
        return view('admin.parametres', compact('parametres'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nom_etablissement' => 'required|string|max:150',
            'slogan'            => 'nullable|string|max:200',
            'ville'             => 'required|string|max:100',
            'pays'              => 'required|string|max:100',
            'adresse'           => 'nullable|string|max:255',
            'telephone'         => 'nullable|string|max:30',
            'telephone2'        => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:150',
            'site_web'          => 'nullable|url|max:200',
            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'seuil_insuffisant' => 'required|numeric|min:0|max:20',
            'seuil_passable'    => 'required|numeric|min:0|max:20',
            'seuil_assez_bien'  => 'required|numeric|min:0|max:20',
            'seuil_bien'        => 'required|numeric|min:0|max:20',
            'seuil_tres_bien'   => 'required|numeric|min:0|max:20',
            'seuil_excellent'   => 'required|numeric|min:0|max:20',
        ], [
            'nom_etablissement.required' => 'Le nom de l\'établissement est obligatoire.',
            'ville.required'             => 'La ville est obligatoire.',
            'pays.required'              => 'Le pays est obligatoire.',
            'logo.image'                 => 'Le logo doit être une image.',
            'logo.max'                   => 'Le logo ne doit pas dépasser 2 Mo.',
            'site_web.url'               => 'Le site web doit être une URL valide (commençant par https://).',
        ]);

        $parametres = Parametre::instance();

        $data = $request->except(['_token', '_method', 'logo']);

        // Gestion upload logo
        if ($request->hasFile('logo')) {
            // Supprimer ancien logo si existant
            if ($parametres->logo && Storage::disk('public')->exists($parametres->logo)) {
                Storage::disk('public')->delete($parametres->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $parametres->update($data);

        return redirect()->route('admin.parametres.index')
            ->with('success', 'Les paramètres ont été enregistrés avec succès.');
    }
}