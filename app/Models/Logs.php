<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
      protected $table = 'logs';

    public $timestamps = false;

    protected $fillable = [
           'ip_address',
            'mensaje',
            'estado',
            'tipo',
            'respuesta',
        ];

         public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'log_id');
    }
}
