<?php
// filepath: c:\laragon\www\EBR\database\migrations\2024_01_01_000004_create_primaria_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('primaria_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_file_id')->constrained('uploaded_files')->onDelete('cascade');
            
            // Campos base heredados
            $table->string('dre')->nullable();
            $table->string('ugel')->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('centro_poblado')->nullable();
            $table->string('codigo_modular');
            $table->string('anexo')->nullable();
            $table->string('nombre_ie');
            $table->string('nivel')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('tipo_ie')->nullable();
            
            // Campos de matrícula
            $table->integer('total_matriculados')->default(0);
            $table->integer('matricula_definitiva')->default(0);
            $table->integer('matricula_proceso')->default(0);
            $table->integer('dni_validado')->default(0);
            $table->integer('dni_sin_validar')->default(0);
            $table->integer('registro_sin_dni')->default(0);
            
            // Campos administrativos
            $table->integer('total_grados')->default(0);
            $table->integer('total_secciones')->default(0);
            $table->integer('nomina_generada')->default(0);
            $table->integer('nomina_aprobada')->default(0);
            $table->integer('nomina_por_rectificar')->default(0);
            
            // Campos específicos de primaria
            $table->integer('primero_hombres')->default(0);
            $table->integer('primero_mujeres')->default(0);
            $table->integer('segundo_hombres')->default(0);
            $table->integer('segundo_mujeres')->default(0);
            $table->integer('tercero_hombres')->default(0);
            $table->integer('tercero_mujeres')->default(0);
            $table->integer('cuarto_hombres')->default(0);
            $table->integer('cuarto_mujeres')->default(0);
            $table->integer('quinto_hombres')->default(0);
            $table->integer('quinto_mujeres')->default(0);
            $table->integer('sexto_hombres')->default(0);
            $table->integer('sexto_mujeres')->default(0);
            
            $table->timestamps();
            
            $table->index(['codigo_modular']);
            $table->index(['uploaded_file_id']);
            $table->index(['departamento']);
            $table->index(['ugel']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('primaria_data');
    }
};