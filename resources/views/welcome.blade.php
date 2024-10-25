<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        

         <title>RNG - Spatial Network Analysis</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Añadir en la sección <head> del archivo index.html -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Maven+Pro:wght@400..900&display=swap" rel="stylesheet">


         <script src="{{ asset('js/app.js') }}" defer></script>
         <link href="{{ asset('css/app.css') }}" rel="stylesheet">
          <script src="https://kit.fontawesome.com/e813ff6fee.js" crossorigin="anonymous"></script>
    
        
    </head>
    <body>
       <div id="app">
            <div id="loading-overlay" style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: black; color: white;">
            Cargando...
        </div>
            <app></app>
       </div>
    </body>
</html>
