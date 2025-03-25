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


    public function projectRecords()
    {
        return $this->hasMany(ProjectRecord::class, 'project_file_id');
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
    
    public function getRespondidosUser()
    {
        return $this->projectRecords->where('status', 'user edit')->count();
    }


}
