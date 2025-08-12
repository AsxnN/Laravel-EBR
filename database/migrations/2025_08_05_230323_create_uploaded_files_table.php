<?php
// filepath: c:\laragon\www\EBR\database\migrations\2025_08_05_230340_create_uploaded_files_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('document_type'); // inicial, primaria, secundaria
            $table->integer('file_size');
            $table->integer('total_institutions')->default(0);
            $table->integer('total_students')->default(0);
            $table->json('processing_summary')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            
            $table->index(['document_type']);
            $table->index(['uploaded_at']);
            $table->index(['uploaded_by']);
            
            // Foreign key para el usuario que subió el archivo
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('uploaded_files');
    }
};