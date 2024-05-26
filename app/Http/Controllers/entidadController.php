<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Entidades\Entidad;
use App\Models\Entidades\EntidadJson;

class entidadController extends Controller
{
    //public $dirBase = "/var/www/adminRelacionesGeo/public";
    public $dirBase = "/media/compartida/app/public";

    public function verEntidades(){
        $target_dir = $this->dirBase."/archentidades";
        //$contenido1 = file_get_contents($target_dir."/"."e_1.json", "r");
        //$contenido2 = file_get_contents($target_dir."/"."e_2.json", "r");
        //$contenido3 = file_get_contents($target_dir."/"."e_3.json", "r");
        return Entidad::all();
        /*return response()->json([
            '0' => $contenido1,
            '1' => $contenido2,
            '2' => $contenido3,
        ]);
        return $contenido;*/
        
    }


    public function verJson(Request $request){
        //$LcResp = EntidadJson::where('idEntidadJson', $request->idJson)->get();
        $target_dir = $this->dirBase."/archentidades";
        $contenido = file_get_contents($target_dir."/".$request->cJson.".json", "r");
        return $contenido;
    }

    public function listAsociar(){
        $items = Entidad::all();
        $documents = array();
        $target_dir = $this->dirBase."/archentidades";
        
        foreach($items as $item){
            $contenido = file_get_contents($target_dir."/".$item->cClave.".json", "r");
            $json = json_decode($contenido);
            foreach($json as $word){
                $documents.push($word->document);
            }
            //return $contenido;
            
        }
        return $documents;
    }

    public function newEntidad(Request $request){
        $PcArchivo = $_FILES;
        $PcClave = $request->cClave;
        $PcNombre = $request->cNombre;
        
        try {
            //OBTIENE LOS PARAMETROS DE ENTRADA DEL ARCHIVO A SUBIR.
            //public $dirBase = "/var/www/adminRelacionesGeo/public";public $dirBase = "/media/compartida/app/public";
            $target_dir = $this->dirBase."/archentidades";
            $LcArchivo = $PcArchivo["file"]["name"];
            $extension = strtolower(substr($LcArchivo, -4));
            if($extension!="json"){
                return 0;
            }
            $mydate=getdate();
            $date=$mydate['mday']."-".$mydate['mon']."-".$mydate['year'];
            //PROCEDE A SUBIRLO.
            
            if(move_uploaded_file($PcArchivo['file']['tmp_name'], $target_dir."/".$PcClave."txt")){              
                //$contenido = file_get_contents($target_dir."/".$PcClave."-".$date.".txt", "r");		
                /*$entidad = EntidadJson::create([
                    'cContenido' => $contenido,
                    'idUsrAlta' => Auth::id()
                ]);*/
                $new = Entidad::create([
                    'cClave' => $PcClave,
                    'cNombre' => $PcNombre,
                    'cJson' => $PcClave,
                    'idUsrAlta' => Auth::id()
                ]);
                $LcResp = "OK";
                return $LcResp;
            } else {			
                $LcResp = "ERRORSUBIDA";
                return $LcResp;
            }	       
        } catch(Exception $e){
            $LcResp = "ERROR";
            return $LcResp;
        }
        
    }
}
