<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = ['nombre', 'paise_id'];

    public function pais()
    {
        return $this->belongsTo('App\Models\Paise', 'paise_id', 'id');
    }
}
