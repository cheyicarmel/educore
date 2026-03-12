<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $fillable = [
        'inscription_id',
        'annee_academique_id',
        'genere_par',
        'type',
        'numero_semestre',
        'chemin_fichier_pdf',
        'date_generation',
    ];

    protected $casts = [
        'date_generation' => 'datetime',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function generePar()
    {
        return $this->belongsTo(User::class, 'genere_par');
    }
}