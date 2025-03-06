<?php

namespace App\Exports;

use App\Http\Controllers\ProjectRecordsController;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\ProjectRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KnowledgeCorrectionExport implements FromCollection, WithHeadings
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
            $ProjectRecord = ProjectRecord::where('id', $value['id_record'])->first();
            if($ProjectRecord->status == "user edit" && empty($ProjectRecord->retroalimentacao)){
                $ProjectRecord->retroalimentacao = $this->KnowledgeBaseExportedId;                
                $ProjectRecord->save();
            }
        }

        return collect($this->Records);
    }

    public function headings(): array
    {
        return ["ID Registro", "Processo", "Subprocesso", "Requisito", "Resposta", "Módulo", "Observações", "Linha/Produto"];
    }


}
