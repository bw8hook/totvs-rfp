<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeRecord extends Model
{
    protected $fillable = [
        'bundle_id',
        'classificacao',
        'classificacao2',
        'knowledge_base_id',
        'requisito',
        'resposta',
        'resposta2',
        'importancia',
    ];
}
