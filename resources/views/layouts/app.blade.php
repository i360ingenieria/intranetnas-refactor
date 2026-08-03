<!DOCTYPE html>
<html lang="es">
<head>
       @include('portails.head')
      
</head>
<body class="hold-transition skin-blue sidebar-mini">
    @include('portails.navbar')
     @include('portails.sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @yield('content')
    </div>
         @include('portails.footer')
         @include('portails.minisidebar')
@livewireStyles
@livewireScripts


{{-- JS base --}}
@include('portails.scripts')
@livewireScripts
<script src="{{ asset('js/foro.js') }}"></script>
<script>
setInterval(() => {
    fetch('/respuestas')
        .then(res => res.text())
        .then(html => {
            document.getElementById('contenedor-mensajes').innerHTML = html;
        });
}, 5000);

</script>
</body>
</html>
