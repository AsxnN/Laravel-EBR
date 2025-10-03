<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->json('metadata')->nullable();
            $table->enum('status', ['draft', 'published', 'sent'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('external_id')->nullable(); // ID en el otro sistema
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['status', 'published_at']);
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};