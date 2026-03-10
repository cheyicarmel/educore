<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('comptable.profil', compact('user'));
    }

    public function updateInfos(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'prenom'    => 'required|string|max:100',
            'nom'       => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'prenom' => $request->prenom,
            'nom'    => $request->nom,
        ]);

        if ($user->comptable) {
            $user->comptable->update(['telephone' => $request->telephone]);
        }

        return back()->with('success_infos', 'Informations mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => 'required',
            'password'                  => 'required|min:8|confirmed',
            'password_confirmation'     => 'required',
        ], [
            'current_password.required'      => 'Le mot de passe actuel est obligatoire.',
            'password.required'              => 'Le nouveau mot de passe est obligatoire.',
            'password.min'                   => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'             => 'La confirmation ne correspond pas.',
            'password_confirmation.required' => 'La confirmation est obligatoire.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.'])->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success_password', 'Mot de passe modifié avec succès.')->with('tab', 'password');
    }
}