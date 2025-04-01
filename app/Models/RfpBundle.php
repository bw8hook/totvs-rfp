<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfpBundle extends Model
{
    public $timestamps = true; // Garante que os timestamps serão usados

    protected $primaryKey = 'bundle_id'; // Define a chave primária

    // Remover guarded completamente
    protected $guarded = [];

    // Definir fillable explicitamente
    protected $fillable = [
        'bundle',
        'agent_id',
        'type_id',
        'service_group_id',
        'working_group_id',
        'category_id',
        'status_totvs',
        'status'
    ];


    // Defina o relacionamento com o modelo Agent
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function projectFiles()
    {
        return $this->belongsToMany(ProjectFiles::class, 'project_files_rfp_bundles', 'bundle_id', 'project_file_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function knowledge()
    {
        return $this->hasMany(KnowledgeRecord::class, 'bundle_id', 'bundle_id');
    }


   // Pertence a (Belongs To)
   public function serviceGroup()
   {
       return $this->belongsTo(ServiceGroup::class, 'service_group_id', 'id');
   }


    public function rfpProcesses()
    {
        return $this->belongsToMany(RfpProcess::class, 'rfp_process_bundle', 'rfp_bundle_id', 'rfp_process_id')
            ->withTimestamps(); // se sua tabela pivot tem timestamps
    }

    


    public function lineOfProduct()
    {
        return $this->belongsToMany(LineOfProduct::class, 'line_of_product_rfp_bundles', 'rfp_bundles_id', 'line_of_product_id');
    }

    public function segments()
    {
        return $this->belongsToMany(Segments::class, 'rfp_bundle_segment', 'bundle_id', 'segment_id');
    }


    /**
     * Relacionamento N:N com a tabela modules.
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_product', 'product_id', 'module_id');
    }


    public function projectRecords()
    {
        return $this->belongsToMany(ProjectRecord::class, 
            'project_records_bundles', 
            'bundle_id', 
            'project_record_id'
        )->withTimestamps();
    }

    

    public function knowledgeRecords()
    {
        return $this->belongsToMany(KnowledgeRecord::class, 
            'knowledge_records_bundles', 
            'bundle_id', 
            'knowledge_record_id', 
            'bundle_id', 
            'id'
        );
    }
    
}
