<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class SingleSheetExport implements FromCollection, WithHeadings, WithTitle
{
    protected $sheetName;
    protected $headers;
    protected $sheetData;

    public function __construct(string $sheetName, array $headers, array $sheetData)
    {
        $this->sheetName = $sheetName;
        $this->headers = $headers;
        $this->sheetData = $sheetData;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function collection()
    {
        return new Collection($this->sheetData);
    }

   

    
}
