<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MoyenneMatiere extends Model
{
    protected $table = 'moyenne_matieres';
    protected $fillable = ['inscription_id', 'matiere_id', 'numero_semestre', 'moyenne_interrogations', 'moyenne_generale', 'moyenne_avec_coefficient'];

    public function inscription() { return $this->belongsTo(Inscription::class); }
    public function matiere()     { return $this->belongsTo(Matiere::class); }
}