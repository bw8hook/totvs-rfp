<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{

    use HasFactory;

    protected $table = 'rfp_products';
    protected $fillable = [
        'type', 'name', 'category', 'service_group_id', 'hook_status',
        'totvs_status', 'line_of_product_id', 'inclusion_date', 'inactivation_date'
    ];

    protected $casts = [
        'hook_status' => 'boolean',
        'inclusion_date' => 'date',
        'inactivation_date' => 'date'
    ];

    public function lineOfProduct()
    {
        return $this->belongsTo(LineOfProduct::class);
    }

    public function serviceGroup()
    {
        return $this->belongsTo(ServiceGroup::class);
    }


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
