<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeRecord extends Model
{
    protected $primaryKey = 'id_record'; // Define a chave primária
    protected $fillable = [
        'user_id',
        'bundle_id',
        'processo',
        'subprocesso',
        'knowledge_base_id',
        'requisito',
        'observacao',
        'resposta',
        'modulo',
        'status',
    ];

    public function rfp_bundles()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id', 'bundle_id'); // Relacionamento com a tabela RfpBundle
    }
}
