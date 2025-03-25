<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'types';
    
    protected $fillable = [
        'name',
        'order',
        'status'
    ];

    public function rfpBundles()
    {
        return $this->hasMany(RFPBundle::class);
    }

    public function knowledgeRecords()
    {
        return $this->belongsToMany(KnowledgeRecord::class, 'knowledge_records_types');
    }
    
    // Mutator para garantir que o status seja sempre minúsculo
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    // Scope para filtrar apenas registros ativos
    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }

    // Scope para ordenar por ordem
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Método para verificar se está ativo
    public function isActive()
    {
        return $this->status === 'ativo';
    }
}
