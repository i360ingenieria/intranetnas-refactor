<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
 
    protected $table = 'resp';
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
            'idr',
            'respuesta',
            'ip_address',
            'estado',
            'created_at',
            'updated_at',
            'log_id' // Asegúrate de que este campo exista en tu tabla 'resp'
          ];

    
    public function log()
    {
        return $this->belongsTo(Logs::class, 'log_id');
    }    
}
 
