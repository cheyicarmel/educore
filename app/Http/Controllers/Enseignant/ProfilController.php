<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $anneeActive = AnneeAcademique::active()->first();
        $enseignant  = $user->enseignant;

        $attributions = Attribution::where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with(['classe', 'matiere'])
            ->get();

        return view('enseignant.profil', compact('user', 'anneeActive', 'attributions'));
    }

    public function update(Request $request)
    {
        $user    = Auth::user();
        $section = $request->input('section');

        if ($section === 'infos') {
            $request->validate([
                'prenom' => 'required|string|max:100',
                'nom'    => 'required|string|max:100',
                'email'  => 'required|email|unique:users,email,' . $user->id,
            ]);

            $user->update([
                'prenom' => $request->prenom,
                'nom'    => $request->nom,
                'email'  => $request->email,
            ]);

            return back()->with('success', 'Informations mises à jour avec succès.');
        }

        if ($section === 'password') {
            $request->validate([
                'current_password'      => 'required',
                'password'              => ['required', 'confirmed', Password::min(8)],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect.');
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Mot de passe mis à jour avec succès.');
        }

        return back()->with('error', 'Action non reconnue.');
    }
}