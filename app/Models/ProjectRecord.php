<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRecord extends Model
{
    protected $fillable = [
        'user_id',
        'bundle_id',
        'project_id',
        'project_file_id',
        'project_answer_id',
        'classificacao',
        'classificacao2',
        'requisito',
        'resposta',
        'resposta2',
        'status',
        'observacao',
    ];

    public function rfp_bundles()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id', 'bundle_id'); // Relacionamento com a tabela RfpBundle
    }

    public function file()
    {
        return $this->belongsTo(ProjectFiles::class, 'project_file_id');
    }
}
