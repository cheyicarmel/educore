<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdministrateurController extends Controller
{
    public function index()
    {
        $admins = User::whereIn('role', ['admin', 'superadmin', 'comptable'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('admin.administrateurs', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:users,email',
            'role'   => 'required|in:admin,superadmin,comptable',
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required'  => 'L\'email est obligatoire.',
            'email.unique'    => 'Cet email est déjà utilisé.',
            'role.required'   => 'Le rôle est obligatoire.',
        ]);

        $motDePasse = Str::random(10);

        \DB::transaction(function () use ($request, $motDePasse) {
            $user = User::create([
                'nom'       => $request->nom,
                'prenom'    => $request->prenom,
                'email'     => $request->email,
                'password'  => Hash::make($motDePasse),
                'role'      => $request->role,
                'est_actif' => true,
            ]);

            if ($request->role === 'comptable') {
                \App\Models\Comptable::create([
                    'user_id' => $user->id,
                ]);
            }

            Mail::raw(
                "Bonjour {$request->prenom} {$request->nom},\n\n" .
                "Votre compte administrateur EduCore a été créé.\n\n" .
                "Email : {$request->email}\n" .
                "Mot de passe : {$motDePasse}\n\n" .
                "Veuillez vous connecter et changer votre mot de passe.\n\n" .
                "Cordialement,\nL'équipe EduCore",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Votre compte administrateur EduCore');
                }
            );
        });

        return redirect()->route('admin.administrateurs.index')
            ->with('success', "Compte administrateur créé pour {$request->prenom} {$request->nom}. Les identifiants ont été envoyés par email.");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom'    => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:users,email,' . $user->id,
            'role'   => 'required|in:admin,superadmin',
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required'  => 'L\'email est obligatoire.',
            'email.unique'    => 'Cet email est déjà utilisé.',
        ]);

        // Empêcher de se rétrograder soi-même
        if ($user->id === Auth::id() && $request->role !== $user->role) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
            'role'   => $request->role,
        ]);

        return redirect()->route('admin.administrateurs.index')
            ->with('success', "{$user->prenom} {$user->nom} a été modifié avec succès.");
    }

    public function toggleStatus(User $user)
    {
        // Empêcher de se désactiver soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['est_actif' => !$user->est_actif]);

        $action = $user->est_actif ? 'activé' : 'désactivé';

        return redirect()->route('admin.administrateurs.index')
            ->with('success', "Le compte de {$user->prenom} {$user->nom} a été {$action}.");
    }

    public function destroy(User $user)
    {
        // Empêcher de se supprimer soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = "{$user->prenom} {$user->nom}";
        $user->delete();

        return redirect()->route('admin.administrateurs.index')
            ->with('success', "Le compte de {$nom} a été supprimé.");
    }
}