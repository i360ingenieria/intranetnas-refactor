@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-title', 'Dashboard')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
    <h2 class="page-header" >(Intranet) Post Hospital Santa Monica</h2>
 <div class="card card-primary card-outline direct-chat direct-chat-primary">
    <div class="card-header">
        <h3 class="card-title">Chat</h3>
    </div>
    <div class="card-body">
       <div class="direct-chat-messages" id="contenedor-mensajes"></div>

    </div>
    <div class="card-footer">
        <div class="input-group">
            <input id="nuevo-mensaje" type="text" class="form-control" placeholder="Type Message...">
           <div class="input-group">
        <input id="respuesta-{{ $logs->log_id }}" type="text" class="form-control" placeholder="Escribe tu respuesta...">
        <span class="input-group-append">
            <button onclick="enviarRespuesta( )" class="btn btn-primary">Responder</button>
        </span>
    </div>

        </div>
    </div>
</div>
@endsection
