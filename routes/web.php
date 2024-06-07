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
        return view('auth.login');
    }
    return view('welcome');
    
});

Route::get('/acercade', function () {
   
    return view('acerca');
    
})->name('acercade');






Route::middleware(['auth'])->group(function () {

  Route::group(['prefix' => 'projects'], function(){
        Route::post('/check', [App\Http\Controllers\proyectos\datosController::class, 'convertJSON']);
        Route::post('/new', [App\Http\Controllers\proyectos\datosController::class, 'newProject']);
        Route::post('/archivoup', [App\Http\Controllers\proyectos\datosController::class, 'subirArchivo']);
        Route::get('/info/{id}', [App\Http\Controllers\proyectos\datosController::class, 'infoProyecto']);
        Route::get('/list', [App\Http\Controllers\proyectos\datosController::class, 'listProjects']);
        Route::post('/compute', [App\Http\Controllers\proyectos\datosController::class, 'ComputeGraph']);

        Route::post('/controlnode', [App\Http\Controllers\proyectos\grafosControl::class, 'CalculateNodesControl']);

        Route::get('/vergrafos/{id}', [App\Http\Controllers\proyectos\grafosControl::class, 'traeGrafos']);
    });


});





Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*');

