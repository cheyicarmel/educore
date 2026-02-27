<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres';

    protected $fillable = [
        'nom',
        'est_litteraire',
        'est_scientifique',
        'sous_groupe',
    ];

    protected $casts = [
        'est_litteraire'   => 'boolean',
        'est_scientifique' => 'boolean',
    ];
}