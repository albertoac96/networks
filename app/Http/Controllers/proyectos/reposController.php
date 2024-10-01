<?php

namespace App\Http\Controllers\proyectos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Repositorio;
use App\Models\relColRepo;

class reposController extends Controller
{
    public function addRepo(Request $request){
        $cName = $request->nombre;
        $cDescripcion = $request->desc;
        $uuid = uniqid();

        $item = Repositorio::create([
            'uuid' => $uuid,
            'cNombre' => $cName,
            'cDescripcion' => $cDescripcion,
            'idUsrAlta' => Auth::id()
        ]);

        return "OK";
    }

    public function updateRepo(Request $request){
        $cName = $request->nombre;
        $cDescripcion = $request->desc;

        Repositorio::where('idRepo', $request->idRepo)->update([
            'cNombre' => $cName,
            'cDescripcion' => $cDescripcion
        ]);

        return "OK";
    }

    public function estatusRepo($id, $estatus){
        Repositorio::where('idRepo', $id)->update([
            'cEstatus' => $estatus
        ]);

        return "OK";
    }

    public function publicColeccion($id, $public){
        Repositorio::where('idRepo', $id)->update([
            'iPublic' => $public
        ]);

        return "OK";
    }

    public function addRelColeccion($idCol, $idRepo){
       // Verificar si ya existe la relación
        $exists = relColRepo::where('idRepo', $idRepo)
        ->where('idColeccion', $idCol)
        ->exists();

        // Si no existe, crear la relación
        if (!$exists) {
            relColRepo::create([
                'idRepo' => $idRepo,
                'idColeccion' => $idCol,
                'idUsrAlta' => Auth::id()
            ]);

            return "OK";
        } else {
            // Opcional: manejar el caso en que ya existe la relación
            // Puedes lanzar una excepción, retornar un mensaje o simplemente no hacer nada
            return response()->json(['message' => 'La relación ya existe'], 409);
        }
    }

    public function deleteRelProject($idRel){
        // Buscar la relación
        $relation = relColRepo::where('idRel', $idRel)->first();
        // Verificar si la relación existe
        if ($relation) {
            // Eliminar la relación
            $relation->delete();
            return response()->json(['message' => 'Relación eliminada con éxito'], 200);
        } else {
            // Opcional: manejar el caso en que no existe la relación
            return response()->json(['message' => 'La relación no existe'], 404);
        }
    }
}
