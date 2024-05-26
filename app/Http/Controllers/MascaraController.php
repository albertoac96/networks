<?php

namespace App\Http\Controllers;

use App\Models\Mascaras\Grupos;
use App\Models\Mascaras\Imagen;
use App\Models\Mascaras\Mascara;
use App\Models\Mascaras\Modelo;
use App\Models\Mascaras\RelGpoModel;
use App\Models\Mascaras\RelMascGpo;
use App\Models\Mascaras\RelMascImg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MascaraController extends Controller
{
   //MASCARAS
    public function showMascaras(Request $request){
        $mascaras = Mascara::where('cEstatus', 'A');
        if($request->grupo){
            $mascaras = $mascaras->leftJoin('masc_rel_masc_gpo', 'masc_rel_masc_gpo.idMascara', 'masc_cat_mascaras.idMascara')
            ->where('masc_rel_masc_gpo.idGrupo', $request->grupo);
        }
        
        $mascaras = $mascaras->get();
        $LcResp = '[';
        $masc = "";
        $i=0;
        foreach($mascaras as $mascara){
            $masc = $masc.'{
            "idMascara": "'.$mascara->idMascara.'",
            "cClave": "'.$mascara->cClave.'",
            "cEtiqueta": "'.$mascara->cEtiqueta.'",
            "cGrupo": "'.$mascara->cGrupo.'",
            "cColumna1": "'.$mascara->cColumna1.'",
            "img": [';

    
            $imagenes = RelMascImg::select(
                'T2.*'
            )
            ->leftJoin('masc_cat_imagenes as T2', 'masc_rel_masc_img.idImagen', 'T2.idImagen')
            ->where('masc_rel_masc_img.idMascara', $mascara->idMascara)
            ->where('T2.cEstatus', 'A')
            ->get();

           
            if($imagenes=='[]'){
                $masc = $masc.'],';
            } else {
                foreach($imagenes as $img){
                    $iImg = 0;
                    $masc = $masc.'{
                    "idImagen": "'.$img->idImagen.'",
                    "cNombre": "'.$img->cNombre.'"},';
                }
                $masc = substr($masc, 0, -1);
                $masc = $masc.'],';
            }
            $masc = $masc.'"grupos": [';

            $grupos = RelMascGpo::select(
                'T2.*', 'T3.cNombre as cModelo'
            )
            ->leftJoin('masc_cat_grupos as T2', 'masc_rel_masc_gpo.idGrupo', 'T2.idGrupo')
            ->where('masc_rel_masc_gpo.idMascara', $mascara->idMascara)
            ->leftJoin('masc_rel_gpo_model', 'masc_rel_gpo_model.idGrupo', 'T2.idGrupo')
            ->leftJoin('masc_cat_modelos as T3', 'masc_rel_gpo_model.idModelo', 'T3.idModelo')
            ->where('T2.cEstatus', 'A')
            ->get();
            if($grupos=='[]'){
                $masc = $masc.']';
            } else {
                foreach($grupos as $gpo){
                    $iGrupo = 0;
                    $masc = $masc.'{
                    "idGrupo": "'.$gpo->idGrupo.'",
                    "cModelo": "'.$gpo->cModelo.'",
                    "cNombre": "'.$gpo->cNombre.'"},';
                }
                $masc = substr($masc, 0, -1);
                $masc = $masc.']';
            }

            $masc = $masc.'},';
            
            
        }

        $masc = substr($masc, 0, -1);
        $LcResp = $LcResp.$masc.']';

        return $LcResp;

        
        
    }
    
    public function newMascara(Request $request){

    }

    public function editMascara(Request $request){

    }

    public function bajaMascara(Request $request){

    }

   //IMAGENES
    public function newImagen(Request $request){

    }

    public function bajaImagen($idImagen){
        Imagen::where('idImagen', $idImagen)->update([
            'cEstatus' => 'B'
        ]);
        return "OK";
    }

    //GRUPOS
    public function showGrupos(){
        $LcResp = Grupos::select(
            'masc_cat_grupos.*',
            'T2.cNombre as cModelo'
        )
        ->leftJoin('masc_rel_gpo_model', 'masc_rel_gpo_model.idGrupo', 'masc_cat_grupos.idGrupo')
        ->leftJoin('masc_cat_modelos as T2', 'masc_rel_gpo_model.idModelo', 'T2.idModelo')
        ->get();

        return $LcResp;
    }

    public function relGrupoAMascara(Request $request){
        $new = RelMascGpo::create([
            'idMascara' => $request->item['idMascara'],
            'idUsrAlta' => Auth::id(),
            'idGrupo' => $request->grupo['idGrupo']
        ]);
        return $new;
    }

    public function ModeloDeGrupo($idGrupo){
        $LcResp = Grupos::select(
            'T2.cNombre as cModelo'
        )
        ->leftJoin('masc_rel_gpo_model', 'masc_rel_gpo_model.idGrupo', 'masc_cat_grupos.idGrupo')
        ->leftJoin('masc_cat_modelos as T2', 'masc_rel_gpo_model.idModelo', 'T2.idModelo')
        ->where('masc_rel_gpo_model.idGrupo', $idGrupo)
        ->get();

        return $LcResp[0]->cModelo;
    }

    public function bajaRelGrupoAMascara(Request $request){
        RelMascGpo::where('idMascara', $request->idMascara)->where('idGrupo', $request->idGrupo)->delete();
        return "OK";
    }

    public function newGrupo(Request $request){
        $new = Grupos::create([
            'cNombre' => $request->cNombre,
            'idUsrAlta' => Auth::id(),
            'cEstatus' => 'A'
        ]);

        RelGpoModel::create([
            'idGrupo' => $new->idGrupo,
            'idUsrAlta' => Auth::id(),
            'idModelo' => $request->oModelo['idModelo']
        ]);

        $LcResp = Grupos::select(
            'masc_cat_grupos.*',
            'T2.cNombre as cModelo'
        )
        ->leftJoin('masc_rel_gpo_model', 'masc_rel_gpo_model.idGrupo', 'masc_cat_grupos.idGrupo')
        ->leftJoin('masc_cat_modelos as T2', 'masc_rel_gpo_model.idModelo', 'T2.idModelo')
        ->where('masc_cat_grupos.idGrupo', $new->idGrupo)
        ->get();

        return $LcResp;
    }

    public function bajaGrupo(Request $request){

    }

    //MODELOS
    public function showModelos(){
        $LcResp = Modelo::where('cEstatus', 'A')->get();
        return $LcResp;
    }

    public function newModelo(Request $request){
        $new = Modelo::create([
            'cNombre' => $request->cNombre,
            'idUsrAlta' => Auth::id(),
            'cEstatus' => 'A'
        ]);

        return $new;
    }

    public function bajaModelo(Request $request){

    }

    public function uploadArchivo(Request $request){
        $PcTipo = $request->tipo;
        $Pid = $request->id;
        $Archivo = $_FILES;
        $LcNombre = $Archivo["archivo"]["name"];

        $cURL = strtolower($LcNombre);
        $pos = strrpos($cURL, ".");
        $ext = substr($cURL, $pos+1);

        if($PcTipo == 'imagen'){
            $target_dir = public_path('/mascarasfiles/imagenes');
            $new = Imagen::create([
                'cNombre' => $LcNombre,
                'idUsrAlta' => Auth::id(),
                'cEstatus' => 'A'
            ]);
            RelMascImg::create([
                'idMascara' => $Pid,
                'idUsrAlta' => Auth::id(),
                'idImagen' => $new->idImagen
            ]);
            $PidItem = $new->idImagen;
        } else {
            $target_dir = public_path('/mascarasfiles/modelos');
        }

        

      
            //OBTIENE LOS PARAMETROS DE ENTRADA DEL ARCHIVO A SUBIR.
            


                //PROCEDE A SUBIRLO.		
                if(move_uploaded_file($Archivo['archivo']['tmp_name'], $target_dir."/".$Pid."-".$PidItem.'.'.$ext)){
                        
                        $LcResp = "OK";
                       
                        
                        //$this->RegistraCambio('Se asoció un archivo', $idDocumento, 'Se asoció el archivo: ' + $LcArchivo);
                        return $LcResp;
                        
                    
                        
                } else {
                    if($PcTipo == 'imagen'){
                            
                        Imagen::where('idImagen', $PidItem)->update([
                            'cEstatus' => 'E'
                        ]);
                        
                    } 			
                    return 'ERROR';
                }	

          
                
              
                
       
        
    }


    public function relmascaramodelo(){
        /*$mascaras = Mascara::all();
        foreach($mascaras as $mascara){
            $grupo = Grupos::where('cNombre', $mascara->cGrupo)->get();
            RelMascGpo::create([
                'idMascara' => $mascara->idMascara,
                'idUsrAlta' => Auth::id(),
                'idGrupo' => $grupo[0]->idGrupo
            ]);
        }*/
        /*$grupos = Grupos::all();
        foreach($grupos as $grupo){
            RelGpoModel::create([
                'idGrupo' => $grupo->idGrupo,
                'idUsrAlta' => Auth::id(),
                'idModelo' => '1'
            ]);
        }*/

        $mascaras = Mascara::all();
        $i = 0;
        $dir_imagenes = public_path('/mascarasfiles/imagenes');
        $dir_pendiente = public_path('/mascarasfiles/fotos');
        foreach($mascaras as $mascara){
            $clave = str_replace("0","",$mascara->cClave);
            $clave = str_replace(".","_",$clave);
            $aClave = explode("_",$clave);
            $LcCad = 'select * from masc_cat_imagenes_virtual where cNombre LIKE "%'.$aClave[1].'%"';
            $items = DB::select($LcCad);
           
            if(count($items)>0){
                foreach($items as $item){
                    $cName = $item->cNombre;
                    $cName = substr($cName, 0, -4);
                    $cName = str_replace("0","",$cName);
                    $cName = str_replace(".","_",$cName);
                    if($clave == $cName){
                        
                        echo('Existe '.$mascara->cNombre.' - '.$mascara->idMascara.' --> '.$item->id.' - '.$cName.'<br>');
                        $i++;
                        $new = Imagen::create([
                            'cNombre' => $item->cNombre,
                            'idUsrAlta' => Auth::id(),
                            'cEstatus' => 'A'
                        ]);
                        RelMascImg::create([
                            'idMascara' => $mascara->idMascara,
                            'idUsrAlta' => Auth::id(),
                            'idImagen' => $new->idImagen
                        ]);
                        $PidItem = $new->idImagen;
                        if(file_exists($dir_pendiente."/".$item->cNombre)) {
                            rename ($dir_pendiente."/".$item->cNombre, $dir_imagenes."/".$mascara->idMascara."-".$PidItem.'.jpg');
                        }
                        
                        break;
                    }
                }
            } else {
                
            }
          
        }
        return $i;
        
    }
}
