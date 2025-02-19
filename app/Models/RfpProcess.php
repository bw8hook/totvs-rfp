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
}
