<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfpAnswer extends Model
{
    use HasFactory;

    protected $table = 'rfp_answer'; // Nome da tabela

    protected $fillable = [
        'answer',  // Corrigindo o nome do campo
        'order',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
