<?php

namespace App\Http\Controllers;

use App\Models\Catalogos\Othername;
use App\Models\Catalogos\Referencia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\relaciones\lugarDS;
use App\Models\relaciones\lugarOthername;
use App\Models\relaciones\lugarPag;
use App\Models\relaciones\lugarRef;
use App\Models\lugar;
use App\Models\relaciones\elementoCapa;
use App\Models\relaciones\ElementoHistorial;
use App\Models\relaciones\lugarIndice;

class elementosController extends Controller
{
    public function cbosElementosDlg(){
        $tipoElementos = DB::table('cat_tipoelemento')->get();
        $tipoLugar = DB::table('cat_tipolugar')->get();
    }


    public function fnHistorialDe($id){
        $LcResp = ElementoHistorial::select([
            'historial_elementos.*',
            'T2.name as user'
        ]) 
        ->leftJoin('users as T2', 'T2.id', 'historial_elementos.idUsrAlta')
        ->where('idElemento', $id)->orderBy('created_at')->get();
        return $LcResp;
    }

    public function AddOtherName(Request $request){
        $new = Othername::create([
            'cName' => $request->cNombre,
            'idUsrAlta' => Auth::id()
        ]);
        return $new;
    }

    public function AddReferencia(Request $request){
        $new = Referencia ::create([
            'cNombre' => $request->nombre,
            'cURL' => $request->url,
            'idUsrAlta' => Auth::id()
        ]);
        return $new;
    }


    public function fnAsociarARelacion(Request $request){
        $items = $request->items;
        foreach($items as $item){
            lugarDS::create([
                'idLugar' => $item["id"],
                'idDS' => $request->relacion["value"],
                'idUsrAlta' => Auth::id()
            ]);
        }
        return "OK";
    }

    

