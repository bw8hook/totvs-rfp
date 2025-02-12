<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description']; // Adicione os campos necessários

    /**
     * Relacionamento N:N com a tabela products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'module_product', 'module_id', 'product_id');
    }
}
