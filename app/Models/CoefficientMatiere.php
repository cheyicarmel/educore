<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoefficientMatiere extends Model
{
    protected $table = 'coefficient_matieres';

    protected $fillable = [
        'matiere_id',
        'classe_id',
        'coefficient',
    ];

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }
}