<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    /**
     * Define o relacionamento 1:1 com o modelo Product.
     */
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
