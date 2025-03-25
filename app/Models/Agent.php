<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = ['agent_name', 'knowledge_id', 'knowledge_id_hook', 'prompt', 'search_engine', 'status']; // Adicione os campos necessários

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Defina o relacionamento com o modelo RfpBundle
    public function rfpBundles()
    {
        return $this->hasMany(RfpBundle::class, 'agent_id');
    }
    
}
