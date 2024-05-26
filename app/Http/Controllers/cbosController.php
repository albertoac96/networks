<?php

namespace App\Http\Controllers;

use App\Models\Catalogos\MapaBase;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Catalogos\Othername;
use App\Models\Catalogos\TipoElemento;
use App\Models\Catalogos\TipoLugar;
use App\Models\Catalogos\Referencia;
use App\Models\Relacion;
use App\Models\lugar;
use App\Models\relaciones\lugarPag;

class cbosController extends Controller
{
    public function cboOtrosNombres(){
        $LcResp = Othername::select([
            'idName', 'cName'
        ])
        ->orderBy('cName')
        ->get();
        return $LcResp;
    }

    public function cboMapasBase(){
        $LcResp = MapaBase::select([
            'idMapa as value', 'cNombre as text'
        ])
        ->orderBy('cNombre')
        ->get();
        return $LcResp;
    }

    public function OtrosNombresDe($idLugar){
        $LcResp = Othername::select([
            'cat_othernames.idName',
            'cat_othernames.cName'
        ])
        ->leftJoin('rel_lugar_othername as T2', 'cat_othernames.idName', 'T2.idOtherName')
        ->where('T2.idLugar', $idLugar)
        ->orderBy('cat_othernames.cName')
        ->get();
        return $LcResp;
    }

    public function cboTipoElementos(){
        $LcResp = TipoElemento::select([
            'idTipo as id', 'cTipo as value'
        ])
        ->orderBy('cTipo')
        ->get();
        return $LcResp;
    }

    public function cboTipoLugar(){
        $LcResp = TipoLugar::select([
            'id', 'cTipo as value'
        ])
        ->orderBy('cTipo')
        ->get();
        return $LcResp;
    }

    public function cboReferencias(){
        $LcResp = Referencia::select([
            'idReferencia', 'cNombre'
        ])
        ->orderBy('cNombre')
        ->get();
        return $LcResp;
    }

    public function ReferenciasDe($idLugar){
        $LcResp = Referencia::select([
            'cat_referencias.idReferencia',
            'cat_referencias.cNombre'
        ])
        ->leftJoin('rel_lugar_referencia as T2', 'cat_referencias.idReferencia', 'T2.idReferencia')
        ->where('T2.idLugar', $idLugar)
        ->orderBy('cat_referencias.cNombre')
        ->get();
        return $LcResp;
    }

    public function RelacionesDe($idLugar){

        /*$paginas = lugarPag::whereNull('idRelacion')->get();
        return $paginas;
        foreach($paginas as $lugar){
            $infoLugar = lugar::where('id', $lugar->idLugar)->get();  
            if($infoLugar != []){
                $tomo = $infoLugar[0]['idArchivoExcel'];
            
            $indices = DB::table('indice_document_sections')->where('idArchivo', $tomo)->get();
            
            foreach($indices as $indice){
                $inicio = $indice->Inicio;
                $fin = $indice->Fin;
               
                if($lugar->nPag <= $fin && $lugar->nPag >= $inicio){
                   
                    lugarPag::where('id', $lugar->id)
                    ->update(['idRelacion' => $indice->idDocumentSection]);
                }
            }
            }
            

        }*/


        $LcResp = Relacion::select([
            'document_sections.idDS as id',
            'document_sections.cNombre',
            'T3.cAbv as cTomo'
        ])
        ->leftJoin('rel_lugar_document_section as T2', 'document_sections.idDS', 'T2.idDS')
        ->leftJoin('archivos_or as T3', 'document_sections.idArchivoXLS', 'T3.id')
        ->where('T2.idLugar', $idLugar)
        ->orderBy('document_sections.cNombre')
        ->get();
        return $LcResp;
    }

    public function PaginasDeRelacion(Request $request){
        $LcResp = lugarPag::select([
            'id',
            'nPag',
        ])
        ->where('idLugar', $request->idLugar)
        ->where('idRelacion', $request->idRelacion)
        ->orderBy('nPag')
        ->get();
        return $LcResp;
    }
}
