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
        Schema::create('rfp_projects', function (Blueprint $table) {
            $table->id(); // Equivalente a 'id' int(11)
            $table->integer('user_id')->length(11); // 'user_id' int(11)
            $table->text('title'); // 'title' longest (assumindo que seja um campo de texto longo)
            $table->text('description'); // 'description' longest
            $table->string('status', 255); // 'status' varchar(255)
            $table->integer('answered')->length(11); // 'answered' int(11)
            $table->integer('unanswered')->length(11); // 'unanswered' int(11)
            $table->text('filename_original'); // 'filename_original' longest
            $table->string('filepath', 255); // 'filepath' varchar(255)
            $table->text('filename'); // 'filename' longest
            $table->string('file_extension', 255); // 'file_extension' varchar(255)
            $table->timestamps(); // 'updated_at' e 'created_at' timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfp_projects');
    }
};
