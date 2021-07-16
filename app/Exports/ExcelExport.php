<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelExport implements FromArray, ShouldAutoSize, WithHeadings
{
    protected $data;
    protected $header;

    public function __construct(array $data, array $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->header;
    }

}
