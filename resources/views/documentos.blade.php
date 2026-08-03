@extends('layouts.app')

@section('content')
<section class="content">
<div class="container-fluid">

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">📁 Explorador NAS - Intranet Documentos</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="btn-refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        {{-- Buscador Global --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" 
                           placeholder="🔍 Buscar archivos y carpetas en toda la intranet...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="btn-buscar" type="button">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-secondary" id="btn-limpiar" type="button">
                            <i class="fa fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    La búsqueda es global en toda la carpeta "sistema gestion de calidad"
                </small>
            </div>
            <div class="col-md-4 text-right">
                <button class="btn btn-danger btn-sm" id="btn-subir">
                    <i class="fa fa-arrow-up"></i> Subir nivel
                </button>
            </div>
        </div>

        {{-- Ruta actual --}}
        <div class="alert alert-info mb-3" style="background-color: #e3f2fd;">
            <strong><i class="fa fa-folder-open"></i> Ruta actual:</strong>
            <span id="ruta-actual" class="font-weight-bold"></span>
            <button class="btn btn-sm btn-link" id="btn-copiar-ruta" title="Copiar ruta">
                <i class="fa fa-copy"></i>
            </button>
        </div>

        {{-- Tabla de resultados --}}
        <div class="table-responsive">
            <table id="tabla-archivos" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="40%">Nombre</th>
                        <th width="45%">Ubicación</th>
                        <th width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargan vía DataTables -->
                </tbody>
            </table>
        </div>
        
        {{-- Mensaje cuando no hay resultados --}}
        <div id="sin-resultados" class="alert alert-warning text-center" style="display: none;">
            <i class="fa fa-exclamation-triangle"></i> No se encontraron archivos o carpetas
        </div>
    </div>
</div>

</div>
</section>
@endsection

@push('scripts')
<script>
let rutaRaiz = '/mnt/intranet/sistema gestion de calidad';
let rutaActual = rutaRaiz;
let buscando = false; // Flag para saber si estamos en modo búsqueda

// Inicializar DataTable
const tabla = $('#tabla-archivos').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    info: true,
    lengthChange: true,
    pageLength: 50,
    language: {
        processing: "Procesando...",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        emptyTable: "No hay datos disponibles",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    },
    ajax: {
        url: '/archivos/buscar',
        data: function (d) {
            d.q = $('#buscador').val().trim();
            d.basePath = buscando ? '' : rutaActual; // Si buscamos, no limitamos por ruta
        }
    },
    columns: [
        {
            data: 'nombre',
            render: function (data, type, row) {
                if (row.tipo === 'carpeta') {
                    return `
                        <a href="#" class="abrir-carpeta" 
                           data-ruta="${row.ruta}"
                           style="font-weight: bold; color: #007bff;">
                           📁 ${escapeHtml(data)}
                        </a>`;
                }
                
                // Icono según extensión
                let icono = '📄';
                if (row.extension === 'pdf') icono = '📑';
                else if (row.extension === 'doc' || row.extension === 'docx') icono = '📝';
                else if (row.extension === 'xls' || row.extension === 'xlsx') icono = '📊';
                else if (row.extension === 'jpg' || row.extension === 'png') icono = '🖼️';
                
                return `${icono} ${escapeHtml(data)}`;
            }
        },
        {
            data: 'ubicacion',
            render: function (data, type, row) {
                if (row.tipo === 'carpeta') {
                    return '<span class="text-muted">—</span>';
                }
                
                // Mostrar ubicación relativa
                let ubicacion = row.ruta_relativa || '';
                let rutaMostrar = ubicacion.substring(0, ubicacion.lastIndexOf('/'));
                
                if (rutaMostrar === '') rutaMostrar = 'raíz';
                
                // Destacar la coincidencia si estamos buscando
                let termino = $('#buscador').val().trim();
                if (termino && buscando) {
                    let regex = new RegExp(`(${escapeRegex(termino)})`, 'gi');
                    rutaMostrar = rutaMostrar.replace(regex, '<mark>$1</mark>');
                }
                
                return `<small style="color: #6c757d;" title="${escapeHtml(ubicacion)}">
                            📁 ${rutaMostrar}
                        </small>`;
            }
        },
        {
            data: null,
            orderable: false,
            className: 'text-center',
            render: function (row) {
                if (row.tipo !== 'archivo') return '<span class="text-muted">—</span>';
                
                let html = '<div class="btn-group btn-group-sm" role="group">';
                
                if (row.extension === 'pdf') {
                    html += `
                        <a href="/archivos/ver/${row.id}"
                           target="_blank"
                           class="btn btn-success"
                           title="Ver PDF">
                           <i class="fa fa-eye"></i>
                        </a>`;
                }
                
                html += `
                    <a href="/archivos/descargar/${row.id}"
                       class="btn btn-primary"
                       title="Descargar">
                       <i class="fa fa-download"></i>
                    </a>
                    <button class="btn btn-info btn-copiar-ruta"
                            data-ruta="${row.ruta}"
                            title="Copiar ruta">
                        <i class="fa fa-copy"></i>
                    </button>
                </div>`;
                
                return html;
            }
        }
    ],
    drawCallback: function() {
        // Mostrar/ocultar mensaje sin resultados
        let data = this.api().data();
        if (data.length === 0) {
            $('#sin-resultados').show();
        } else {
            $('#sin-resultados').hide();
        }
    }
});

