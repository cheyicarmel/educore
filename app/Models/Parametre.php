<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'nom_etablissement',
        'slogan',
        'ville',
        'pays',
        'adresse',
        'telephone',
        'telephone2',
        'email',
        'site_web',
        'logo',
        'seuil_insuffisant',
        'seuil_passable',
        'seuil_assez_bien',
        'seuil_bien',
        'seuil_tres_bien',
        'seuil_excellent',
    ];

    protected $casts = [
        'seuil_insuffisant' => 'decimal:1',
        'seuil_passable'    => 'decimal:1',
        'seuil_assez_bien'  => 'decimal:1',
        'seuil_bien'        => 'decimal:1',
        'seuil_tres_bien'   => 'decimal:1',
        'seuil_excellent'   => 'decimal:1',
    ];

    // Toujours une seule ligne en base — pattern Singleton
    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'nom_etablissement' => 'Mon Établissement',
            'ville'             => 'Cotonou',
            'pays'              => 'Bénin',
            'seuil_insuffisant' => 0,
            'seuil_passable'    => 10,
            'seuil_assez_bien'  => 12,
            'seuil_bien'        => 14,
            'seuil_tres_bien'   => 16,
            'seuil_excellent'   => 18,
        ]);
    }

    // Helper mention selon une moyenne
    public function getMention(float $moyenne): string
    {
        return match(true) {
            $moyenne >= $this->seuil_excellent   => 'Excellent',
            $moyenne >= $this->seuil_tres_bien   => 'Très bien',
            $moyenne >= $this->seuil_bien        => 'Bien',
            $moyenne >= $this->seuil_assez_bien  => 'Assez bien',
            $moyenne >= $this->seuil_passable    => 'Passable',
            default                              => 'Insuffisant',
        };
    }
}