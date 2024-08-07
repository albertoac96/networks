<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class AdjacencyListExport implements FromCollection, WithTitle
{
    protected $sheetName;
    protected $adjacencyList;

    public function __construct(string $sheetName, array $adjacencyList)
    {
        $this->sheetName = $sheetName;
        $this->adjacencyList = $adjacencyList;
    }

    public function collection()
    {
        return new Collection($this->adjacencyList);
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}
