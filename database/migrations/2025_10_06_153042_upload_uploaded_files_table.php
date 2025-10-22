<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            // No necesitamos cambiar la estructura de la tabla
            // Solo actualizar el enum en la validación si usas enum en BD
        });
        
        // Si tienes check constraints o enum, actualízalos aquí
        DB::statement("ALTER TABLE uploaded_files MODIFY COLUMN document_type ENUM('inicial', 'primaria', 'secundaria', 'global')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE uploaded_files MODIFY COLUMN document_type ENUM('inicial', 'primaria', 'secundaria')");
    }
};