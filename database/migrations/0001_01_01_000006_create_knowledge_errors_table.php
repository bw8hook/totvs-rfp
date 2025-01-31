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
        Schema::create('knowledge_errors', function (Blueprint $table) {
            $table->id(); // Coluna ID auto-incrementada
            $table->string('error_code'); // Código do erro
            $table->text('error_message'); // Mensagem de erro
            $table->json('error_data'); // Mensagem de erro
            $table->unsignedBigInteger('user_id')->nullable(); // Relacionamento com usuários, se necessário
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_errors');
    }
};
