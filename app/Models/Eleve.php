<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    protected $table = 'eleves';

    protected $fillable = [
        'user_id',
        'numero_matricule',
        'date_naissance',
        'sexe',
        'email_parent',
        'telephone_parent',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'eleve_id');
    }

    // Inscription de l'année active
    public function inscriptionActive()
    {
        return $this->hasOne(Inscription::class, 'eleve_id')
            ->whereHas('anneeAcademique', function ($q) {
                $q->where('statut', 'active');
            });
    }

    // Générer un matricule unique
    public static function genererMatricule(): string
    {
        $annee = date('Y');
        $dernier = self::where('numero_matricule', 'like', "EDC-{$annee}-%")
            ->orderBy('numero_matricule', 'desc')
            ->first();

        if ($dernier) {
            $numero = (int) substr($dernier->numero_matricule, -3) + 1;
        } else {
            $numero = 1;
        }

        return "EDC-{$annee}-" . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}