<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chart_templates', function (Blueprint $table) {
            $table->boolean('allow_multiple_metrics')->default(false)->after('y_axis');
            $table->json('recommended_metrics')->nullable()->after('allow_multiple_metrics');
        });
    }

    public function down()
    {
        Schema::table('chart_templates', function (Blueprint $table) {
            $table->dropColumn(['allow_multiple_metrics', 'recommended_metrics']);
        });
    }
};