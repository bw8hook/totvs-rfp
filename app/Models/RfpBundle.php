<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfpBundle extends Model
{
    public $timestamps = true; // Garante que os timestamps serão usados

    protected $primaryKey = 'bundle_id'; // Define a chave primária
    protected $fillable = [
        'bundle',
        'agent_id',
        'created_at',
        'updated_at',
    ];

    // Defina o relacionamento com o modelo Agent
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function knowledge()
    {
        return $this->hasMany(KnowledgeRecord::class, 'bundle_id', 'bundle_id');
    }
}
