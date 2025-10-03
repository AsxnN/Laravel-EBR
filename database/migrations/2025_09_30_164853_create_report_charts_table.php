<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_charts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('template_id');
            $table->string('chart_title');
            $table->json('file_ids'); // Archivos usados
            $table->json('chart_data'); // Datos del gráfico generado
            $table->json('chart_config'); // Configuración
            $table->text('notes')->nullable(); // Notas adicionales
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('chart_templates');
            $table->index(['report_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_charts');
    }
};