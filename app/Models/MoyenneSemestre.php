<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MoyenneSemestre extends Model
{
    protected $table = 'moyenne_semestres';
    protected $fillable = ['inscription_id', 'numero_semestre', 'valeur', 'rang'];

    public function inscription() { return $this->belongsTo(Inscription::class); }
}