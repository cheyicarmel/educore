<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MoyenneAnnuelle extends Model
{
    protected $table = 'moyenne_annuelles';
    protected $fillable = ['inscription_id', 'valeur', 'rang', 'decision', 'serie_attribuee'];

    public function inscription() { return $this->belongsTo(Inscription::class); }
}