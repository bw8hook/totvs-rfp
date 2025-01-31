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
        Schema::create('knowledge_records', function (Blueprint $table) {
            $table->id('id_record');
            $table->bigInteger('bundle_id')->unsigned()->nullable();
            $table->string('bundle_old', 255)->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('knowledge_base_id')->unsigned();
            $table->string('spreadsheet_line', 255)->nullable();
            $table->string('classificacao', 255)->nullable();
            $table->string('classificacao2', 255)->nullable();
            $table->longText('requisito')->nullable();
            $table->longText('resposta')->nullable();
            $table->longText('resposta2')->nullable();
            $table->longText('observacao')->nullable();
            $table->enum('status', ['aguardando', 'ativo', 'inativo'])->default('aguardando'); // Substitua pelos valores reais do enum
            $table->timestamps(); // Isso cria `created_at` e `updated_at`

            // Definindo as chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bundle_id')->references('bundle_id')->on('rfp_bundles')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_records', function (Blueprint $table) {
            // Remover as chaves estrangeiras antes de dropar a tabela
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bundle_id']);
        });

        Schema::dropIfExists('knowledge_records');
    }
};
