<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LocInegi;
use App\Models\Carta;
use App\Models\lugar;
use App\Models\Relacion;
use App\Models\relaciones\lugarPag;
use App\Models\relaciones\lugarDS;
use App\Models\relaciones\lugarIndice;

class ordenaController extends Controller
{
    
    //TABLA DE MARIANA ROJOS Y AMARILLOS
    public function separaMariana()
    {
        $LcCad = "select * from lugares_mariana";
        $lugares = DB::select($LcCad);
        
        foreach ($lugares as $lugar) {

            /*$LcCad = "insert into lugares (Placename, Toponym_Pages, idArchivoExcel, cEstatus) values
                ('".$lugar->Toponimo."', '".$lugar->Paginas."', '1', '".$lugar->cEstatus."') ";
                DB::select($LcCad);*/

            $pag = $lugar->Paginas;
            $pag = str_replace(" ", "", $pag);

            // echo $pag."<br>";

            $aPag = explode(",", $pag);

            //Separar a la tabla de paginas 
            for ($i = 0; $i < count($aPag); $i++) {
                $pagina = $aPag[$i];
                $existe = strripos($pagina, "-");
                //Si hay un guion en la pagina es un intervalo
                if ($existe > 0) {
                    $inicio = substr($pagina, 0, $existe);
                    $fin = substr($pagina, $existe + 1, 3);
                    echo $lugar->id . "<br>";
                    for ($inicio; $inicio <= $fin; $inicio++) {
                        $LcCad = "insert into rel_lugares_paginas (idLugar, nPag) values
                            ('" . $lugar->id . "', '" . $inicio . "') ";
                        DB::select($LcCad);
                    }
                } else {
                    $LcCad = "insert into rel_lugares_paginas (idLugar, nPag) values
                            ('" . $lugar->id . "', '" . $pagina . "') ";
                    DB::select($LcCad);
                }
            }
        }

        echo "ECHO";
    }

