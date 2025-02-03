<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeRecord extends Model
{
    protected $fillable = [
        'user_id',
        'bundle_id',
        'classificacao',
        'classificacao2',
        'knowledge_base_id',
        'requisito',
        'observacao',
        'resposta',
        'resposta2',
        'status',
    ];

    public function rfp_bundles()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id', 'bundle_id'); // Relacionamento com a tabela RfpBundle
    }
}
