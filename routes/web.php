<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('/', function () {
    $idUser = Auth::id();
    if($idUser == ""){
        return view('auth.loginnew');
    }
    return view('welcome');
    
})->name('inicio');



Route::get('/acercade', function () {
   
    return view('acerca');
    
})->name('acercade');

Route::get('/repo', function () {
   
    return view('repo');
    
})->name('repo');

Route::get('/phpinfo', function () {
    set_time_limit(300); 
    phpinfo();
});


Route::get('/prueba', [App\Http\Controllers\proyectos\coleccionController::class, 'colPrueba']);
Route::get('/grafos/{id}', [App\Http\Controllers\proyectos\coleccionController::class, 'verGrafo']);
Route::post('/download', [App\Http\Controllers\proyectos\coleccionController::class, 'descargar']);


Route::middleware(['auth'])->group(function () {
    Route::post('/updategrafos', [App\Http\Controllers\proyectos\grafosControl::class, 'updateGrafos']);
  
  
    Route::group(['prefix' => 'projects'], function(){
        Route::post('/check', [App\Http\Controllers\proyectos\datosController::class, 'convertJSON']);
        Route::post('/update', [App\Http\Controllers\proyectos\datosController::class, 'updateProyecto']);
        Route::post('/new', [App\Http\Controllers\proyectos\datosController::class, 'newProject']);
        Route::post('/archivoup', [App\Http\Controllers\proyectos\datosController::class, 'subirArchivo']);
        Route::get('/info/{id}', [App\Http\Controllers\proyectos\datosController::class, 'infoProyecto']);
        Route::get('/list', [App\Http\Controllers\proyectos\datosController::class, 'listProjects']);
        Route::post('/compute', [App\Http\Controllers\proyectos\datosController::class, 'ComputeGraph']);
        //Route::post('/compute', [App\Http\Controllers\graphController::class, 'ComputeGraphOptimized']);
        Route::post('/download', [App\Http\Controllers\proyectos\datosController::class, 'DescargarTodo']);
       
        Route::post('/controlnode', [App\Http\Controllers\proyectos\grafosControl::class, 'CalculateNodesControl']);

        Route::get('/vergrafos/{id}', [App\Http\Controllers\proyectos\grafosControl::class, 'traeGrafos']);
    });

    Route::group(['prefix' => 'cols'], function(){
        Route::get('/list', [App\Http\Controllers\proyectos\coleccionController::class, 'listColeccion']);
        Route::post('/add', [App\Http\Controllers\proyectos\coleccionController::class, 'addColeccion']);

        
      
    });


});





Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*');

