<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineOfProduct extends Model
{
    protected $table = 'rfp_line_of_products';
    protected $fillable = ['name', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
