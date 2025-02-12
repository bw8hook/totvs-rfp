<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project'; // Nome da tabela

    protected $fillable = [
        'name',
        'iduser_responsable',
        'project_date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'iduser_responsable');
    }

    public function projectRecords()
    {
        return $this->hasMany(ProjectRecord::class, 'project_file_id'); // Certifique-se de que 'knowledge_base_id' seja a chave estrangeira
    }


    public function files()
    {
        return $this->hasMany(ProjectFiles::class, 'project_id');
    }

    public function records()
    {
        return $this->hasManyThrough(ProjectRecord::class, ProjectFiles::class, 'project_id', 'project_file_id');
    }



    

    
}
