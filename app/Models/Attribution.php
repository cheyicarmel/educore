<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribution extends Model
{
    protected $table = 'attributions';

    protected $fillable = [
        'enseignant_id',
        'classe_id',
        'matiere_id',
        'annee_academique_id',
        'est_prof_principal',
        'date_attribution',
    ];

    protected $casts = [
        'est_prof_principal' => 'boolean',
        'date_attribution'   => 'date',
    ];

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }
}