// Funciones de utilidad
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function actualizarRutaActual() {
    $('#ruta-actual').text(buscando ? '🔍 Modo búsqueda global' : rutaActual);
}

// 🔍 Buscar
function realizarBusqueda() {
    let termino = $('#buscador').val().trim();
    if (termino === '') {
        // Si está vacío, salir del modo búsqueda
        buscando = false;
        rutaActual = rutaRaiz;
        actualizarRutaActual();
    } else {
        buscando = true;
        actualizarRutaActual();
    }
    tabla.ajax.reload();
}

// Eventos
$('#buscador').on('keypress', function(e) {
    if (e.which === 13) {
        realizarBusqueda();
    }
});

$('#btn-buscar').on('click', realizarBusqueda);

$('#btn-limpiar').on('click', function() {
    $('#buscador').val('');
    buscando = false;
    rutaActual = rutaRaiz;
    actualizarRutaActual();
    tabla.ajax.reload();
});

$('#btn-refresh').on('click', function() {
    tabla.ajax.reload();
});

// 📂 Entrar a carpeta (solo cuando no estamos buscando)
$(document).on('click', '.abrir-carpeta', function(e) {
    e.preventDefault();
    
    if (buscando) {
        // Si estamos buscando, al hacer clic salimos del modo búsqueda
        buscando = false;
        $('#buscador').val('');
    }
    
    rutaActual = $(this).data('ruta');
    actualizarRutaActual();
    tabla.ajax.reload();
});

// ⬆️ Subir nivel
$('#btn-subir').on('click', function() {
    if (buscando) {
        // Salir del modo búsqueda
        buscando = false;
        $('#buscador').val('');
        rutaActual = rutaRaiz;
    } else {
        // Subir nivel normal
        if (rutaActual === rutaRaiz) {
            // No subir más allá de la raíz
            return;
        }
        rutaActual = rutaActual.substring(0, rutaActual.lastIndexOf('/'));
        if (rutaActual === '') rutaActual = rutaRaiz;
    }
    
    actualizarRutaActual();
    tabla.ajax.reload();
});

// Copiar ruta
$(document).on('click', '.btn-copiar-ruta', function() {
    let ruta = $(this).data('ruta');
    copiarAlPortapapeles(ruta);
});

$('#btn-copiar-ruta').on('click', function() {
    let ruta = buscando ? '🔍 Modo búsqueda global' : rutaActual;
    copiarAlPortapapeles(ruta);
});

function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(function() {
        // Mostrar notificación
        let toast = `
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fa fa-check"></i> Ruta copiada: ${texto}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        `;
        $('body').append(toast);
        setTimeout(() => $('.alert').fadeOut('slow', function() { $(this).remove(); }), 2000);
    }).catch(function() {
        alert('No se pudo copiar la ruta');
    });
}

// Actualizar la visualización inicial
actualizarRutaActual();
</script>
@endpush