<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDownloadHistory extends Model
{
    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'project_download_history';

    /**
     * Chave primária da tabela
     */
    protected $primaryKey = 'id';

    /**
     * Atributos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'filename'
    ];

    /**
     * Atributos que devem ser ocultos em arrays/JSON
     */
    protected $hidden = [];

    /**
     * Atributos que devem ser convertidos para tipos nativos
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relacionamento com o projeto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Método de escopo para filtrar downloads recentes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Método para registrar um novo download
     */
    public static function recordDownload(int $userId, int $projectId, string $filename): self
    {
        return self::create([
            'user_id' => $userId,
            'project_id' => $projectId,
            'filename' => $filename
        ]);
    }

    /**
     * Método para obter downloads por usuário
     */
    public static function getDownloadsByUser(int $userId)
    {
        return self::where('user_id', $userId)
            ->with(['project', 'user'])
            ->latest()
            ->get();
    }
    
}
