<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceGroup extends Model
{
    protected $table = 'service_group';
    
    protected $fillable = [
        'name',
        'order',
        'status'
    ];

    // Se você não quiser usar timestamps (created_at e updated_at)
    // public $timestamps = true;

    // Define os valores possíveis para o campo status
    const STATUS_ATIVO = 'ativo';
    
    // Mutator para garantir que o status seja sempre minúsculo
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    // Scope para filtrar apenas registros ativos
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    // Scope para ordenar por ordem
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Método para verificar se está ativo
    public function isActive()
    {
        return $this->status === self::STATUS_ATIVO;
    }
}
