<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserDepartamentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users_departaments')->insertOrIgnore([
            ['id' => 1, 'departament' => 'TOTVS IP', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'departament' => 'Franquias', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'departament' => 'TOTVS Rio Grande do Sul', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'departament' => 'SMB', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'departament' => 'Setor Público', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'departament' => 'BW8', 'manager_user_id' => null, 'departament_type' => 'Geral', 'created_at' => now(), 'updated_at' => now()]   
        ]);
    }
}
