<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Logs;
use App\Models\Respuesta;

class PostsComponent extends Component
{
    public $logs;

    public function mount()
    {
        $this->cargarPosts();
    }

    public function cargarPosts()
    {
        $this->logs = Logs::with('respuestas')->latest()->get();
    }

    protected $listeners = ['post-guardado' => 'cargarPosts'];

    public function render()
    {
        return view('livewire.posts-component', [
            'logs' => Logs::with('respuestas')->latest()->get()
        ]);
    }

      public $respuestaTexto = [];
    
      public function responder($logId)
    {
        
          if (!empty($this->respuestaTexto[$logId])) {
                $ip = request()->header('X-Forwarded-For') ?? request()->ip(); // obtiene la IP del cliente 
                Respuesta::create([
                    'log_id' => $logId,
                    'idr' => $logId,
                    'respuesta'  => $this->respuestaTexto[$logId],
                    'ip_address' => $ip, // guarda la IP en la base de datos
                    'created_at' => now()
                ]);

            $this->respuestaTexto[$logId] = '';
            $this->logs = Logs::with('respuestas')->latest()->get();
        } 
        
      }
}
