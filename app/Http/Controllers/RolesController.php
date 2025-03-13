<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        // Exemplo: Recuperar todos os registros
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('roles.list', compact('roles', 'permissions'));
    }

    public function filter(Request $request)
    {
        // Aplicar ordenação
        $orderBy = $request->get('sort_order', 'id_desc'); // Padrão: mais recente primeiro

        // Iniciar a query
        $query = Role::with('permissions');

        // Aplicar ordenação
        switch ($orderBy) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            // Adicione mais casos conforme necessário
        }

        // Paginação
        $roles = $query->paginate(40);

        // Carregar todas as permissões
        $permissions = Permission::all();

        // Para depuração
        // dd($roles);

        // Retornar dados em JSON
        return response()->json(data: $roles);
    }
    

    public function new()
    {
        // Exemplo: Recuperar todos os registros
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('roles.create', compact('roles', 'permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.list')
            ->with('success', 'Role criada com sucesso!');
    }

    public function show($id)
    {
        $role = Role::findById($id);

         // Exemplo: Recuperar todos os registros
         $roles = Role::with('permissions')->get();
         $permissions = Permission::all();

         $data = array(
            'role' => $role,
            'permissions' => $permissions,
            'id' => $id,
        );

        return view('roles.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        $role = UserRole::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:users_role,name,' . $id . '|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $role->update($validated);
        return response()->json($role);
    }

    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Role deletada com sucesso.']);
    }
}
