```blade
@extends('layouts.app')

@section('content')

<section class="content">

<div class="container-fluid">

    {{-- PANEL --}}
    <div class="card card-primary shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title m-0">
                📁 Explorador NAS Ficha Técnica
            </h3>

            <div>
                <button type="button" class="btn btn-tool" id="btn-refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="card-body">

            {{-- SEARCH --}}
            <div class="row mb-3">

                <div class="col-md-8">

                    <div class="input-group">

                        <input
                            type="text"
                            id="buscador"
                            class="form-control"
                            placeholder="🔍 Buscar archivos..."
                        >

                        <div class="input-group-append">

                            <button
                                class="btn btn-primary"
                                id="btn-buscar"
                                type="button"
                            >
                                <i class="fa fa-search"></i>
                                Buscar
                            </button>

                            <button
                                class="btn btn-secondary"
                                id="btn-limpiar"
                                type="button"
                            >
                                <i class="fa fa-times"></i>
                                Limpiar
                            </button>

                        </div>

                    </div>

                </div>

                <div class="col-md-4 text-right">

                    <button class="btn btn-danger" id="btn-subir">
                        <i class="fa fa-arrow-left"></i>
                        Regresar
                    </button>

                </div>

            </div>

            {{-- RUTA --}}
            <div class="mb-3">

                <strong>Ruta actual:</strong>

                <span id="ruta-actual">
                    /mnt/nas_pcmercadeo
                </span>

            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table
                    id="tabla-archivos"
                    class="table table-bordered table-hover table-striped"
                >

                    <thead>

                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Fecha</th>
                            <th width="180">Acciones</th>
                        </tr>

                    </thead>

                </table>

            </div>

        </div>

    </div>

</div>

</section>

{{-- MODAL PDF --}}
<div
    class="modal fade"
    id="visorModal"
    tabindex="-1"
    role="dialog"
>

    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title" id="visorModalLabel">
                    📄 Visualizador PDF
                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                >
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body p-0" style="height:85vh;">

                <iframe
                    id="visorIframe"
                    src=""
                    frameborder="0"
                    style="width:100%; height:100%;"
                ></iframe>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Cerrar
                </button>

                <a
                    href="#"
                    target="_blank"
                    class="btn btn-primary"
                    id="btnDescargarDoc"
                >
                    <i class="fa fa-download"></i>
                    Descargar
                </a>

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

#tabla-archivos td{
    vertical-align: middle;
}

mark{
    background: #ffeb3b;
    border-radius: 3px;
    padding: 0 2px;
}

</style>

@endpush


@push('scripts')

<script>

let rutaRaiz   = '/mnt/nas_pcmercadeo';
let rutaActual = rutaRaiz;
let buscando   = false;

const tabla = $('#tabla-archivos').DataTable({

    processing: true,
    serverSide: false,
    searching: false,
    pageLength: 50,
    responsive: true,
    order: [[0, 'asc']],

    language: {
        emptyTable: 'No hay archivos'
    },

    ajax: {

        url: '/fichatecnica/buscar',

        dataSrc: 'data',

        data: function(d){

            d.q = $('#buscador').val().trim();

            d.basePath = buscando
                ? ''
                : rutaActual;
        }
    },

    columns: [

        {
            data: 'nombre',

            render: function(data, type, row){

                if(row.tipo === 'carpeta'){

                    return `
                        <a href="#"
                           class="abrir-carpeta"
                           data-ruta="${row.ruta}">
                            📁 ${escapeHtml(data)}
                        </a>
                    `;
                }

                return `📄 ${escapeHtml(data)}`;
            }
        },

        {
            data: 'ruta',

            render: function(data, type, row){

                if(row.tipo === 'carpeta'){
                    return '—';
                }

                return `
                    <small class="text-muted">
                        ${escapeHtml(data)}
                    </small>
                `;
            }
        },

        {
            data: 'modified',

            render: function(data){
                return data ?? '—';
            }
        },

        {

            data: null,

            orderable: false,

            render: function(row){

                if(row.tipo !== 'archivo'){
                    return '';
                }

                let html = '';

                const extension = (row.extension || '').toLowerCase();

                // PDF
                if(extension === 'pdf'){

                    const viewUrl = `/fichatecnica/ver/${row.id}`;
                    const downloadUrl = `/fichatecnica/descargar/${row.id}`;

                    html += `
                        <button
                            class="btn btn-success btn-sm btn-ver-pdf"
                            data-url="${viewUrl}"
                            data-download="${downloadUrl}"
                            data-titulo="${escapeHtml(row.nombre)}"
                        >
                            <i class="fa fa-eye"></i>
                        </button>
                    `;
                }

                // EXCEL
                if(
                    extension === 'xls' ||
                    extension === 'xlsx' ||
                    extension === 'csv'
                ){

                    html += `
                        <a
                            href="/excel/ver//${row.ruta}"
                            target="_blank"
                            class="btn btn-info btn-sm"
                        >
                            <i class="fa fa-table"></i>
                        </a>
                    `;
                }

                // DESCARGAR
                html += `
                    <a
                        href="/fichatecnica/descargar/${row.id}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="fa fa-download"></i>
                    </a>
                `;

                return html;
            }
        }

    ]

});


// OPEN FOLDER
$(document).on('click', '.abrir-carpeta', function(e){

    e.preventDefault();

    buscando = false;

    rutaActual = $(this).data('ruta');

    $('#ruta-actual').text(rutaActual);

    tabla.ajax.reload();
});


// SEARCH
function realizarBusqueda(){

    const q = $('#buscador').val().trim();

    buscando = q !== '';

    tabla.ajax.reload();
}

$('#btn-buscar').on('click', realizarBusqueda);

$('#buscador').on('keyup', function(e){

    if(e.key === 'Enter'){
        realizarBusqueda();
    }
});


// CLEAR
$('#btn-limpiar').on('click', function(){

    $('#buscador').val('');

    buscando = false;

    rutaActual = rutaRaiz;

    $('#ruta-actual').text(rutaActual);

    tabla.ajax.reload();
});


// BACK
$('#btn-subir').on('click', function(){

    if(rutaActual === rutaRaiz){
        return;
    }

    rutaActual = rutaActual.substring(
        0,
        rutaActual.lastIndexOf('/')
    );

    if(rutaActual === ''){
        rutaActual = rutaRaiz;
    }

    $('#ruta-actual').text(rutaActual);

    tabla.ajax.reload();
});


// REFRESH
$('#btn-refresh').on('click', function(){
    tabla.ajax.reload(null, false);
});


// PDF VIEWER
$(document).on('click', '.btn-ver-pdf', function(){

    const url       = $(this).data('url');
    const download  = $(this).data('download');
    const titulo    = $(this).data('titulo');

    $('#visorModalLabel').html(
        `📄 ${titulo}`
    );

    $('#visorIframe').attr('src', url);

    $('#btnDescargarDoc').attr('href', download);

    $('#visorModal').modal('show');
});


// CLEAN IFRAME
$('#visorModal').on('hidden.bs.modal', function(){

    $('#visorIframe').attr('src', '');
});


// ESCAPE
function escapeHtml(text){

    if(!text) return '';

    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

</script>

@endpush
```
