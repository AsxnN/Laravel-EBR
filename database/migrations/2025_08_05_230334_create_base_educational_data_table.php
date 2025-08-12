<?php
// filepath: c:\laragon\www\EBR\database\migrations\2024_01_01_000002_create_base_educational_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Esta tabla no se crea físicamente, solo define la estructura base
        // Las tablas hijas heredarán estos campos
    }

    public function down()
    {
        // No hay tabla física que eliminar
    }
};