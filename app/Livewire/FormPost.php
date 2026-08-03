<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Logs;

class FormPost extends Component
{
    public $mensaje;

    public function guardar()
    {
        Logs::create([
            'mensaje' => $this->mensaje,
            'tipo' => 'Info',
            'created_at' => now(),
        ]);

        $this->mensaje = '';
        $this->dispatch('post-guardado'); // evento para refrescar PostsComponent
    }

    public function render()
    {
        return view('livewire.form-post');
    }
}
