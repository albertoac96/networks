@extends('layouts.app')

@section('content')
<div class="container-sm">
    <h4>Acerca de</h4>

    <hr class="my-4">

    <div class="card" style="width: 100%;">
    <div class="card-body">
    <p class="text-align: justify;"><h5><b>Sobre la base de datos</b></h5></p>
    <p class="text-align: justify;">El sistema cuenta con 33 tablas en la base de datos sin contar el módulo de seguridad, que comprende 5 tablas más. 
    En lo que va de 2022 se llevó a cabo la migración de información de las hojas de Excel a una base de datos relacional utilizando MariaDB.</p>
    <p class="text-align: justify;">La base de datos relacional nos proporciona una forma de consultar la base de datos en forma de expresiones semánticas. 
    Las relaciones entre los datos expresan un modelo de datos semántico. </p>
    <p class="text-align: justify;">El sistema cuenta con dos grandes grupos que se irán relacionando con otras tablas: Document Sections y Lugares. 
    Las Document Sections (DS) hacen referencia a la división que se realizó por relación estudiada y pertenecen a tomos, 
    paleografiados anteriormente, de autores como Acuña y Cline. Por su parte, la tabla Lugares alberga la información de cada 
    lugar que se referencia en el texto como un topónimo o como un elemento que forma parte del ambiente (por ejemplo: ríos, montañas, 
    áreas culturales, elementos arquitectónicos, etc.)</p>

    <img src="images/MER-Julio2022.jpg"
     alt="ModeloER"
     width="100%">
     <p class="text-align: center;">Modelo Entidad-Relación de la base de datos administrativa</p>


    <hr class="my-2">
    </div>
    </div>

    <hr class="my-4">

    <div class="card" style="width: 100%;">
    <div class="card-body">
    <p><h5><b>Diseño de API</b></h5></p>


    <p class="text-align: justify;">Se programo un servicio de API para la comunicación entre el sistema administrativo y el sitio web. Con la finalidad de enviar información 
para que en el sitio web de vista publica se puedan visualizar los mapas de acuerdo con las actualizaciones que se vayan haciendo en el sistema de administración.</p>

<p class="text-align: justify;"><h5>Los servicios API con los que se cuentan actualmente son:</h5></p>

<p class="text-align: justify;"><b>Consulta de document_sections (relaciones geográficas)</b></p>
<p class="text-align: justify;">RUTA GET: /api/v1/rels/0<br>
Resultado de consulta: Lista JSON de relaciones geográficas en el sistema de administración</p>

<p class="text-align: justify;"><b>Consulta de mapa por document_section</b></p>
<p class="text-align: justify;">RUTA GET: /api/v1/mapa/{idDS}<br>
Resultado de consulta: Árbol de datos JSON que contiene la información para pintar el mapa con Leaflet y las capas con sus respectivos elementos.</p>


    </div>
    </div>



</div>
@endsection