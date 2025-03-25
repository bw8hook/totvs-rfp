<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeRecord extends Model
{
    protected $primaryKey = 'id_record'; // Define a chave primária
    protected $fillable = [
        'user_id',
        'processo',
        'subprocesso',
        'knowledge_base_id',
        'requisito',
        'observacao',
        'resposta',
        'status',
    ];

    public function bundles()
    {
        return $this->belongsToMany(RfpBundle::class, 
            'knowledge_records_bundles', 
            'knowledge_record_id',  // foreign key na tabela pivot que referencia knowledge_records
            'bundle_id'            // foreign key na tabela pivot que referencia rfp_bundles
        )->withPivot('old_bundle', 'bundle_status', 'created_at', 'updated_at');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'knowledge_records_types');
    }

    public function rfp_bundles()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id', 'bundle_id'); // Relacionamento com a tabela RfpBundle
    }
}
