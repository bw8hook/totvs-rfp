<?php

namespace App\Exports;

use App\Http\Controllers\ProjectRecordsController;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KnowledgeBaseExport implements FromCollection, WithHeadings
{
    protected $id;
    protected $Records;
    protected $KnowledgeBaseExportedId;
    public function __construct($id, $Records, $KnowledgeBaseExportedId)
    {
        $this->id = $id;
        $this->Records = $Records;
        $this->KnowledgeBaseExportedId = $KnowledgeBaseExportedId; 
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        foreach ($this->Records as $key => $value) {
            $ProjectRecord = KnowledgeRecord::where('id_record', $value->id_record)->first();
            $ProjectRecord->status = 'processado';
            $ProjectRecord->base_exported_id = $this->KnowledgeBaseExportedId;
            $ProjectRecord->save();
        }

        return $this->Records;
    }

    public function headings(): array
    {
        return ["ID Registro", "Processo", "Subprocesso", "Requisito", "Resposta", "Módulo", "Observações", "Linha/Produto"];
    }


}
