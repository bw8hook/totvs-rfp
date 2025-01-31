<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersDepartaments extends Model
{
    use HasFactory;

     // Relacionamento com User
    public function users(){
         return $this->hasMany(User::class, 'departament_id');
    }
}
