<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Inscription;
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
        $eleve       = $user->eleve;

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('classe.serie')
            ->first();

        $classe    = $inscription?->classe;
        $matricule = $eleve->numero_matricule ?? '—';

        return view('eleve.profil', compact('user', 'anneeActive', 'classe', 'matricule'));
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
                'current_password' => 'required',
                'password'         => ['required', 'confirmed', Password::min(8)],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect.');
            }

            $user->update(['password' => Hash::make($request->password)]);

            return back()->with('success', 'Mot de passe mis à jour avec succès.');
        }

        return back()->with('error', 'Action non reconnue.');
    }
}