    public function relDeOtros(){
        $LcCad = "select * from lugares where idArchivoExcel = 6 and cEstatus != 'A'";
        $lugares = DB::select($LcCad);
        

        $LcCad = "select * from indice_document_sections where idArchivo = 6";
        $indice = DB::select($LcCad);

        foreach ($lugares as $lugar) {
            $LcCad = "select * from rel_lugares_paginas where idLugar = " . $lugar->id;
            $paginas = DB::select($LcCad);
            //return $paginas;

            if (count($paginas) > 0) {
                //echo "IDLugar: ".$lugar->id."<br>";
                $cRelaciones = "";
                //return $paginas;

                for ($i = 0; $i < count($paginas); $i++) {
                    $vacio = 0;
                    $pag = $paginas[$i]->nPag;
                    //echo "Pagina: ".$pag."<br>";
                    //echo count($paginas);


                    for ($e = 0; $e < count($indice); $e++) {
                        $inicio = $indice[$e]->Inicio;
                        $fin = $indice[$e]->Fin;
                        //echo $inicio."<br>";
                        //echo $fin."<br>";
                        if ($pag >= $inicio && $pag <= $fin) {
                            $cRelaciones = $cRelaciones . "," . $indice[$e]->idDocumentSection;
                            //echo "OK".$indice[$e]->idDocumentSection;
                        } else {
                            //echo "NO";
                        }
                        //echo "<br><br><br>";
                    }
                }
                $cRelaciones = substr($cRelaciones, 1);
                    echo $cRelaciones;
                    $aRel = explode(",", $cRelaciones);
                    //return $aRel;
                    $aRel = array_unique($aRel);

                    $keys = array_keys($aRel);

                    for ($a = 0; $a < count($keys); $a++) {

                        $idDS = $aRel[$keys[$a]];
                        echo $idDS . "<br>";
                        $LcCad = "insert into rel_lugar_document_section (idLugar, idDS) values
                        (".$lugar->id.", ".$aRel[$keys[$a]].") ";
                        DB::select($LcCad);
                    }
                    echo "<br><br><br>";
            }
        }
    }



public function ligaIndiceConDocumentSection(){
    $DS = DB::select('select * from document_sections where idArchivoXLS = 10');
    foreach($DS as $ds){
        DB::update('update indice_document_sections set idDocumentSection = '.$ds->idDS.' where Documento = "'.$ds->cNombre.'" and idArchivo = 10');
    }
    return "OK";
}











public function comparaLugares(){
    $idArchivo = 10;

    $LcCad = "select * from lugares_mariana where My_FID > 0 group by My_FID";
    $lugares = DB::select($LcCad);
    foreach($lugares as $lugar){
        echo $lugar->id."-".$lugar->Toponimo."<br>";
        $LcCad = "select * from lugares_mariana where My_FID = ".$lugar->My_FID;
        $variantes = DB::select($LcCad);
        $paginas = $this->separaPaginas($variantes);
       
        $lugarOK = DB::select("select * from lugares where My_FID7 = ".$lugar->My_FID." and idArchivoExcel =".$idArchivo);
        if($lugarOK == []){
            $this->creaLugar($lugar, $idArchivo, 'Y');
        }
        $lugarOK = DB::select("select * from lugares where My_FID7 = ".$lugar->My_FID." and idArchivoExcel =".$idArchivo);
        //echo $lugar->id;
        if($paginas != 0){
            $this->asociaPaginas($paginas, $lugarOK[0]);
            $this->relPaginaRelacion($lugarOK[0], $idArchivo);
        }
       
        

    }

   

    $LcCad = "select * from lugares_mariana where My_FID = 0";
    $lugares = DB::select($LcCad);
    

    foreach($lugares as $lugar){
        $LcCad = "select * from lugares_mariana where Toponimo = '".$lugar->Toponimo."'";
        $variantes = DB::select($LcCad);
        
        $paginas = $this->separaPaginas($variantes);
       
            $existe = DB::select("select * from lugares where Placename = '".$lugar->Toponimo."' and idArchivoExcel =".$idArchivo);
            if($existe == []){
                $this->creaLugar($lugar, $idArchivo, $lugar->cEstatus);
               
            }          
            
            $lugarOK = DB::select("select * from lugares where Placename = '".$lugar->Toponimo."' and idArchivoExcel =".$idArchivo);
            
       
            if($paginas != 0){
                $this->asociaPaginas($paginas, $lugarOK[0]);
                $this->relPaginaRelacion($lugarOK[0], $idArchivo);
            }
       

    }

    return "OK";
    

}

public function creaLugar($lugar, $idArchivo, $cEstatus){
   
    $new = lugar::insert([
        'Placename' => $lugar->Toponimo,
        'My_FID7' => $lugar->My_FID,
        'Toponym_Reference' => $lugar->Referencia,
        'Toponym_Pages' => $lugar->Paginas,
        'idArchivoExcel' => $idArchivo,
        'cEstatus' => $cEstatus
    ]);

    return $new;;
}

public function separaPaginas($variantes){
    $cPaginas = "";
    foreach($variantes as $variante){
        $paginas = $variante->Paginas;
        $paginas = str_replace(" ", "", $paginas);
        if($paginas != ''){
            $cPaginas = $cPaginas.$paginas.",";
            //array_push($cResp, $item[$clave]);
        }
       
    }
    if($cPaginas == ''){
        return 0;
    }
    $cPaginas = substr($cPaginas, 0, -1);
    $aPag = explode(",", $cPaginas);
    $cPaginas = "";
    for ($i = 0; $i < count($aPag); $i++) {
        $pagina = $aPag[$i];
        $existe = strripos($pagina, "-");
        if ($existe > 0) {
            $inicio = substr($pagina, 0, $existe);
            $fin = substr($pagina, $existe + 1, 3);
            //echo $inicio."<br>";
            //echo $fin."<br><br>";
            for($inicio; $inicio<$fin+1; $inicio++){
                $cPaginas = $cPaginas.$inicio.",";
            }
        } else {
           $cPaginas = $cPaginas.$pagina.",";
        }
    }
    $cPaginas = substr($cPaginas, 0, -1);
    $aPag = explode(",", $cPaginas);
    $aPag = array_unique($aPag, SORT_NUMERIC);
    return $aPag;
}


public function asociaPaginas($paginas, $lugar){
    
    $keys = array_keys($paginas);
    foreach($keys as $key){
        //echo $paginas[$key]."<br>";
        $existe = DB::select("select * from rel_lugares_paginas where idLugar =".$lugar->id." and nPag = ".$paginas[$key]);
        
        if($existe == [] || count($existe) == 0){
            $LcCad = "insert into rel_lugares_paginas (idLugar, nPag) values
            ('" . $lugar->id . "', '" . $paginas[$key] . "') ";
            DB::select($LcCad);
            
        }
       
    }
    
}

public function relPaginaRelacion($lugar, $idArchivo){
    $LcCad = "select * from indice_document_sections where idArchivo = ".$idArchivo;
    $indice = DB::select($LcCad);

    $LcCad = "select * from rel_lugares_paginas where idLugar = " . $lugar->id;
    $paginas = DB::select($LcCad);

    if (count($paginas) > 0) {
        //echo "IDLugar: ".$lugar->id."<br>";
        $cRelaciones = "";
        //return $paginas;

        for ($i = 0; $i < count($paginas); $i++) {
            $vacio = 0;
            $pag = $paginas[$i]->nPag;
            //echo "Pagina: ".$pag."<br>";
            //echo count($paginas);


            for ($e = 0; $e < count($indice); $e++) {
                $inicio = $indice[$e]->Inicio;
                $fin = $indice[$e]->Fin;
                //echo $inicio."<br>";
                //echo $fin."<br>";
                if ($pag >= $inicio && $pag <= $fin) {
                    $cRelaciones = $cRelaciones . "," . $indice[$e]->idDocumentSection;
                    //echo "OK".$indice[$e]->idDocumentSection;
                } else {
                    //echo "NO";
                }
                //echo "<br><br><br>";
            }
        }
        $cRelaciones = substr($cRelaciones, 1);
            //echo $cRelaciones;
            $aRel = explode(",", $cRelaciones);
            //return $aRel;
            $aRel = array_unique($aRel);

            $keys = array_keys($aRel);

            for ($a = 0; $a < count($keys); $a++) {

                $idDS = $aRel[$keys[$a]];
                if($aRel[$keys[$a]]){
                    $existe = DB::select("select * from rel_lugar_document_section where idLugar = ".$lugar->id." and idDS =".$aRel[$keys[$a]]);
                    if($existe == []){
                        $LcCad = "insert into rel_lugar_document_section (idLugar, idDS) values
                        (".$lugar->id.", ".$aRel[$keys[$a]].") ";
                        DB::select($LcCad);
                    }
                }
                //echo $idDS . "<br>";
               
                
            }
            
    }

    return "OK";
}


public function archivos(){
   
        $lugares = DB::select("select * from lugares where Toponym_Pages != ''");
        foreach($lugares as $lugar){
        echo $lugar->id."<br>";
           $result = $this->separaPaginasIndividual($lugar);
          
        }
    
}
public function separaPaginasIndividual($lugar){

    $paginas = $lugar->Toponym_Pages;
    
    if($paginas == '' || $paginas == null){
        return;
    }
    $pag = str_replace(" ", "", $paginas);

    $aPag = explode(",", $pag);
   

    //Separar a la tabla de paginas 
    for ($i = 0; $i < count($aPag); $i++) {
        $pagina = $aPag[$i];
        $existe = strripos($pagina, "-");
        //Si hay un guion en la pagina es un intervalo
        if ($existe > 0) {
            $inicio = substr($pagina, 0, $existe);
            $fin = substr($pagina, $existe + 1, 3);
            echo $lugar->id . "<br>";
            for ($inicio; $inicio <= $fin; $inicio++) {
                $existe = DB::select("select * from rel_lugares_paginas where idLugar =".$lugar->id." and nPag = ".$inicio);
                if($existe == []){
                    $LcCad = "insert into rel_lugares_paginas (idLugar, nPag) values
                    ('" . $lugar->id . "', '" . $inicio . "') ";
                DB::select($LcCad);
                
                }
                
            }
        } else {
            $existe = DB::select("select * from rel_lugares_paginas where idLugar =".$lugar->id." and nPag = ".$pagina);
                if($existe == []){
                    $LcCad = "insert into rel_lugares_paginas (idLugar, nPag) values
                    ('" . $lugar->id . "', '" . $pagina . "') ";
            DB::select($LcCad);
                }
            
        }
    }
}









