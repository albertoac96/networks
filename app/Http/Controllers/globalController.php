<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LocInegi;
use App\Models\Carta;
use App\Models\lugar;
use App\Models\Relacion;

use ZipArchive;

use App\Exports\diego;
use Maatwebsite\Excel\Facades\Excel;

class globalController extends Controller
{

    public function LlenaTablaXArchivo($id)
    {
        /*$LcCad = "select T1.*, T3.Cve_Carta from 
        lugares as T1
        left join rel_lugar_inegi as T2 on T1.id = T2.idLugar
        left join loc_inegi_csv as T3 on T2.idInegi = T3.Id 
        where T1.idArchivoExcel =" . $id." order by T1.id";
        $tabla = DB::select($LcCad);*/

        if($id == 0){
            $LcCad = "select T2.id as value, T2.cTipo as text from lugares as T1 
            left join cat_tipolugar as T2 on T1.idTipo = T2.id where T1.idTipo is not null group by T2.cTipo order by T2.cTipo";
            $tipos = DB::select($LcCad);
            $LcCad = "select idDS as value, cNombre as text, T1.idElementoPrincipal, T2.Placename from document_sections as T1
            left join lugares as T2 on T1.idElementoPrincipal = T2.id
            order by T1.cNombre";
            $cbo = DB::select($LcCad);
        } else {
            $LcCad = "select T2.id as value, T2.cTipo as text from lugares as T1 left join cat_tipolugar as T2 on T1.idTipo = T2.id where T1.idTipo is not null and T1.idArchivoExcel = ".$id." group by T2.cTipo order by T2.cTipo";
            $tipos = DB::select($LcCad);
            $LcCad = "select idDS as value, cNombre as text from document_sections as T1
            left join lugares as T2 on T1.idElementoPrincipal = T2.id
            where T1.idArchivoXLS =" . $id." order by T1.cNombre";
            $cbo = DB::select($LcCad);
        }

        


        $Total = array_merge(
            array('tipos' => $tipos),
            array('cbo' => $cbo),

        );
        return $Total;
    }

    public function infoLugar(Request $request){
        $PidLugar = $request->id;
        $otrosNombres = DB::table('rel_lugar_othername')
        ->select([
            'T2.*'
        ])
        ->leftJoin('cat_othernames as T2', 'T2.idName', 'rel_lugar_othername.idOtherName')
        ->where('rel_lugar_othername.idLugar', $PidLugar)
        ->get();
       
        $paginas = DB::select('select * from rel_lugares_paginas where idLugar ='.$PidLugar." order by nPag");
        $inegi = DB::select('select T2.* from rel_lugar_inegi as T1 left join loc_inegi_csv as T2 on T1.idInegi = T2.Id where T1.idLugar ='.$PidLugar);
        $referencias = DB::select('select T2.* from rel_lugar_referencia as T1 left join cat_referencias as T2 on T1.idReferencia = T2.idReferencia where T1.idLugar ='.$PidLugar);
        $documentos = $this->traeDocumentosDeLugar($request);
        $lugar = DB::select('select * from lugares where id ='.$request->id);
      //return $documentos;
        return response()->json([
            'otrosNombres' => $otrosNombres,
            'paginas' => $paginas,
            'inegi' => $inegi,
            'documentos' => json_decode($documentos),
            'lugar' => $lugar[0],
            'refs' => $referencias
        ]);
       
    }

