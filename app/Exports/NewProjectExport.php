<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class NewProjectExport implements FromCollection
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Retorna os dados como uma coleção.
     */
    public function collection()
    {
        return collect($this->data);
    }

}
