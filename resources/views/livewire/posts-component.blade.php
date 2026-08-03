<div class="direct-chat-messages">
    @foreach($logs as $log)
        <div class="direct-chat-msg">
            <span class="direct-chat-name">{{ $log->mensaje }}</span>

            {{-- Respuestas --}}
                           <div class="mensajes-container">

            @foreach($log->respuestas as $resp)
                    <span class="direct-chat-name">{{ $resp->respuesta }}</span>
                     
               
            @endforeach
          </div> 
            {{-- Formulario de respuesta --}}
           <textarea wire:model="respuestaTexto.{{ $log->id }}"></textarea>

            <button wire:click="responder({{ $log->id }})">
                
        </div>
    @endforeach
</div>