    public function separaLugares()
    {

        $LcCad = "select * from lugares where idArchivoExcel = 6";
        $lugares = DB::select($LcCad);
        foreach ($lugares as $lugar) {
            $secciones = $lugar->Toponym_Document_Sections;
            $info2 = json_decode($secciones, true);
            $num = count($info2);
            $keys = array_keys($info2);
            foreach ($keys as $key) {
                //echo $key."<br>";  
                $LcCad = "select * from document_sections where cNombre = '" . $key . "'";
                $LcResp = DB::select($LcCad);
                $idDS = $LcResp[0]->idDS;

                $LcCad = "insert into rel_lugar_document_section (idLugar, idDS) values
                (" . $lugar->id . ", " . $idDS . ") ";
                DB::select($LcCad);

               
            }
            //echo "<br><br>";
        }
        return;

        
    }


    public function separaRelaciones(){


        $LcCad = "select * from lugares where idArchivoExcel = 6";
        $lugares = DB::select($LcCad);
        foreach ($lugares as $lugar) {
            $secciones = $lugar->Toponym_Document_Sections;
            $info2 = json_decode($secciones, true);
            $num = count($info2);
            $keys = array_keys($info2);
            foreach ($keys as $key) {
                
                $LcCad = "select * from document_sections where cNombre = '".$key."'";
                $LcResp = DB::select($LcCad);
            
            if($LcResp == []){
                //echo $key."<br>";  
                $LcCad = "insert into document_sections (cNombre, idArchivoXLS) values ('".$key."', 6) ";
                DB::select($LcCad);
            }

               
            }
            //echo "<br><br>";
        }
        return;



        /*$LcCad = "select * from lugares where idArchivoExcel = 2";
        $lugares = DB::select($LcCad);
        //$info = '{"Relación de Mexicaltzingo y su partido": ["Mexicaltzingo", "Iztapalapa", "Culhuacan", "|"], "Relación de la alcaldía mayor de Meztitlan y su jurisdicción": ["Introducción", "Texto", "|"], "Relación de Ocopetlayucan": ["Texto", "|"], "Relación de Quauhquilpan": ["Introducción", "Texto", "|"], "Relación de las minas de Tasco": ["Introducción", "Texto", "|"], "Relación de las minas de Temazcaltepec y Tuzantla": ["Introducción", "|", "Temazcaltepec"], "Relación de Tepeapulco": ["Texto", "|"], "Relación de Tequixquiac y su partido": ["Citlaltepec", "Tequixquiac", "|", "Xilotzingo"], "Relación de Tequizistlan y su partido": ["Tequizistlan", "San Juan Teutihuacan", "Tepexpan", "Aculma", "|"], "Relación de Tetela y Hueyapan": ["Introducción", "Texto", "|"], "Relación de Teutenango": ["Introducción", "Texto", "|"]}';
        //echo $info;
        //$info2 = json_decode($info, true);
        $num = count($lugares);
        return $lugares;


        $keys = array_keys($lugares);
        foreach ($keys as $key) {
            echo $key . "<br>";
            //$LcCad = "select * from document_sections where cNombre =".$key;
           // $LcResp = DB::select($LcCad);
            
            //if($LcResp == []){
                //$LcCad = "insert into document_sections (cNombre) values ('".$key."') ";
                //DB::select($LcCad);
            //}
        }*/
    }







