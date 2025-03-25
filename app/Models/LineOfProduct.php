<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineOfProduct extends Model
{
    protected $table = 'rfp_line_of_products';
    protected $fillable = ['name', 'status'];

    public function rfpBundles()
    {
        return $this->belongsToMany(RfpBundle::class, 'line_of_product_rfp_bundles', 'line_of_product_id', 'rfp_bundles_id');
    }
}
