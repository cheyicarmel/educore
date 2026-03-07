<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'est_actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'est_actif' => 'boolean',
    ];

    public function enseignant()
    {
        return $this->hasOne(Enseignant::class);
    }

    public function eleve()
    {
        return $this->hasOne(Eleve::class);
    }

    public function getNomAttribute($value): string
    {
        return strtoupper($value);
    }
}