    public function OtherNames(){
        $lugares = DB::select("select * from lugares where Alt_names is not null and Alt_names != ''");
        foreach($lugares as $lugar){
            $nombres = $lugar->Alt_names;
            $aNombres = explode(";", $nombres);
            foreach($aNombres as $nombre){
                $existe = DB::select('select * from cat_othernames where cName ="'.$nombre.'"');
                if($existe == []){
                    DB::table('cat_othernames')->insert([
                        'cName' => $nombre
                    ]);
                }
                $name = DB::select('select * from cat_othernames where cName ="'.$nombre.'"');

                DB::table('rel_lugar_othername')->insert([
                    'idLugar' => $lugar->id,
                    'idOtherName' => $name[0]->idName
                ]);
            }
        }
    }


    public function Tipos(){
        $lugares = DB::select("select * from lugares where Type is not null or Type != ''");
        foreach($lugares as $lugar){
            $tipo = $lugar->Type;
          
          
                $existe = DB::select('select * from cat_tipolugar where cTipo ="'.$tipo.'"');
                if($existe == []){
                    DB::table('cat_tipolugar')->insert([
                        'cTipo' => $tipo
                    ]);
                }
                $name = DB::select('select * from cat_tipolugar where cTipo ="'.$tipo.'"');

                DB::table('lugares')
                ->where('id', $lugar->id)
                ->update([
                    'idTipo' => $name[0]->id,
                ]);
          
        }

        return "OK";
    }

    public function Referencias(){
        $lugares = DB::select("select * from lugares where cReferences is not null");
 
        foreach($lugares as $lugar){
            $refs = $lugar->cReferences;
            //$refs = str_replace(":", ";", $refs);
            $aNombres = explode(";", $refs);
            foreach($aNombres as $nombre){
                $existe = DB::select('select * from cat_referencias where cNombre ="'.$nombre.'"');
                if($existe == []){
                    DB::table('cat_referencias')->insert([
                        'cNombre' => $nombre
                    ]);
                }
                $name = DB::select('select * from cat_referencias where cNombre ="'.$nombre.'"');

                DB::table('rel_lugar_referencia')->insert([
                    'idLugar' => $lugar->id,
                    'idReferencia' => $name[0]->idReferencia
                ]);
            }
        }
    }


