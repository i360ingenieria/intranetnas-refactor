<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichaTec extends Model
{
    protected $table = 'fichatec';

    public $timestamps = false;

    protected $fillable = [
           'nombre',
            'ruta',
            'tipo',
            'extension',
            'size',
            'modified'
       ];
}
