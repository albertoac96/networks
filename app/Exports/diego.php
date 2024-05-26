<?php

namespace App\Exports;

use App\Models\lugar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class diego implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    
    protected $idSeccion;

    public function __construct($idSeccion)
    {
        $this->idSeccion = $idSeccion;
    }

    public function headings(): array
    {
        return [
            'FID',
            'My_FID7',
            'My_FID2',
            'Placename',
            'Alt_names',
            'ModernName',
            'Confidence',
            'Notes',
            'Type',
            'FID_Relate',
            'Relation',
            'My_FID71',
            'FID_Relat2',
            'Type2',
            'Start_Date',
            'End_Date',
            'cReferences',
            'Type_URL',
            'X',
            'Y',
            'Time_span',
            'Toponym_Name',
            'Toponym_Reference',
            'Toponym_Pages',
            'Toponym_Document_Sections',
            'cEstatus'
        ];
    }

    public function collection()
    {

        $LcCad = "select T2.FID, T2.My_FID7, T2.My_FID2, T2.Placename, T2.Alt_names, T2.ModernName, T2.Confidence,
        T2.Notes, T2.Type, T2.FID_Relate, T2.Relation, T2.My_FID71, T2.FID_Relat2, T2.Type2, T2.Start_Date, T2.End_Date,
        T2.cReferences, T2.Type_URL, T2.X, T2.Y, T2.Time_span, T2.Toponym_Name, T2.Toponym_Reference,
        T2.Toponym_Pages, T2.Toponym_Document_Sections, T2.cEstatus from 
        rel_lugar_document_section as T1
        left join lugares as T2 on T2.id = T1.idLugar
        where T1.idDS = ".$this->idSeccion;





        
        $LcResp = DB::select($LcCad);
        return collect($LcResp);
       
    }
}

