@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-title', 'Dashboard')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
     <!-- IFrame con el libro -->
    <div class="card card-outline card-success">
        <div class="card-header">
             
            <div class="card-tools">
                <button type="button" class="btn btn-tool" onclick="reloadIframe()">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button type="button" class="btn btn-tool" onclick="openFullscreen()">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
           <div style="position: relative; width: 100%; height: 800px; background: #1a1e24;">
                <!-- Loading overlay -->
                <div id="iframeLoading" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; color: white; flex-direction: column;">
                    <div class="spinner-border text-light mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <div>Cargando manual interactivo...</div>
                </div>
                
                <!-- Iframe -->
                <iframe id="libroIframe" 
                        src="{{ route('libro') }}" 
                        style="width: 100%; height: 100%; border: none; background: #1a1e24;"
                        allowfullscreen
                        onload="hideLoading()">
                </iframe>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Página actual: <span id="pageInfo">1/10</span>
                    </small>
                </div>
                <div class="col-sm-6 col-12 text-right">
                    <small class="text-muted">
                        <i class="fas fa-keyboard"></i>
                        Atajos: ← → (navegar) · ESC (cerrar imagen)
                    </small>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Estilos específicos para el contenedor del iframe */
        #libroIframe {
            transition: opacity 0.3s ease;
        }
        
        #libroIframe.loading {
            opacity: 0;
        }
        
        #iframeLoading {
            display: none !important;
        }
        
        #iframeLoading.active {
            display: flex !important;
        }
        
        /* Ajustes responsive */
        @media (max-width: 768px) {
            div[style*="height: 600px"] {
                height: 400px !important;
            }
        }
        
        /* Mejora visual para el breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
    </style>
@stop

@section('js')
    <script>
        const iframe = document.getElementById('libroIframe');
        const loading = document.getElementById('iframeLoading');
        
        // Mostrar loading mientras carga
        function showLoading() {
            loading.classList.add('active');
            iframe.classList.add('loading');
        }
        
        function hideLoading() {
            loading.classList.remove('active');
            iframe.classList.remove('loading');
        }
        
        // Recargar iframe
        function reloadIframe() {
            showLoading();
            iframe.src = iframe.src; // Recarga la misma URL
        }
        
        // Pantalla completa
        function openFullscreen() {
            if (iframe.requestFullscreen) {
                iframe.requestFullscreen();
            } else if (iframe.webkitRequestFullscreen) { /* Safari */
                iframe.webkitRequestFullscreen();
            } else if (iframe.msRequestFullscreen) { /* IE11 */
                iframe.msRequestFullscreen();
            }
        }
        
        // Escuchar mensajes del iframe (si el libro envía información)
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'pageChange') {
                document.getElementById('pageInfo').textContent = 
                    event.data.current + '/' + event.data.total;
            }
        });
        
        // Mostrar loading al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            showLoading();
        });
        
        // Manejar errores de carga
        iframe.addEventListener('error', function() {
            hideLoading();
            alert('Error al cargar el manual. Por favor, recarga la página.');
        });
        
        // Opcional: recargar si el iframe pierde conexión
        iframe.addEventListener('load', function() {
            console.log('Iframe cargado correctamente');
        });
    </script>
@stop
