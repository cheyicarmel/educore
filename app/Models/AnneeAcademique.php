<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeAcademique extends Model
{
    protected $table = 'annees_academiques';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // Scope année active
    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    // Scope années terminées
    public function scopeTerminee($query)
    {
        return $query->where('statut', 'terminee');
    }

    // Scope années à venir
    public function scopeAVenir($query)
    {
        return $query->where('statut', 'a_venir');
    }

    // Helpers
    public function estActive(): bool
    {
        return $this->statut === 'active';
    }

    public function estTerminee(): bool
    {
        return $this->statut === 'terminee';
    }

    public function estAVenir(): bool
    {
        return $this->statut === 'a_venir';
    }
}