    public function fnCombineElementos(Request $request){
        $PoConservar = $request->oConservar;
        $PoItems = $request->oItems;

        foreach($PoItems as $item){ 
            if($item['idTipoElemento'] != 1){ 
                return ("Solo puede combinar elementos de punto");
            }
        }

        $cItems = "";

        $relaciones = "";
        $paginas = "";
        $referencias = "";
        $oNombres = "";
        $capas = "";
        $indice = "";
        

        $relacionesB = "";
        $paginasB = "";
        $referenciasB = "";
        $oNombresB = "";
        $capasB = "";
        $indiceB = "";
       
        foreach($PoItems as $item){
            
            if($item['id'] != $PoConservar['id']){
                $cRelaciones = DB::table('rel_lugar_document_section')->where('idLugar', $item['id'])->get();
                
                if($cRelaciones != '[]'){
                    foreach($cRelaciones as $relacion){
                        $relaciones = $relaciones.$relacion->idDS.",";
                    }
                }

                $cIndice = DB::table('rel_lugar_indice')->where('idLugar', $item['id'])->get();
                
                if($cIndice != '[]'){
                    foreach($cIndice as $indi){
                        
                        $indice = $indice.$indi->idIndice.",";
                    }
                }
                
                
                $cPaginas = DB::table('rel_lugares_paginas')->where('idLugar', $item['id'])->get();
                if($cPaginas != '[]'){
                    foreach($cPaginas as $pagina){
                        $paginas = $paginas.$pagina->nPag.":".$pagina->idRelacion.",";
                    }
                }

                $cReferencias = DB::table('rel_lugar_referencia')->where('idLugar', $item['id'])->get();
                if($cReferencias != '[]'){
                    foreach($cReferencias as $ref){
                        $referencias = $referencias.$ref->idReferencia.",";
                    }
                }

                $cNombres = DB::table('rel_lugar_othername')->where('idLugar', $item['id'])->get();
                if($cNombres != '[]'){
                    foreach($cNombres as $nombre){
                        $oNombres = $oNombres.$nombre->idOtherName.",";
                    }
                }

                $cCapas = DB::table('capa_rel_lugar')->where('idLugar', $item['id'])->get();
                if($cCapas != '[]'){
                    foreach($cCapas as $capa){
                        $capas = $capas.$capa->idCapa.",";
                    }
                }

                lugar::where('id', $item['id'])->update([
                    'cEstatusFinal' => 'B'
                ]);

                $cItems = $cItems.$item['id'].',';

                ElementoHistorial::create([
                    'idElemento' => $item['id'],
                    'cAccion' => 'Combinar',
                    'cDescripcion' => 'El elemento se combinó con: '.$PoConservar['Placename'].' con ID número '.$PoConservar['id'].'. Y este fue dado de baja.',
                    'idUsrAlta' => Auth::id()
                ]);
                
            } else {
                $cRelaciones = DB::table('rel_lugar_document_section')->where('idLugar', $item['id'])->get();
                
                if($cRelaciones != '[]'){
                    foreach($cRelaciones as $relacion){
                        $relacionesB = $relacionesB.$relacion->idDS.",";
                    }
                }
                
                $cIndice = DB::table('rel_lugar_indice')->where('idLugar', $item['id'])->get();
                if($cIndice != '[]'){
                    foreach($cIndice as $indi){
                       
                        $indiceB = $indiceB.$indi->idIndice.",";
                    }
                }
                
                $cPaginas = DB::table('rel_lugares_paginas')->where('idLugar', $item['id'])->get();
                if($cPaginas != '[]'){
                    foreach($cPaginas as $pagina){
                        $paginasB = $paginasB.$pagina->nPag.":".$pagina->idRelacion.",";
                    }
                }

                $cReferencias = DB::table('rel_lugar_referencia')->where('idLugar', $item['id'])->get();
                if($cReferencias != '[]'){
                    foreach($cReferencias as $ref){
                        $referenciasB = $referenciasB.$ref->idReferencia.",";
                    }
                }

                $cNombres = DB::table('rel_lugar_othername')->where('idLugar', $item['id'])->get();
                if($cNombres != '[]'){
                    foreach($cNombres as $nombre){
                        $oNombresB = $oNombresB.$nombre->idOtherName.",";
                    }
                }

                $cCapas = DB::table('capa_rel_lugar')->where('idLugar', $item['id'])->get();
                if($cCapas != '[]'){
                    foreach($cCapas as $capa){
                        $capasB = $capasB.$capa->idCapa.",";
                    }
                }



                
            }

        }

        if($relaciones != ""){$relaciones = substr($relaciones, 0, -1);$relaciones = explode(",", $relaciones); $relaciones = array_unique($relaciones, SORT_NUMERIC);};
        if($indice != ""){$indice = substr($indice, 0, -1);$indice = explode(",", $indice); $indice = array_unique($indice, SORT_NUMERIC);};
        if($paginas != ""){$paginas = substr($paginas, 0, -1);$paginas = explode(",", $paginas);$paginas = array_unique($paginas, SORT_NUMERIC);};
        if($referencias != ""){$referencias = substr($referencias, 0, -1);$referencias = explode(",", $referencias);$referencias = array_unique($referencias, SORT_NUMERIC);};
        if($oNombres != ""){$oNombres = substr($oNombres, 0, -1);$oNombres = explode(",", $oNombres);$oNombres = array_unique($oNombres, SORT_NUMERIC);};
        if($capas != ""){$capas = substr($capas, 0, -1);$capas = explode(",", $capas);$capas = array_unique($capas, SORT_NUMERIC);};

        if($relacionesB != ""){$relacionesB = substr($relacionesB, 0, -1);$relacionesB = explode(",", $relacionesB);$relacionesB = array_unique($relacionesB, SORT_NUMERIC);};
        if($indiceB != ""){$indiceB = substr($indiceB, 0, -1);$indiceB = explode(",", $indiceB); $indiceB = array_unique($indiceB, SORT_NUMERIC);};
        if($paginasB != ""){$paginasB = substr($paginasB, 0, -1);$paginasB = explode(",", $paginasB);$paginasB = array_unique($paginasB, SORT_NUMERIC);};
        if($referenciasB != ""){$referenciasB = substr($referenciasB, 0, -1);$referenciasB = explode(",", $referenciasB);$referenciasB = array_unique($referenciasB, SORT_NUMERIC);};
        if($oNombresB != ""){$oNombresB = substr($oNombresB, 0, -1);$oNombresB = explode(",", $oNombresB);$oNombresB = array_unique($oNombresB, SORT_NUMERIC);};
        if($capasB != ""){$capasB = substr($capasB, 0, -1);$capasB = explode(",", $capasB);$capasB = array_unique($capasB, SORT_NUMERIC);};

        $cItems = substr($cItems, 0, -1);
        ElementoHistorial::create([
            'idElemento' => $PoConservar['id'],
            'cAccion' => 'Combinar',
            'cDescripcion' => 'El elemento se combinó con '.(count($PoItems)-1).' elementos con los ID: '.$cItems.'. Este elemento quedó como principal.',
            'idUsrAlta' => Auth::id()
        ]);

        if($relaciones){
            foreach($relaciones as $relacion){
                if(!$relacionesB){
                    $existe = false;
                } else {
                    $existe = array_search($relacion, $relacionesB);
                }
                
                if($existe === false){
                    echo("Se asocia->".$relacion."<br>");
                    lugarDS::create([
                        'idLugar' => $PoConservar['id'],
                        'idDS' => $relacion,
                        'idUsrAlta' => Auth::id()
                    ]);
                    
                }
            }
        }

        if($indice){
            foreach($indice as $indi){
                if(!$indiceB){
                    $existe = false;
                } else {
                    $existe = array_search($indi, $indiceB);
                }
                
                if($existe === false){
                    
                    lugarIndice::create([
                        'idLugar' => $PoConservar['id'],
                        'idIndice' => $indi,
                    ]);
                    
                }
            }
        }

        if($referencias){
            foreach($referencias as $ref){
                if(!$referenciasB){
                    $existe = false;
                } else {
                    $existe = array_search($ref, $referenciasB);
                }
                
                if($existe === false){
                    echo("Se asocia->".$ref."<br>");
                    lugarRef::create([
                        'idLugar' => $PoConservar['id'],
                        'idReferencia' => $ref,
                    ]);
                    
                }
            }
        }

        if($oNombres){
            foreach($oNombres as $name){
                if(!$oNombresB){
                    $existe = false;
                } else {
                    $existe = array_search($name, $oNombresB);
                }
                if($existe === false){
                    echo("Se asocia->".$name."<br>");
                    lugarOthername::create([
                        'idLugar' => $PoConservar['id'],
                        'idOtherName' => $name,
                    ]);
                    
                }
            }
        }

        if($capas){
            foreach($capas as $capa){
                if(!$capasB){
                    $existe=false;
                } else {
                    $existe = array_search($capa, $capasB);
                }
            
                if($existe === false){
                    echo("Se asocia->".$capa."<br>");
                    elementoCapa::create([
                        'idLugar' => $PoConservar['id'],
                        'idCapa' => $capa,
                        'idUsrAlta' => Auth::id()
                    ]);
                    
                }
            }
        }

        if($paginas){
            foreach($paginas as $pag){
                if(!$paginasB){
                    $existe=false;
                } else {
                    $existe = array_search($pag, $paginasB);
                }
                
                
                
                if($existe === false){
                    $pagFinal = explode(":", $pag);
                    echo("Se asocia->".$pagFinal[0]."<br>");
                    lugarPag::create([
                        'idLugar' => $PoConservar['id'],
                        'nPag' => $pagFinal[0],
                        'idRelacion' => $pagFinal[1],
                        'idUsrAlta' => Auth::id()
                    ]);   
                }
            }
        }
       
        return "OK";
        
        return response()->json([
            "relaciones" => $relaciones,
            "paginas" => $paginas,
            "referencias" => $referencias,
            "oNombres" => $oNombres,
            "relacionesB" => $relacionesB,
            "paginasB" => $paginasB,
            "referenciasB" => $referenciasB,
            "oNombresB" => $oNombresB,
            "capas" => $capas,
            "capasB" => $capasB
        ]);
        
    }

    
}