    public function relLugarSecciones(){
        $archivos = DB::select('select * from archivos_or');
        foreach($archivos as $archivo){
            $lugares = DB::select("select * from lugares where idArchivoExcel =".$archivo->id);
            foreach($lugares as $lugar){
                $this->relPaginaSeccion($lugar, $archivo->id);
            }
        }
       return 'HECHO';
    }


    


    public function relPaginaSeccion($lugar, $idArchivo){
        $LcCad = "select * from indice_document_sections where idArchivo = ".$idArchivo;
        $indice = DB::select($LcCad);
    
        $LcCad = "select * from rel_lugares_paginas where idLugar = " . $lugar->idLugar." and id > 19196";
        $paginas = DB::select($LcCad);
        
    
        if (count($paginas) > 0) {
            echo "IDLugar: ".$lugar->idLugar."<br>";
            $cRelaciones = "";
            //return $paginas;
    
            for ($i = 0; $i < count($paginas); $i++) {
                $vacio = 0;
                $pag = $paginas[$i]->nPag;
                //echo "Pagina: ".$pag."<br>";
                //echo count($paginas);
    
    
                for ($e = 0; $e < count($indice); $e++) {
                    $inicio = $indice[$e]->Inicio;
                    $fin = $indice[$e]->Fin;
                    //echo $inicio."<br>";
                    //echo $fin."<br>";
                    if ($pag >= $inicio && $pag <= $fin) {
                        $cRelaciones = $cRelaciones . "," . $indice[$e]->id;
                        //echo "OK".$indice[$e]->idDocumentSection;
                    } else {
                        //echo "NO";
                    }
                    //echo "<br><br><br>";
                }
            }
            $cRelaciones = substr($cRelaciones, 1);
                echo $cRelaciones;
                $aRel = explode(",", $cRelaciones);
                //return $aRel;
                $aRel = array_unique($aRel);
                //return $aRel;
                $keys = array_keys($aRel);
    
                for ($a = 0; $a < count($keys); $a++) {
    
                    
                    if($aRel[$keys[$a]]){
                        $existe = DB::select("select * from rel_lugar_indice where idLugar = ".$lugar->idLugar." and idIndice =".$aRel[$keys[$a]]);
                        
                        if($existe == []){
                            $LcCad = "insert into rel_lugar_indice (idLugar, idIndice) values
                            (".$lugar->idLugar.", ".$aRel[$keys[$a]].") ";
                            DB::select($LcCad);
                            
                        }
                    }
                    //echo $idDS . "<br>";
                   
                    
                }
                
        }
    
        return "OK";
    }











