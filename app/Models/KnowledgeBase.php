<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = [
        'user_id',
        'bundle_id',
        'filename_original',
        'filepath',
        'filename',
        'status',
    ];
    
}
