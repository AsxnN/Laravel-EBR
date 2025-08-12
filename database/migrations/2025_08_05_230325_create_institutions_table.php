<?php
// filepath: c:\laragon\www\EBR\database\migrations\2024_01_01_000001_create_institutions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_modular')->unique();
            $table->string('anexo')->nullable();
            $table->string('nombre_ie');
            $table->string('nivel')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('tipo_ie')->nullable();
            $table->string('dre')->nullable();
            $table->string('ugel')->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('centro_poblado')->nullable();
            $table->timestamps();
            
            $table->index(['codigo_modular']);
            $table->index(['departamento']);
            $table->index(['ugel']);
            $table->index(['nivel']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('institutions');
    }
};