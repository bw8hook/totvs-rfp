<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;



use App\Models\UsersDepartaments;


class User extends Authenticatable
{
    use HasRoles, HasFactory, HasApiTokens, HasFactory, Notifiable; // Adicione o HasApiTokens aqui

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_picture',
        'name',
        'email',
        'idtotvs',
        'identity_id',
        'password',
        'position',
        'departament_id',
        'user_role_id',
        'status',
        'corporate_phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function toArray()
    {
        $data = parent::toArray();

        // Incluir o relacionamento 'role'
        $data['role'] = $this->role ? $this->role->only(['id', 'name']) : null;

        return $data;
    }


     // Relacionamento com UsersDepartaments
    public function departament(){
        return $this->belongsTo(UsersDepartaments::class, 'departament_id');
    }

    public function userPosition(){
        return $this->hasOne(UsersDepartaments::class, 'user_position_id', 'user_position');
    }
}
