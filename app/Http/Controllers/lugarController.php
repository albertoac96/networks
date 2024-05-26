<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\lugar;
use Illuminate\Support\Facades\Auth;
use App\Models\relaciones\ElementoHistorial;
use App\Models\relaciones\lugarDS;
use App\Models\relaciones\lugarOthername;
use App\Models\relaciones\lugarPag;
use App\Models\relaciones\lugarRef;

class lugarController extends Controller
{
    public function newLugar(Request $request){
        $new = lugar::create([
            'Placename' => $request->cNombre
        ]);

        ElementoHistorial::create([
            'idElemento' => $new->id,
            'cAccion' => 'Creado',
            'cDescripcion' => 'Se creó el elemento',
            'idUsrAlta' => Auth::id()
        ]);

        return $new;
    }

    public function NewRelacionAsociada(Request $request){
        lugarDS::create([
            'idLugar' => $request->oLugar['id'],
            'idDS' => $request->oDs['idDS'],
            'idUsrAlta' => Auth::id()
        ]);
        return "OK";
    }

    public function NewPagAsociada(Request $request){
        lugarPag::create([
            'idLugar' => $request->idLugar,
            'nPag' => $request->pag,
            'idRelacion' => $request->idRelacion,
            'idUsrAlta' => Auth::id()
        ]);
        return "OK";
    }

    public function editLugar(Request $request){
       
        lugar::where('id', $request->idLugar)
        ->update([
            'idTipoElemento' => $request->idtipoElemento,
            'idTipo' => $request->idtipoLugar,
            'Placename' => $request->cNombre,
            'ModernName' => $request->cNombreModerno,
            'Notes' => $request->cNotas
        ]);
        if($request->idtipoLugar>1){
            lugar::where('id', $request->idLugar)
            ->update([
                'X' => $request->cLat
            ]);
        } else {
            lugar::where('id', $request->idLugar)
            ->update([
                'X' => $request->cLat,
                'Y' => $request->cLong
            ]);
        }

        ElementoHistorial::create([
            'idElemento' => $request->idLugar,
            'cAccion' => 'Editado',
            'cDescripcion' => 'Se editó el elemento',
            'idUsrAlta' => Auth::id()
        ]);


        lugarOthername::where('idLugar', $request->idLugar)->delete();
        lugarRef::where('idLugar', $request->idLugar)->delete();

        foreach($request->oReferencias as $ref){
            lugarRef::create([
                'idLugar' => $request->idLugar,
                'idReferencia' => $ref['idReferencia'],
                'idUsrAlta' => Auth::id()
            ]);
        }

        foreach($request->oOtherName as $name){
            lugarOthername::create([
                'idLugar' => $request->idLugar,
                'idOtherName' => $name['idName'],
                'idUsrAlta' => Auth::id()
            ]);
        }

        return "OK";
    }
}
