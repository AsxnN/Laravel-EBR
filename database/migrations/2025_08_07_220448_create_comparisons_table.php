<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->enum('comparison_type', ['single_level', 'multi_level']);
            $table->json('education_levels');
            $table->string('comparison_period')->nullable();
            $table->json('geo_filters')->nullable();
            $table->longText('charts_data')->nullable();
            $table->enum('status', ['processing', 'ready', 'error', 'draft'])->default('processing');
            $table->integer('total_institutions')->nullable();
            $table->integer('total_students')->nullable();
            $table->string('dataset_path')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['status', 'comparison_type']);
            $table->index('template_id');
            $table->index('created_by');
            $table->index('comparison_period');
            $table->index('created_at');
            
            // Claves foráneas
            $table->foreign('template_id')->references('id')->on('chart_templates')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comparisons');
    }
};