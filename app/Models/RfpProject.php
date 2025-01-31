<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfpProject extends Model
{
    use HasFactory;

    protected $table = 'rfp_projects'; // Nome da tabela

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'answered',
        'unanswered',
        'filename_original',
        'filepath',
        'filename',
        'file_extension',
    ];
}
