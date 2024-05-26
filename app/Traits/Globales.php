<?php

namespace App\Traits;
date_default_timezone_set("America/Mexico_City");

use Illuminate\Http\Request;
use App\Models\ReporteCambios;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


trait Globales{

    public function metodoComun() {
    return "OK";
    }

    private function RegistraCambio($tipo, $idDoc, $cDesc){
        $idUser = Auth::id();
       //return $tipo." ".$idtipo." ".$cDesc;
        $cambio = ReporteCambios::create([
            'cTipo' => $tipo,
            'idTipo' => $idDoc,
            'cDescripcion' => $cDesc,
            'idUsrAlta' => $idUser,
        ]);
        return "OK";

    }
}