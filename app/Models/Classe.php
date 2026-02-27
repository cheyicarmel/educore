<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'annee_academique_id',
        'serie_id',
        'prof_principal_id',
        'nom',
        'niveau',
        'cycle',
    ];

    // Année académique de la classe
    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    // Série de la classe
    public function serie()
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }

    // Professeur principal
    public function profPrincipal()
    {
        return $this->belongsTo(Enseignant::class, 'prof_principal_id');
    }

    // Inscriptions dans cette classe
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'classe_id');
    }

    // Effectif : nombre d'élèves inscrits
    public function getEffectifAttribute()
    {
        return $this->inscriptions()->count();
    }
}