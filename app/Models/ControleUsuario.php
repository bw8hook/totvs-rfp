<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleUsuario extends Model
{
    protected $table = 'controle_usuarios';

    protected $fillable = [
        'data',
        'usuarios_habilitados',
    ];

    public $timestamps = true; // Se quiser desativar: false
}
