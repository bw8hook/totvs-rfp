<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfpBundle extends Model
{
    protected $primaryKey = 'bundle_id'; // Define a chave primária
    protected $fillable = [
        'bundle',
        'type_bundle',
    ];


    public function knowledge()
    {
        return $this->hasMany(KnowledgeRecord::class, 'bundle_id', 'bundle_id');
    }
}
