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
    public function __construct($id, $Records)
    {
         $this->id = $id;
         $this->Records = $Records;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        foreach ($this->Records as $key => $value) {
            $ProjectRecord = KnowledgeRecord::where('id_record', $value->id_record)->first();
            $ProjectRecord->status = 'processado';
            $ProjectRecord->save();
        }

        return $this->Records;
        //return KnowledgeRecord::select('classificacao', 'classificacao2', 'requisito', 'resposta', 'resposta2', 'observacao', 'bundle_old')->get();
    }

    public function headings(): array
    {
        return ["Classificação 1", "Classificação 2", "Descrição do Requisito", "Resposta 1", "Resposta 2", "Observações", "Linha/Produto"];
    }


}
