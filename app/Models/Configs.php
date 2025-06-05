<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configs extends Model
{

    protected $table = 'configs';
    
    protected $fillable = [
        'users_liberado',
        'agentes_ativo',
        'status_enviando'
    ];

}
