<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviFinancier extends Model
{
    protected $table = 'suivi_financiers';

    protected $fillable = [
        'inscription_id',
        'total_du',
        'total_paye',
        'solde_restant',
        'statut',
    ];

    protected $casts = [
        'total_du'      => 'decimal:2',
        'total_paye'    => 'decimal:2',
        'solde_restant' => 'decimal:2',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'inscription_id');
    }
}