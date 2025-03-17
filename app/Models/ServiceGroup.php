<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceGroup extends Model
{
    protected $table = 'rfp_service_groups';
    protected $fillable = ['name', 'active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
