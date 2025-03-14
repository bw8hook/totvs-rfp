<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


use App\Mail\NewUserEmail;
use Illuminate\Support\Facades\Mail;



class PermissionsController extends Controller
{
    public function index()
    {
        $permissions = [
            'knowledge.manage',
            'knowledge.create',
            'knowledge.edit',
            'knowledge.delete',
            'projects.all.manage',
            'projects.all.create',
            'projects.all.edit',
            'projects.all.delete',
            'projects.my.manage',
            'projects.my.create',
            'projects.my.edit',
            'projects.my.delete',
            'users.manage',
            'users.create',
            'users.edit',
            'users.delete',
            'config.manage',
            'roles.manage',
            'bundles.manage',
            'agents.manage',
            'answers.manage',
            'process.manage'
        ];
        
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }



    public function new_email()
    {

        $data = [
            'name' => "Henrique",
            'data' =>  date('d/m/y \à\s H:i'),
            'email' => 'henrique@bw8.com.br',
            'senha' => '1d8a1sd891d',
            'url' => 'https://totvs.bw8.tech/'
        ];

         Mail::to('henrique@bw8.com.br')->send(new NewUserEmail($data));
    }



}
