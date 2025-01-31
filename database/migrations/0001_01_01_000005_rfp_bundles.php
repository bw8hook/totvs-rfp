<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rfp_bundles', function (Blueprint $table) {
            $table->id('bundle_id');
            $table->string('bundle', 255);
            $table->enum('type_bundle', ['Admin','Analista','Engenheiro'])->default('Analista');
            $table->string('bundle_color', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundles');
    }
};
