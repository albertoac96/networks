<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capa;
use App\Models\relaciones\elementoCapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CapaController extends Controller
{
    public function fnShowCapas(Request $request){
        $LcResp = Capa::where('idDocumentSection', $request->value)->where('cEstatus', 'A')
        ->orderBy('iOrden')->get();
        return $LcResp;
    }

    public function fnNuevaCapa(Request $request){
        $capas = DB::select('select * from capas_mapas where idDocumentSection ='.$request->idRel);
        if(!$capas){
            $iOrden = 1;
        } else {
            $iOrden = count($capas) + 1;
        }
        $LcResp = Capa::create([
            'cNombre' => $request->cNombre,
            'idDocumentSection' => $request->idRel,
            'iOrden' => $iOrden,
            'cEstatus' => 'A',
            'iPublic' => 1,
            'idUsrAlta' => Auth::id()
        ]);

        return $LcResp;
    }

    public function fnEditPublicCapa(Request $request){
        if($request->capa['iPublic'] == 0){
            $new = 1;
        } else {
            $new = 0;
        }
        $LcResp = Capa::where('idCapa', $request->capa['idCapa'])->update([
            'iPublic' => $new
        ]);
        return "OK";
    }

    public function fnEditOrdenCapa(Request $request){
        $orden = 1;
        $capas = $request->capas;
        
        for($i=0; $i<count($capas);$i++){
            Capa::where('idCapa', $capas[$i]['idCapa'])->update([
                'iOrden' => $orden++
            ]);
        }
        
        return "OK";
    }

    public function fnBajaCapa(Request $request){
        Capa::where('idCapa', $request->idCapa)->update([
            'cEstatus' => 'B'
        ]);
        return "OK";
    }

    public function fnAsociaElementos(Request $request){
        $PoCapa = $request->capa;
        $PoItems = $request->items;
        foreach($PoItems as $item){
            $existe = elementoCapa::where('idCapa', $PoCapa['idCapa'])->where('idLugar', $item['id'])->get();
            if($existe == '[]'){
                elementoCapa::create([
                    'idCapa' => $PoCapa['idCapa'],
                    'idLugar' => $item['id'],
                    'idUsrAlta' => Auth::id()
                ]);
            }
        }
    }

    public function fnListaElementosDeCapa(Request $request){
        $PoCapa = $request->capa;
        $LcResp = elementoCapa::select([
            'T2.*',
            'T3.cTipo',
            'T5.Cve_Carta'
        ])
        ->leftJoin('lugares as T2', 'T2.id', 'capa_rel_lugar.idLugar')
        ->leftJoin('cat_tipolugar as T3', 'T3.id', 'T2.idTipo')
        ->leftJoin('rel_lugar_inegi as T4', 'T4.idLugar', 'T2.id')
        ->leftJoin('loc_inegi_csv as T5', 'T5.Id', 'T4.idInegi')
        ->where('capa_rel_lugar.idCapa', $PoCapa['idCapa'])
        ->orderBy('T2.Placename')
        ->get();
        return $LcResp;
    }
}
