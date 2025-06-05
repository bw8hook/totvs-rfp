<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFiles extends Model
{
    protected $fillable = [
        'user_id',
        'bundle_id',
        'project_id',
        'filename',
        'filepath',
        'filename_original',
        'file_extension',
        'status',
    ];

    public function rfp_bundles()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id', 'bundle_id'); // Relacionamento com a tabela RfpBundle
    }

    public function bundles()
    {
        return $this->belongsToMany(RfpBundle::class, 'project_files_rfp_bundles', 'project_file_id', 'bundle_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'iduser_responsable'); // Confirme o nome correto da chave estrangeira
    }


    // public function projectRecords()
    // {
    //     return $this->hasMany(ProjectRecord::class, 'project_file_id');
    // }

    public function projectRecords()
    {
        return $this->hasMany(ProjectRecord::class, 'id')
            ->leftJoin('project_answer', 'project_records.id', '=', 'project_answer.requisito_id');
    }




    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function records()
    {
        return $this->hasMany(ProjectRecord::class, 'project_file_id');
    }

    public function getRespondidosIa()
    {
        return $this->projectRecords->where('status', 'respondido ia')->count();
    }
    
    public function getRespondidosDesconhecidos($id)
    {
        //return $this->projectRecords->where('status', 'respondido ia');
        // return ProjectRecord::join('project_answers', 'project_answers.id', '=', 'project_records.project_answer_id')
        //     ->where('status', 'respondido ia')
        //     ->where('project_answers', 'respondido ia');


        return ProjectRecord::join('project_answer', function($join) {
            $join->on('project_answer.id', '=', 'project_records.project_answer_id');
        })
        ->where('status', 'respondido ia')
        ->where('project_records.project_file_id', $id)
        ->where('project_answer.aderencia_na_mesma_linha', 'desconhecido')
        ->whereRaw('project_answer.id = (
            SELECT MAX(id) 
            FROM project_answer AS sub 
            WHERE sub.requisito_id = project_answer.requisito_id 
            AND sub.requisito_id = project_answer.requisito_id
        )')->count();
        

      
    }


    public function getRespondidosUser()
    {
        return $this->projectRecords->where('status', 'user edit')->count();
    }


}
