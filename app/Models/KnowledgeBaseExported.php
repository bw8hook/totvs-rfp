<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseExported extends Model
{
    use HasFactory;

    protected $table = 'knowledge_base_exported';

    public $timestamps = true;

    protected $fillable = ['user_id', 'bundle_id', 'filepath', 'filename'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bundle()
    {
        return $this->belongsTo(RfpBundle::class, 'bundle_id');
    }
}
