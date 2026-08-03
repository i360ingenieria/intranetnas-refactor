@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-title', 'Dashboard')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
<table id="tablaContactos" class="display table">
        <thead>
            <tr>
                <th>#</th>
                <th>Perfil</th>
            </tr>
        </thead>
        <tbody>
            @foreach (collect($contactos)->chunk(2) as $fila)
                <tr>
                    @foreach ($fila as $index => $contacto)
                        <td>
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <img class="profile-user-img img-responsive img-circle"
                                         src="{{ asset('dist/img/avatardir.png') }}"
                                         alt="User profile picture">

                                    <h3 class="profile-username text-center">{{ $contacto['Nombre'] ?? '' }}</h3>
                                    <span class="label label-success">{{ $contacto['Cargo'] ?? '' }}</span>

                                    <ul class="list-group list-group-unbordered">
                                        <li class="list-group-item">
                                            <b>Contacto</b> <a class="pull-right">{{ $contacto['Teléfono'] ?? '' }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Correo</b> <a class="pull-right">{{ $contacto['Email'] ?? '' }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Ubicación de trabajo</b> <a class="pull-right">{{ $contacto['Ubicacion'] ?? '' }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    @endforeach

                    {{-- Si hay solo un contacto en la fila, agregamos celda vacía --}}
                    @if ($fila->count() < 2)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
 
 
     <!-- Estilos de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>

     $(document).ready(function() {
        $('#tablaContactos').DataTable({
            "pageLength": 10,
            "searching": true,
            "ordering": false,
            "lengthMenu": [5, 10, 50, 100],
            "language": {
                "decimal": ",",
                "thousands": ".",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando..."
            }
        });
    });
</script>

 

@endsection