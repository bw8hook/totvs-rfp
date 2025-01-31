<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users_role')->insertOrIgnore([
            ['id' => 1, 'name' => 'Super Admin', 'description' => 'Super Administrator', 'role_priority' => 99, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Administrator', 'description' => 'Administrador', 'role_priority' => 90, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Gestor', 'description' => 'Gestor', 'role_priority' => 80, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Especialista', 'description' => 'Engenheiro de Valor', 'role_priority' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Executive', 'description' => 'Executivo de Vendas', 'role_priority' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
