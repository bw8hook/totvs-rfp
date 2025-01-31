<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
             // Definindo as chaves estrangeiras
             $table->foreign('departament_id')->references('id')->on('users_departaments')->onDelete('cascade');
             $table->foreign('user_role_id')->references('id')->on('users_role')->onDelete('cascade');
        });

        Schema::table('users_departaments', function (Blueprint $table) {
             // Adiciona a chave estrangeira
            $table->foreign('manager_user_id')->references('id')->on('users')->onDelete('set null'); // Definir como NULL se o usuário for deletado
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departament_id']);
        });

        Schema::table('users_departaments', function (Blueprint $table) {
            $table->dropForeign(['manager_user_id']); // Remove a chave estrangeira
        });
    }
};
