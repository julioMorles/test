<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudade extends Model
{
    protected $fillable = ['nombre', 'departamento_id'];

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento', 'departamento_id', 'id');
    }
}
