<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectHistory extends Model
{
    protected $table = 'project_history';

    protected $fillable = [
        'user_id',
        'answer_id',
        'old_answer',
        'new_answer',
        'old_module',
        'new_module',
        'old_observation',
        'new_observation',
        'old_bundle',
        'new_bundle',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Confirme o nome correto da chave estrangeira
    }


    public function answers()
    {
        return $this->belongsTo(ProjectAnswer::class, 'answer_id'); // Relacionamento com a tabela RfpBundle
    }
}
