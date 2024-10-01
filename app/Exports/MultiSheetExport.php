<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $adjacencyList;

    public function __construct(array $data, array $adjacencyList)
    {
        $this->data = $data;
        $this->adjacencyList = $adjacencyList;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->data as $sheetName => $sheetContent) {
            $sheets[] = new SingleSheetExport($sheetName, $sheetContent['headers'], $sheetContent['data']);
        }

        $sheets[] = new AdjacencyListExport('Distance_Matrix', $this->adjacencyList);

        return $sheets;
    }
}
