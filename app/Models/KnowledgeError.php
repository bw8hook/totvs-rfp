<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeError extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser atribuídos em massa.
     */
    protected $fillable = [
        'error_code',
        'error_message',
        'user_id',
        'error_data',
    ];

    /**
     * Relacionamento com o modelo User (se necessário).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}