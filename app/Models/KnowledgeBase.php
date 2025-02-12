<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = [
        'user_id',
        'bundle_id',
        'name',
        'filename_original',
        'filepath',
        'filename',
        'status',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function knowledgeRecords()
    {
        return $this->hasMany(KnowledgeRecord::class, 'knowledge_base_id'); // Certifique-se de que 'knowledge_base_id' seja a chave estrangeira
    }

}
