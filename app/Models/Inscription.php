<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_academique_id',
        'statut',
        'frais_annuels',
    ];

    protected $casts = [
        'frais_annuels' => 'decimal:2',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function suiviFinancier()
    {
        return $this->hasOne(SuiviFinancier::class, 'inscription_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'inscription_id');
    }

    public function moyenneMatieres()
    {
        return $this->hasMany(MoyenneMatiere::class, 'inscription_id');
    }

    public function moyenneSemestres()
    {
        return $this->hasMany(MoyenneSemestre::class, 'inscription_id');
    }

    public function moyenneAnnuelle()
    {
        return $this->hasOne(MoyenneAnnuelle::class, 'inscription_id');
    }
}