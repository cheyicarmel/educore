<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    //

    protected $table = 'paiements';

    protected $fillable = [
        'inscription_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'comptable_id',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function comptable()
    {
        return $this->belongsTo(Comptable::class, 'comptable_id');
    }
}
