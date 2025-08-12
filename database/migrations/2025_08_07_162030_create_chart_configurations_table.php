<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chart_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('chart_name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('chart_type', ['bar', 'line', 'pie', 'doughnut', 'area', 'radar', 'scatter', 'table']);
            $table->enum('education_level', ['inicial', 'primaria', 'secundaria', 'multi_level'])->nullable();
            $table->string('x_axis_field');
            $table->json('y_axis_fields');
            $table->json('chart_config')->nullable();
            $table->integer('order_position')->default(0);
            $table->timestamps();
            
            // Índices
            $table->index(['template_id', 'order_position']);
            $table->index('chart_type');
            $table->index('education_level');
            $table->index('x_axis_field');
            
            // Claves foráneas
            $table->foreign('template_id')->references('id')->on('chart_templates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chart_configurations');
    }
};