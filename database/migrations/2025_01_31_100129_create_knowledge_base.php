<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->unsignedInteger('bundle_id')->nullable()->default(null);
            $table->longText('name')->nullable()->charset('latin1');
            $table->longText('filename_original')->nullable()->charset('utf8mb4');
            $table->string('filepath', 255)->nullable()->default(null)->charset('latin1');
            $table->longText('filename')->nullable()->charset('utf8mb4');
            $table->string('file_extension', 255)->nullable()->default(null)->charset('latin1');
            $table->longText('project')->nullable()->charset('latin1');
            $table->date('rfp_date')->nullable()->default(null);
            $table->string('project_team', 255)->nullable()->default(null)->charset('latin1');
            $table->enum('status', ['não enviado', 'processando', 'concluído'])->default('não enviado')->charset('latin1');
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);

            $table->primary(['id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_base');
    }
};
