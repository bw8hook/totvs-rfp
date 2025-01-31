<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_role', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('name')->unique(); // Nome da role
            $table->string('role_priority'); // Nome da role
            $table->string('description')->nullable(); // Descrição da role
            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_role');
    }
};
