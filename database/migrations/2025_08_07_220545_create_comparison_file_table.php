<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comparison_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comparison_id');
            $table->unsignedBigInteger('uploaded_file_id');
            $table->timestamps();
            
            // Índices
            $table->index('comparison_id');
            $table->index('uploaded_file_id');
            $table->unique(['comparison_id', 'uploaded_file_id'], 'comparison_file_unique');
            
            // Claves foráneas
            $table->foreign('comparison_id')->references('id')->on('comparisons')->onDelete('cascade');
            $table->foreign('uploaded_file_id')->references('id')->on('uploaded_files')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comparison_file');
    }
};