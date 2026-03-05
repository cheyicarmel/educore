<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'inscription_id',
        'enseignant_id',
        'matiere_id',
        'type',
        'numero_semestre',
        'valeur',
        'date_saisie',
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
}