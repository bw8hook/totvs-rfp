<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'users_role';

    protected $fillable = [
        'name',
        'description',
        'role_priority',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'user_role_id', 'id');
    }
}
