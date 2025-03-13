<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


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


}
