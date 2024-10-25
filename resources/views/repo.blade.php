<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        

        <title>Mi sistema</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

         <script src="{{ asset('js/app.js') }}" defer></script>
         <link href="{{ asset('css/app.css') }}" rel="stylesheet">
          <script src="https://kit.fontawesome.com/e813ff6fee.js" crossorigin="anonymous"></script>
    
        
    </head>
    <body>
       <div id="app">
            <div id="loading-overlay" style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: black; color: white;">
            Cargando...
        </div>
            <repo></repo>
       </div>
    </body>
</html>