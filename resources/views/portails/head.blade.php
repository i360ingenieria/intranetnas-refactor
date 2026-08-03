<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield('title', config('app.name'))</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
 <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/skins/skin-blue.min.css') }}">
 <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
 <link rel="stylesheet" href="{{ asset('dist/css/libro.css') }}">
   <!-- Theme style -->
   
  
  
@stack('styles')
