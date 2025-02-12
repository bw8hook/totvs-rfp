<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'agent_id']; // Adicione os campos necessários

    /**
     * Relacionamento N:N com a tabela modules.
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_product', 'product_id', 'module_id');
    }

    /**
     * Relacionamento 1:1 com a tabela agents.
     */
    public function agent()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }
}
