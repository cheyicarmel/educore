<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Afficher la page de connexion
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Vérifier que le compte est actif et tenter la connexion
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Bloquer les comptes désactivés
            if (!$user->est_actif) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
                ]);
            }

            // Régénérer la session pour éviter le session fixation
            $request->session()->regenerate();

            return $this->redirectByRole($user->role);
        }

        // Echec de connexion — message générique pour ne pas révéler si l'email existe
        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ])->onlyInput('email');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Redirection selon le rôle
    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match($role) {
            'superadmin', 'admin' => redirect()->route('admin.dashboard'),
            'enseignant'          => redirect()->route('enseignant.dashboard'),
            'eleve'               => redirect()->route('eleve.dashboard'),
            'comptable'           => redirect()->route('comptable.dashboard'),
            default               => redirect()->route('login'),
        };
    }
}