    public function separaMarianaYuc()
    {
        $LcCad = "select * from lugares_mariana";
        $lugares = DB::select($LcCad);

       // return $lugares;
        
        foreach ($lugares as $lugar) {

            /*$LcCad = "insert into lugares (Placename, Toponym_Pages, idArchivoExcel, cEstatus) values
                ('".$lugar->Toponimo."', '".$lugar->Paginas."', '1', '".$lugar->cEstatus."') ";
                DB::select($LcCad);*/

            $pag = $lugar->Paginas;
            $pag = str_replace(" ", "", $pag);

            // echo $pag."<br>";

            $aPag = explode(",", $pag);
            //return $aPag;


            
            if(!$lugar->My_FID){
                $LcCad = "select * from lugares where Placename like '%".$lugar->Toponimo."%'";
                $TLugar = DB::select($LcCad);
                
                if(empty($TLugar)==1){
                    //No existe y hay que crearlo
                    lugar::insert([
                        'Placename' => $lugar->Toponimo,
                        'cEstatus' => 'Y'
                    ]);
                    $LcCad = "select * from lugares where Placename like '%".$lugar->Toponimo."%'";
                    $TLugar = DB::select($LcCad);
                    $TLugar = $TLugar[0];
                } else {
                    $TLugar = $TLugar[0];
                }
            } else {
                /*$LcCad = "select * from lugares where My_FID7 = ".$lugar->My_FID;
                $TLugar = DB::select($LcCad);*/
                $TLugar = lugar::where('My_FID7', $lugar->My_FID)->where('Placename', $lugar->Toponimo)->whereNull('idArchivoExcel')->get();
                //$TLugar = $TLugar[0];

                
                
    
                if(empty($TLugar)==1 || $TLugar == "[]"){

                    
                    $LcCad = "select * from lugares where Placename like '%".$lugar->Toponimo."%'";
                    $TLugar = DB::select($LcCad);
                    if(empty($TLugar)==1){
                        //No existe y hay que crearlo
                        lugar::insert([
                            'Placename' => $lugar->Toponimo,
                            'cEstatus' => 'Y'
                        ]);
                        $LcCad = "select * from lugares where Placename like '%".$lugar->Toponimo."%'";
                        $TLugar = DB::select($LcCad);
                        $TLugar = $TLugar[0];
                    }

                    
                } else {
                    
                    
                    $TLugar = $TLugar[0];
                }
            }

           
           //return $TLugar[0];
            
          


            //Separar a la tabla de paginas 
            
            for ($i = 0; $i < count($aPag); $i++) {

                
                $pagina = $aPag[$i];
                
                $existe = strripos($pagina, "-");
                //Si hay un guion en la pagina es un intervalo
                echo $existe;
                if ($existe > 0) {
                   
                    $inicio = substr($pagina, 0, $existe);
                    $fin = substr($pagina, $existe + 1, 3);
                    echo $TLugar->id." - ".$TLugar->Placename. "<br>";
                    
                    for ($inicio; $inicio <= $fin; $inicio++) {
                        lugarPag::insert([
                            'idLugar' => $TLugar->id,
                            'nPag' => $inicio
                        ]);
                    }
                } else {
                    //return is_array($TLugar);
                    if(is_array($TLugar)){
                        $TLugar = $TLugar[0];
                    }
                    echo $TLugar->id." - ".$TLugar->Placename. "<br>";
                 
                   
                    $new = lugarPag::insert([
                        'idLugar' => $TLugar->id,
                        'nPag' => $pagina
                    ]);
                }
            }
        }

        echo "ECHO";
    }

    public function ordenaRelaciones(){
        $LcCad = "select * from indice_document_sections where idArchivo = 12 group by Documento";
        $indice = DB::select($LcCad);
        foreach($indice as $index){
            if(!$index->idDocumentSection){
                Relacion::insert([
                    'cNombre' => $index->Documento,
                    'idArchivoXLS' => 12,
                    'jsonDatosMapa' => '{"centro":{"lat":"19.19480396","long":"-89.36894743"},"limites":{"visible":false,"nE":{"lat":null,"long":null},"nO":{"lat":null,"long":null},"sE":{"lat":null,"long":null},"sO":{"lat":null,"long":null}},"zoom":{"max":12,"min":0,"inicial":8}}'
                ]);
                $new = Relacion::where('cNombre', $index->Documento)->where('idArchivoXLS', 12)->get();
                $LcCad = "update indice_document_sections set idDocumentSection = ".$new[0]->idDS." where Documento = '".$index->Documento."'";
                DB::select($LcCad);
            } else {
                $new = Relacion::where('idDS', $index->idDocumentSection)->update([
                    'idArchivoXLS' => 12,
                ]);
                
            }
        }
    }


    public function LugaresYucatan(){//Asocia los lugares con las paginas en el indice
        
    
        $LcCad = "select * from rel_lugares_paginas where id > 19196 group by idLugar";
        $lugares = DB::select($LcCad);

       // return $lugares;

        foreach($lugares as $lugar){
            $this->relPaginaSeccion($lugar, 12);
            
        }

        return 'HECHO';
    }


    public function LugaresARelacion(){//Asocia los lugares a su relacion principal

        
        
    
        $LcCad = "select * from indice_document_sections where idArchivo = 12 group by idDocumentSection";
        $indice = DB::select($LcCad);

        foreach($indice as $index){
            $lugares = lugarIndice::where('idIndice', $index->id)->get();
            //return $lugares;
            foreach($lugares as $lugar){
                $existe = lugarDS::where('idLugar', $lugar->idLugar)->where('idDS', $index->idDocumentSection)->get();
                if(empty($existe)==1 || $existe == "[]"){
                    echo $lugar->idLugar." - ".$index->idDocumentSection."<br>";
                    lugarDS::insert([
                        'idLugar' => $lugar->idLugar,
                        'idDS' => $index->idDocumentSection,
                        'idUsrAlta' => Auth::id()
                    ]);
                } 

            }
            
        }

        return 'HECHO';
    }



}
