<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        $roles = UserRole::all();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:users_role|max:255',
            'description' => 'nullable|string|max:500',
            'role_priority' => 'nullable|string|max:500',
        ]);

        $role = UserRole::create($validated);
        return response()->json($role, 201);
    }

    public function show($id)
    {
        $role = UserRole::findOrFail($id);
        return response()->json($role);
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
