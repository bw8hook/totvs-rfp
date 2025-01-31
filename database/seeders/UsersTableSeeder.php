<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'profile_picture' => null,
            'name' => 'Henrique',
            'idtotvs' => '123',
            'email' => 'henrique@bw8.com.br',
            'corporate_phone' => '132', // Se for necessário, adicione o valor correto para o telefone
            'user_role_id' => 1, // Defina o id do cargo ou role do usuário
            'departament_id' => 6, // Defina o id do cargo ou role do usuário
            'password' => Hash::make('Vabene@123'),
            'remember_token' => 'AeUipQfnQZTwSuw0AnowTXkVuiklcuT5nx8Nrwq3Dx6Z5Rvim6dDh8MSOy9u', // Token de "lembrar-se"
            'status' => 'ativo', // Status do usuário
            'created_at' => now(),
            'updated_at' => now()
        ]);
        

        // Usando updateOrCreate para garantir que a informação seja alterada ou inserida
        DB::table('users_departaments')->update(['manager_user_id' => 1, 'updated_at' => now()]);
        
    }
    
}