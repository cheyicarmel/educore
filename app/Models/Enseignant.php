<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    protected $table = 'enseignants';

    protected $fillable = [
        'user_id',
        'specialite',
        'telephone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attributions()
    {
        return $this->hasMany(Attribution::class, 'enseignant_id');
    }

    public function classes()
    {
        return $this->hasManyThrough(Classe::class, Attribution::class, 'enseignant_id', 'id', 'id', 'classe_id');
    }
}