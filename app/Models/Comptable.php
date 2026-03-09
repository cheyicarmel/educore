<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comptable extends Model
{
    protected $fillable = ['user_id', 'telephone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}