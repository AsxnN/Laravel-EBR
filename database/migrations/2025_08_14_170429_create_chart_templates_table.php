<?php
// filepath: c:\laragon\www\Laravel-EBR\database\migrations\2024_12_XX_create_chart_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chart_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('x_axis');
            $table->string('y_axis');
            $table->enum('chart_type', ['bar', 'line', 'column', 'pie']);
            $table->json('config')->nullable(); // Para configuraciones adicionales
            $table->string('purpose'); // Para qué sirve el gráfico
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chart_templates');
    }
};