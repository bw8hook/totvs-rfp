<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_departaments', function (Blueprint $table) {
            $table->id();
            $table->string('departament', length: 255);
            $table->unsignedBigInteger('manager_user_id')->nullable(); 
            $table->enum('departament_type', ['Executivo','Especialista','Geral'])->default('Geral');
            $table->timestamps(); // Isso cria `created_at` e `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::dropIfExists('users_departaments');
    }
};
