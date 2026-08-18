<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    protected $fillable = [
        'nombre',
        'logo',
        'url',
        'categoria',
        'icono',
        'color',
        'orden',
        'activo'
    ];
}