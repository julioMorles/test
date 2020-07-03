<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugadore extends Model
{
    protected $fillable = [
        "nombre",
        "apellidos",
        "nick",
        "email",
        "fondos",
        "apuesta",
    ];
}
