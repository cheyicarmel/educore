<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnseignantController extends Controller
{
    public function index()
    {
        $enseignants = Enseignant::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.enseignants', compact('enseignants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:users,email',
            'specialite'=> 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required'  => 'L\'email est obligatoire.',
            'email.unique'    => 'Cet email est déjà utilisé.',
        ]);

        DB::transaction(function () use ($request) {
            // Générer un mot de passe aléatoire
            $motDePasse = Str::random(10);

            // Créer le compte utilisateur
            $user = User::create([
                'nom'      => $request->nom,
                'prenom'   => $request->prenom,
                'email'    => $request->email,
                'password' => Hash::make($motDePasse),
                'role'     => 'enseignant',
                'est_actif'=> true,
            ]);

            // Créer le profil enseignant
            Enseignant::create([
                'user_id'   => $user->id,
                'specialite'=> $request->specialite,
                'telephone' => $request->telephone,
            ]);

            // Envoyer les identifiants par email
            Mail::raw(
                "Bonjour {$request->prenom} {$request->nom},\n\n" .
                "Votre compte EduCore a été créé.\n\n" .
                "Email : {$request->email}\n" .
                "Mot de passe : {$motDePasse}\n\n" .
                "Veuillez vous connecter et changer votre mot de passe.\n\n" .
                "Cordialement,\nL'équipe EduCore",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Vos identifiants EduCore');
                }
            );
        });

        return redirect()->route('admin.enseignants.index')
            ->with('success', 'Enseignant créé avec succès. Les identifiants ont été envoyés par email.');
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:users,email,' . $enseignant->user_id,
            'specialite'=> 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required'  => 'L\'email est obligatoire.',
            'email.unique'    => 'Cet email est déjà utilisé.',
        ]);

        DB::transaction(function () use ($request, $enseignant) {
            $enseignant->user->update([
                'nom'    => $request->nom,
                'prenom' => $request->prenom,
                'email'  => $request->email,
            ]);

            $enseignant->update([
                'specialite' => $request->specialite,
                'telephone'  => $request->telephone,
            ]);
        });

        return redirect()->route('admin.enseignants.index')
            ->with('success', 'Enseignant modifié avec succès.');
    }

    public function toggleStatus(Enseignant $enseignant)
    {
        $enseignant->user->update([
            'est_actif' => !$enseignant->user->est_actif,
        ]);

        $message = $enseignant->user->est_actif
            ? 'Enseignant activé avec succès.'
            : 'Enseignant désactivé avec succès.';

        return redirect()->route('admin.enseignants.index')
            ->with('success', $message);
    }

    public function destroy(Enseignant $enseignant)
    {
        // Bloquer si l'enseignant a des notes saisies ou des attributions
        if ($enseignant->attributions()->count() > 0) {
            return redirect()->route('admin.enseignants.index')
                ->with('error', 'Impossible de supprimer cet enseignant car il a des attributions actives. Désactivez-le à la place.');
        }

        DB::transaction(function () use ($enseignant) {
            $user = $enseignant->user;
            $enseignant->delete();
            $user->delete();
        });

        return redirect()->route('admin.enseignants.index')
            ->with('success', 'Enseignant supprimé avec succès.');
    }
}