    private function traeDocumentosDeLugar($lugar){
       
        $archivos = DB::select('select T1.*, T2.idArchivoXLS from
        rel_lugar_document_section as T1
        left join document_sections as T2 on T1.idDS = T2.idDS
        where T1.idLugar = '.$lugar->id.'
        group by T2.idArchivoXLS');

        //$archivos = DB::select('select * from archivos_or where id ='.$lugar->idArchivoExcel);
        $LcArchivos = " [ ";
        $iArchivo = 0;
        foreach($archivos as $archivo){
            $oArchivo = DB::select('select * from archivos_or where id ='.$archivo->idArchivoXLS);
            $oArchivo = $oArchivo[0];
                  $PidLugar = $lugar->id;    
            $LcArchivos = $LcArchivos.' { '.$this->HazObjToString($oArchivo).', "children" :  [ ';
            $LcCad = "select T1.idDS, T1.cNombre from document_sections as T1
            left join rel_lugar_document_section as T2
            on T1.idDS = T2.idDS
            where T1.idArchivoXLS =".$oArchivo->id."
            and T2.idLugar =".$PidLugar.' group by T2.idDS';
                $relaciones = DB::select($LcCad);
           
            if($relaciones == []){
                $LcArchivos = $LcArchivos." ] ";
            } else {
                $iRelacion = 0;
                foreach($relaciones as $relacion){
                    $LcArchivos = $LcArchivos.' {'.$this->HazObjToString($relacion).', "children" : [ ';
                    $LcCad = 'select * from indice_document_sections as T1
                    left join rel_lugar_indice as T2
                    on T1.id = T2.idIndice
                    where T1.idDocumentSection ='.$relacion->idDS.'
                    and T2.idLugar ='.$lugar->id.'';
                    $secciones = DB::select($LcCad);
                    if($secciones == []){
                        $LcArchivos = $LcArchivos."  ";
                    } else {
                        $iSeccion = 0;
                        
                        foreach($secciones as $seccion){
                            $LcArchivos = $LcArchivos.'  { '.$this->HazObjToString($seccion).'} ,';
                        }
                        $LcArchivos = substr($LcArchivos, 0, -1);
                        //$LcArchivos = $LcArchivos."  ";
                    }

                    $LcArchivos = $LcArchivos."]},";
                }
                
                $LcArchivos = $LcArchivos."]}";
            }

            $LcArchivos = substr($LcArchivos, 0, -3);
            //$LcArchivos = $LcArchivos." },";
            $LcArchivos = $LcArchivos."]},";
        }

        
        $LcArchivos = substr($LcArchivos, 0, -1);
        $LcArchivos = $LcArchivos." ]";
        return $LcArchivos;
    }

    private function HazObjToString($obj){
        $array = get_object_vars($obj);
        $keys = array_keys($array);
        $LcResp = " ";
        for($i=0; $i<count($keys); $i++){
            if($keys[$i] == "Seccion" || $keys[$i] == "cAbv" || $keys[$i] == "cNombre"){
                $LcResp = $LcResp.' "name" : "'.$array[$keys[$i]].'", ';
            } else {
                $LcResp = $LcResp.' "'.$keys[$i].'" : "'.$array[$keys[$i]].'", ';
            }
           
        }
        $LcResp = substr($LcResp, 0, -2);
        $LcResp = $LcResp." ";
        return $LcResp;
    }

    public function TraeSeccionesDeDS(Request $request){
        $LcCad = "select id as value, seccion as text, inicio, fin from indice_document_sections where Seccion != '|' and idDocumentSection = ".$request->value;
        $LcResp = DB::select($LcCad);
        return $LcResp;
    }

    public function InfoUser(){
        $info = DB::select('select name, email from users where id ='.Auth::id());
        return $info[0];
    }


    public function llenaTabla(Request $request){


        
        
        $LcResp = lugar::select([
            'lugares.*',
            'T2.cTipo',
            'T5.Cve_Carta'
        ])
        ->leftJoin('cat_tipolugar as T2', 'T2.id', 'lugares.idTipo')
        ->leftJoin('rel_lugar_inegi as T4', 'T4.idLugar', 'lugares.id')
        ->leftJoin('loc_inegi_csv as T5', 'T5.Id', 'T4.idInegi')
        ->leftJoin('rel_lugar_document_section as T6', 'T6.idLugar', 'lugares.id');

        if($request->tipo == 'tomo'){
            $relaciones = Relacion::where('idArchivoXLS', $request->id)->get();
            $cRelaciones = array();
            foreach($relaciones as $rel){
                //$cRelaciones = $cRelaciones.", ".$rel->idDS;
                array_push($cRelaciones, $rel->idDS);
            }
            //$cRelaciones = substr($cRelaciones,2);
            //$cRelaciones = '['.$cRelaciones.']';
            //return $cRelaciones;
            $LcResp = $LcResp->whereIn('T6.idDS', $cRelaciones);
        } else if ($request->tipo == 'relacion') {
            
            $LcResp = $LcResp->where('T6.idDS', $request->id);
        } else if ($request->tipo == 'todo') {
            
        } else {
            $LcResp = $LcResp->leftJoin('rel_lugar_indice as T3', 'T3.idLugar', 'lugares.id')
            ->where('T3.idIndice', $request->id);
        }

        if($request->search || $request->search!=""){
            
            $LcResp = $LcResp->where('lugares.Placename', 'like', '%'.$request->search.'%')
            ->orWhere('lugares.Alt_names', 'like', '%'.$request->search.'%')
            ->orWhere('lugares.ModernName', 'like', '%'.$request->search.'%');

            /*$LcResp = $LcResp->orWhere('lugares.id', 'like', ''.$request->search.'')
            ->orWhere('lugares.Alt_names', 'like', ''.$request->search.'')
            ->orWhere('lugares.Placename', 'like', ''.$request->search.'')
            ->orWhere('lugares.ModernName', 'like', ''.$request->search.'');*/
        }

        if($request->type){
            $cTipos = $this->HazString($request->type, 'value');
            $LcResp = $LcResp->whereIn('lugares.idTipo', $cTipos);
        }

        if($request->estatus){
            $cTipos = $this->HazString($request->estatus, 'value');
            $LcResp = $LcResp->whereIn('lugares.cEstatus', $cTipos);
            foreach($request->estatus as $estatus){
                if($estatus['value'] == 'B'){
                    $LcResp = $LcResp->where('lugares.cEstatusFinal', 'B');
                } 
            }
        } else {
            $LcResp = $LcResp->where('lugares.cEstatusFinal', 'A');
        }



        

        $LcResp = $LcResp->orderBy('lugares.Placename')
        ->groupBy('lugares.id')
        ->paginate(150);

        return $LcResp;




    }

    public function HazString($array, $clave){
        $cResp = array();
        /*for($i=0; $i<count($array); $i++){
           echo $array[$i][$clave];
            $cResp = $cResp + $array[$i][$clave] + ",";
        }*/
        foreach($array as $item){
            array_push($cResp, $item[$clave]);
            //$cResp = $cResp.$item[$clave].", ";
        }
        
       //$cResp = substr($cResp, 0, -2);
        return $cResp;
    }

    public function HazString2($array, $clave){
       
        $cResp = "";
        foreach($array as $item){
            //array_push($cResp, $item[$clave]);
            $cResp = $cResp.$item[$clave].", ";
        }
       
        
       $cResp = substr($cResp, 0, -2);
        return $cResp;
    }

    public function export($id)
    {
        $LcCad = "select * from document_sections where idDS =".$id;
        $DS = DB::select($LcCad);
        $excel = new diego($id);

        

        return Excel::download($excel, $DS[0]->cNombre . '.xlsx');
       
        
    }


    public function TraeLugares(Request $request){
       
        $LcCad = "select T1.id, T1.Placename as name, T1.X as lng, T1.Y as lat, T3.cTipo, T3.cURL 
        from lugares as T1 
        left join rel_lugar_document_section as T2 on T1.id = T2.idLugar 
        left join cat_tipolugar as T3 on T1.idTipo = T3.id ";

        if($request->cTipo == 'parte'){
            $LcCad = $LcCad.' left join rel_lugar_indice as T4 on T4.idLugar = T1.id  ';
        }

        if($request->cTipo == 'tomo'){
            $LcCad = $LcCad." where T1.idArchivoExcel = ".$request->idArchivo;
        } else if($request->cTipo == 'relacion') {
            $LcCad = $LcCad." where T2.idDS = ".$request->idDS;
        } else if ($request->cTipo == 'parte'){
            $LcCad = $LcCad." where T4.idindice = ".$request->idArchivo;
        } else {
            $LcCad = $LcCad." where 1=1 ";
        }

        if($request->types){
            $cTipos = $this->HazString2($request->types, 'value');
            $LcCad = $LcCad." and T1.idTipo in (".$cTipos.") ";
        } 

        $LcCad = $LcCad." and T1.X is not null and T1.Y is not null group by T1.id";

        
        $LcResp = DB::select($LcCad);
        return $LcResp;

    }

    public function cboArchivos()
    {
        $LcCad = "select * from archivos_or order by cAbv";
        $LcResp = DB::select($LcCad);
        return $LcResp;
    }

    public function LlenaTablaXSeccion($id)
    {
        $LcCad = "select T2.*, T4.Cve_Carta from 
        rel_lugar_document_section as T1
        left join lugares as T2 on T2.id = T1.idLugar
        left join rel_lugar_inegi as T3 on T2.id = T3.idLugar
        left join loc_inegi_csv as T4 on T3.idInegi = T4.Id 
        where T1.idDS = " . $id." group by T2.id order by T2.id";
        $LcResp = DB::select($LcCad);
        return $LcResp;
    }

    public function cboEstadosINEGI(){
        $LcCad = "select Nom_Ent as text, Cve_Ent as value from loc_inegi_csv group by Nom_Ent order by Nom_Ent";
        $LcResp = DB::select($LcCad);
       
        return $LcResp;
    }

    public function getPagination(Request $request)
    {
        /*$orderBy = $request->query('orderBy', 'name');
        $order = $request->query('order', 'ASC');
        $perPage = $request->query('perPage', 20);

        $search = $request->query('search', false);
        $type = $request->query('type', false);
        $active = $request->query('active', null);*/

        $search = $request->buscar;
        $estados = $request->estados;
        //return $estados;

        

        
        
        

        $LcResp = DB::table('loc_inegi_csv')
        
        ->where('Nom_Loc', 'like', "%{$search}%")
            ->orWhere('Nom_Mun', 'like', "%{$search}%")
            
            ->paginate(10);
            return $LcResp;

        
        
      

        /*if ($search !== false) {
            $query->where(function($query) use ($search){
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%");
            });
        }

        if ($type !== false) {
            if (is_string($type)) {
                $type = [$type];
            }

            $query->whereIn('type', $type);
        }

        if ($active !== null) {
            $query->where('active', filter_var($active, FILTER_VALIDATE_BOOL));
        }

        return $query->paginate($perPage);*/
    }

    public function traeCartas(Request $request){
        $search = $request->search;
        $LcResp = Carta::select([
            'cartas_inegi.*',
            'rel_carta_inegi_bil.titulo',
            'rel_carta_inegi_bil.url',
            'rel_carta_inegi_bil.datum',
            'rel_carta_inegi_bil.edicion',
            'rel_carta_inegi_bil.id',
            'rel_carta_inegi_bil.url_interno'
        ])
        ->leftJoin('rel_carta_inegi_bil', 'rel_carta_inegi_bil.cvecarta', 'cartas_inegi.CveCarta')
        ->where('cartas_inegi.CveCarta', 'like', "%{$search}%")
        ->orderBy('cartas_inegi.CveCarta')
        ->paginate(10);
        return $LcResp;
    }

    public function traeCartasDe(Request $request){
        if($request->chk == 0){
            $chk = 'N';
        } else if ($request->chk == 1){
            $chk = 'P';
        } else {
            $chk = 'L';
        }
        $LcCad = "select T2.*, T3.titulo, T3.url, T3.datum, T3.edicion, T3.id, T3.url_interno from
        rel_cartas_document_section as T1
        left join rel_carta_inegi_bil as T3 on T3.Id = T1.idCarta
        left join cartas_inegi as T2 on T3.cvecarta = T2.CveCarta
        where T1.idDocSec =".$request->seccion['value']." and T1.cTipo = '".$chk."' order by T3.cvecarta";
        $LcResp = DB::select($LcCad);
        return $LcResp;
    }

    public function URLDeRelacion($id){
        $relacion = DB::select('select * from document_sections where idDS ='.$id);
        $url = $relacion[0]->cURL;
        if($url){
            return $url;
        } else {
            return 0;
        }
      
    }

    public function uploadCarta(Request $request){
        $item = $request->item;
        $Archivo = $_FILES;

        try {
            //OBTIENE LOS PARAMETROS DE ENTRADA DEL ARCHIVO A SUBIR.
            $dirBase = "/media/compartida/app/public";
            
                $target_dir = $dirBase.'/archivos'; 
        
            
            
            $PidCarta = $item;
            $PcArchivo = $Archivo;
        
            
            $LcArchivo = $PcArchivo["archivo"]["name"];           
                
                //PROCEDE A SUBIRLO.		
                if(move_uploaded_file($PcArchivo['archivo']['tmp_name'], $target_dir."/".$LcArchivo)){
                        
                        $LcResp = "OK";

                        DB::update('update rel_carta_inegi_bil set url_interno ="'.$LcArchivo.'" where Id ='.$PidCarta);
                        
                 
                        return $LcResp;
                        
                      
                } else {			
                    throw new Exception("ERROR");
                }	

          
                
              
                
        } catch(Exception $e){
            $LcResp = "ERROR";
            return $LcResp;
        }
        
       
        
    }

    public function TraeTiposTodos(){
        $LcCad = "select T2.id as value, T2.cTipo as text from lugares as T1 left join cat_tipolugar as T2 on T1.idTipo = T2.id where T1.idTipo is not null group by T2.cTipo order by T2.cTipo";
        $tipos = DB::select($LcCad);
        return $tipos;
    }

    public function descargaZonasZIP(Request $request){
        
        
        if($request->chk == 0){
            $chk = 'N';
            $pre = "ZonaNuclear";
        } else if ($request->chk == 1){
            $chk = 'P';
            $pre = "ZonaPeriferica";
        } else {
            $chk = 'L';
            $pre = "ZonaLejana";
        }
        $LcCad = "select T3.* from
        rel_cartas_document_section as T1
        left join rel_carta_inegi_bil as T3 on T3.Id = T1.idCarta
        left join cartas_inegi as T2 on T3.cvecarta = T2.CveCarta
        where T1.idDocSec =".$request->seccion['value']." and T1.cTipo = '".$chk."' order by T3.cvecarta";
        $LcResp = DB::select($LcCad);

        $seccion = DB::select('select * from document_sections where idDS ='.$request->seccion['value']);


         // Define Dir Folder
         $public_dir = "/media/compartida/app/public/archivos";
       
         $zip = new ZipArchive();
         // Ruta absoluta
         $nombreArchive = $pre."-".$seccion[0]->cNombre.".zip";
         $nombreArchivoZip = $public_dir."/".$nombreArchive;
         
         if (!$zip->open($nombreArchivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
             exit("Error abriendo ZIP en $nombreArchivoZip");
         }

        foreach($LcResp as $carta){
           if($carta->url_interno != null){
               // Si no hubo problemas, continuamos
                // Agregamos el script.js
                // Su ruta absoluta, como D:\documentos\codigo\script.js
                $rutaAbsoluta = $public_dir."/".$carta->url_interno;
                // Su nombre resumido, algo como "script.js"
                $nombre = basename($rutaAbsoluta);
                $zip->addFile($rutaAbsoluta, $nombre);
                
               
           }
        }

         // No olvides cerrar el archivo
         $resultado = $zip->close();
         if (!$resultado) {
             exit("Error creando archivo");
         }

        // Ahora que ya tenemos el archivo lo enviamos como respuesta
        // para su descarga
        
        // El nombre con el que se descarga
        $nombreAmigable = "simple.zip";
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=$nombreAmigable");
        // Leer el contenido binario del zip y enviarlo
        //readfile($nombreArchivoZip);

        return $nombreArchive;
        
    
      

    }


    public function deleteZIP(Request $request){
        $public_dir = "/media/compartida/app/public/archivos/";
        $archivo = $request->archivo;
        unlink($public_dir.$archivo);
        
    }

    public function AsociaCartasADS(Request $request){
        $PoSeccion = $request->seccion;
        $PoCartas = $request->cartas;
        if($request->chk == 0){
            $chk = 'N';
        } else if ($request->chk == 1){
            $chk = 'P';
        } else {
            $chk = 'L';
        }
        DB::delete('delete from rel_cartas_document_section where idDocSec = '.$PoSeccion['value'].' and cTipo = "'.$chk.'"');
        foreach($PoCartas as $carta){
            DB::select('insert into rel_cartas_document_section (idCarta, idDocSec, cTipo) values ("'.$carta['id'].'", "'.$PoSeccion['value'].'", "'.$chk.'")');
        }
        return "OK";
    }

    public function AsociaLugarToInegi(Request $request){
        $lugar = json_decode($request->lugar);
        $inegi = json_decode($request->inegi);

        
        $idLugar = $lugar->id;
        $localidadINEGI = $inegi->Id;

        $LcCad = "insert into rel_lugar_inegi (idLugar, idInegi) values
        ('" . $idLugar . "', '" . $localidadINEGI . "') ";
        DB::select($LcCad);
        return ("OK");
    }

    public function InegiAsociadoA($id){
        $LcCad = "select T2.* from 
        rel_lugar_inegi as T1
        left join loc_inegi_csv as T2 on T1.idInegi = T2.Id
        where T1.idLugar = ".$id;
        $LcResp = DB::select($LcCad);
        return $LcResp;
    }


    public function uneLugares(Request $request){
        $PaLugares = $request->lugares;
        $cLugarConservar = "";
        for($i=0; $i<count($PaLugares); $i++){
            if($PaLugares[$i]['id'] == $request->idConservar){
                $cLugarConservar = $PaLugares[$i];
            }
        }
        foreach($PaLugares as $lugar){

        }
    }


    public function cboTipoElementos(){
        $LcResp = DB::table('cat_tipoelemento')->get();
        return $LcResp;
    }

    public function cboTipoLugares(){
        $LcResp = DB::table('cat_tipolugar')->get();
        return $LcResp;
    }

    public function cboOtrosNombres($id){
        $LcResp = DB::table('cat_othernames');
        if($id){
            $LcResp = $LcResp->leftJoin('rel_lugar_othername', 'cat_othernames.idName', 'rel_lugar_othername.idOtherName')
            ->where('rel_lugar_othername', 'idLugar');
        }
        $LcResp = $LcResp->orderBy('cat_othernames.cName')->get();
        return $LcResp;
    }

    public function cboReferencias($id){
        $LcResp = DB::table('cat_referencias');
        if($id){
            $LcResp = $LcResp->leftJoin('rel_lugar_referencia', 'cat_referencias.idReferencia', 'rel_lugar_referencia.idReferencia')
            ->where('rel_lugarothername', 'idLugar');
        }
        $LcResp = $LcResp->orderBy('cat_referencias.cNombre')->get();
        return $LcResp;
    }

    public function cboRelacionesDe($id){
        $LcResp = DB::table('cat_othernames');
        if($id){
            $LcResp = $LcResp->leftJoin('rel_lugar_othername', 'cat_othernames.idName', 'rel_lugar_othername.idOtherName')
            ->where('rel_lugar_othername', 'idLugar');
        }
        $LcResp = $LcResp->orderBy('cat_othernames.cName')->get();
        return $LcResp;
    }






}
