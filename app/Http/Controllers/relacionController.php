<?php

namespace App\Http\Controllers;

use App\Models\Relacion;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\RelacionesExport;
use App\Models\lugar;
use App\Models\relaciones\lugarDS;
use App\Models\relaciones\relMapaB;
use App\Models\Entidades\catDSent;
use Maatwebsite\Excel\Facades\Excel;


class relacionController extends Controller
{
    public function fnInfoDeRelacion($id){
        $rel = Relacion::where('idDS', $id)->get();
        return $rel;
    }

    public function fnEditarRelacion(Request $request){
       
        $jsonMapa = json_encode($request->info['jsonDatosMapa']);
        
        Relacion::where('idDS', $request->info['idDS'])->update([
            'jsonDatosMapa' => $request->info['jsonDatosMapa'],
            'cNombre' => $request->info['cNombre'],
            'cURL' => $request->info['cURL'],
            'cContenido' => $request->info['cContenido']
        ]);

        relMapaB::where('idDS', $request->info['idDS'])->delete();

        if(count($request->mapasBase)>0){
            foreach($request->mapasBase as $mapa){
                relMapaB::create([
                    'idDS' => $request->info['idDS'],
                    'idMapa' => $mapa['value']
                ]);
            }
        }
        return "OK";
    }

    public function cboMapasDe($id){
        $LcResp = relMapaB::select([
            'T2.idMapa as value',
            'T2.cNombre as text'
        ])
        ->leftJoin('cat_mapas_base as T2', 'T2.idMapa', 'ds_rel_mapabase.idMapa')
        ->where('ds_rel_mapabase.idDS', $id)
        ->orderBy('T2.cNombre')
        ->get();
        return $LcResp;

    }

    public function TodasRelacionesTXT(){
        //return (public_path('/pdf'));
        $relaciones = Relacion::whereNotNull('cContenido')->get();
        foreach($relaciones as $relacion){
            $name = $relacion->cNombre;
            $name = str_replace(" ","_",$name);
            $micarpeta = (public_path('/textos') . '/'.$name);
            if (!file_exists($micarpeta)) {
                mkdir($micarpeta, 0777, true);
            }
            $contenido = $relacion->cContenido;
            $archivo = fopen($micarpeta.'/'.$name.'_transcripcion_Acuña.txt','a');
            fputs($archivo,$contenido);
            fclose($archivo);
            echo ($micarpeta."<br>");
        }
        
       
    }

    public function export()
    {
        return Excel::download(new RelacionesExport, 'users.xlsx');
       
        
    }

    public function actualizarEntidadesDS(Request $request){
        $cadena = substr($request->lista, 0, -1);
        $array = explode(",", $cadena);
        
        foreach($array as $ds){
            $existe = catDSent::where('cNombre', $ds)->get();
            if($existe == "[]"){
                catDSent::create([
                    'cNombre' => $ds,
                    'idUsrAlta' => Auth::id()
                ]);
            } else {
                //return "HAY";
            }
            
        }

        $LcResp = catDSent::where('cNombre', $ds)->get();
    }

    public function showDSTematicas(){
        $LcResp = catDSent::select([
            'cat_ds_entidades.*',
            'T2.idDS', 'T2.cNombre as cNombreDS', 
        ])
        ->leftJoin('document_sections as T2', 'T2.idDSent', 'cat_ds_entidades.idDSent')
        ->orderBy('cat_ds_entidades.cNombre')->get();
        return $LcResp;
    }

    public function showRelInfo(){
        $relaciones = Relacion::select([
            'document_sections.*',
            'T2.cAbv as cTomo'
        ])
        ->leftJoin('archivos_or as T2', 'document_sections.idArchivoXLS', 'T2.id')
        ->whereNull('document_sections.idDSent')
        ->orderBy('cTomo')->get();
        $LcResp = '[ ';
        $i = 0;
        foreach($relaciones as $ds){
            $LcResp = $LcResp.' { "idDS": '.$ds["idDS"].', "cNombre": "'.$ds["cTomo"].' - '.$ds["cNombre"].'" },';
        }

        $LcResp = substr($LcResp, 0, -1);
        $LcResp = $LcResp." ] ";
        return $LcResp;
    }

    public function AsoDSEnt(Request $request){
        $item = $request->item;
        $relacion = $request->relacion;
        Relacion::where('idDS', $relacion['idDS'])->update([
            'idDSent'=>$item['idDSent']
        ]);
        return "OK";
        
    }

    public function nombreRelTematica($id){
        $ds = catDSent::where('idDSent', $id)->get();
        return $ds[0]->cNombre;
    }

    public function cboRelacionesDe(Request $request){
        $LcResp = Relacion::select([
            'document_sections.*',
            'T2.cAbv as cTomo'
        ])
        ->leftJoin('archivos_or as T2', 'document_sections.idArchivoXLS', 'T2.id');

        if($request->tipo == 'Editar'){
            $rel = lugarDS::where('idLugar', $request->oLugar['id'])->get();
            $cRel = array();
            if(count($rel)>0){
                foreach($rel as $item){
                    array_push($cRel, $item->idDS);
                }
                $LcResp = $LcResp->whereNotIn('document_sections.idDS', $cRel);
            }
        } 

        $LcResp = $LcResp->orderBy('document_sections.cNombre')->get();
        return $LcResp;

    }


    public function AsociaLugarPrincipal(Request $request){
        Relacion::where('idDS', $request->idRel)->update([
            'idElementoPrincipal'=>$request->idLugar
        ]);
        $rel = Relacion::where('idDS', $request->idRel)->get();
        $json = $rel[0]->jsonDatosMapa;
        $json = json_decode($json);

        $lugar = lugar::where('id', $request->idLugar)->get();
        $lugar = $lugar[0];
        $json->centro->lat = $lugar->Y;
        $json->centro->long = $lugar->X;
        Relacion::where('idDS', $request->idRel)->update([
            'jsonDatosMapa'=>json_encode($json)
        ]);
      

        return "OK";
    }


    public function fnShowRelaciones(){
        $rel = Relacion::select([
            'document_sections.*',
            'T2.cAbv as cTomo', 'T2.cISBN as cISBN', 'T3.Placename as cPrincipal'
        ])
        ->leftJoin('archivos_or as T2', 'document_sections.idArchivoXLS', 'T2.id')
        ->leftJoin('lugares as T3', 'document_sections.idElementoPrincipal', 'T3.id')->get();
        return $rel;
    }

    public function cboTematicasTodas(){
        $rel = Relacion::select([
            'document_sections.idDS', 'document_sections.cNombre',
            'T2.cAbv as cTomo', 'T3.cNombre as cNameJson', 'T4.X', 'T4.Y'
        ])
        ->leftJoin('archivos_or as T2', 'document_sections.idArchivoXLS', 'T2.id')
        ->leftJoin('cat_ds_entidades as T3', 'document_sections.idDSent', 'T3.idDSent')
        ->leftJoin('lugares as T4', 'document_sections.idElementoPrincipal', 'T4.id')
        ->whereNotNull('document_sections.idDSent')
        ->whereNotNull('T4.Y')
        ->orderBy('document_sections.cNombre')->get();
        return $rel;
    }

    public function AsoSitioAgustin(Request $request){
        //return $request;
        Relacion::where('idDS', $request->idDS)->update([
            'idSitioWeb'=>$request->id,
            'cSitioWeb'=>$request->alt_nombre,
            'uuid'=>$request->uuid
        ]);
        return "OK";
    }
}
