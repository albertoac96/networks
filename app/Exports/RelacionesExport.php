<?php

namespace App\Exports;


use App\Models\Relacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class RelacionesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    

    public function headings(): array
    {
        return [
            'idInterno',
            'Acuña',
            'NombreAcuña'
        ];
    }

    public function collection()
    {

        $LcResp = Relacion::select([
            'document_sections.idDS',
            'T2.cAbv',
            'document_sections.cNombre'
        ])
        ->leftJoin('archivos_or as T2', 'T2.id', 'document_sections.idArchivoXLS')
        ->whereNotNull('document_sections.cContenido')
        ->get();

        return collect($LcResp);
       
    }
}
