<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAnswer extends Model
{
    use HasFactory;

    // Definindo a tabela associada a este modelo
    protected $table = 'project_answer';

    // Definindo os campos que podem ser preenchidos em massa
    protected $fillable = [
        'bundle_id',
        'user_id',
        'requisito_id',
        'requisito',
        'aderencia_na_mesa_linha',
        'linha_produto',
        'resposta',
        'referencia',
        'observacao',
        'acuracidade_porcentagem',
        'acuracidade_explicacao',
        'created_at',
        'updated_at'
    ];

    // Definindo os campos que são do tipo date
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    // Definindo os relacionamentos (se necessário)
    public function bundle()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requisito()
    {
        return $this->belongsTo(ProjectRecord::class, 'requisito_id');
    }
}
