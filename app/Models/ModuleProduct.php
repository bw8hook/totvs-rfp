<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleProduct extends Model
{
    use HasFactory;

    protected $table = 'module_product'; // Nome da tabela

    protected $fillable = ['module_id', 'product_id'];

    public function products() {
        return $this->belongsToMany(Product::class, 'module_product', 'module_id', 'product_id');
    }

    public function modules() {
        return $this->belongsToMany(Module::class, 'module_product', 'product_id', 'module_id');
    }

    
    
}
