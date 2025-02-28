<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfpProcess extends Model
{
    
    protected $table = 'rfp_process'; // Nome da tabela

    protected $fillable = [
        'process',  // Corrigindo o nome do campo
        'order',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];


    public function rfpBundles()
    {
        return $this->belongsToMany(RfpBundle::class, 'rfp_process_bundle', 'rfp_process_id', 'rfp_bundle_id')->withTimestamps(); // se sua tabela pivot tem timestamps
    }

    
}
