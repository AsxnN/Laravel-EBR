<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            // Agregar campos si no existen
            if (!Schema::hasColumn('uploaded_files', 'total_institutions')) {
                $table->integer('total_institutions')->nullable()->after('table_name');
            }
            
            if (!Schema::hasColumn('uploaded_files', 'total_students')) {
                $table->integer('total_students')->nullable()->after('total_institutions');
            }
            
            if (!Schema::hasColumn('uploaded_files', 'document_type')) {
                $table->enum('document_type', ['inicial', 'primaria', 'secundaria', 'mixed'])->nullable()->after('total_students');
            }
            
            if (!Schema::hasColumn('uploaded_files', 'processing_status')) {
                $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('document_type');
            }
            
            if (!Schema::hasColumn('uploaded_files', 'uploaded_at')) {
                $table->timestamp('uploaded_at')->nullable()->after('processing_status');
            }
            
            // Agregar índices si no existen
            if (!$this->indexExists('uploaded_files', 'uploaded_files_document_type_index')) {
                $table->index('document_type');
            }
            
            if (!$this->indexExists('uploaded_files', 'uploaded_files_processing_status_index')) {
                $table->index('processing_status');
            }
            
            if (!$this->indexExists('uploaded_files', 'uploaded_files_uploaded_at_index')) {
                $table->index('uploaded_at');
            }
        });
    }

    public function down()
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->dropIndex(['document_type']);
            $table->dropIndex(['processing_status']);
            $table->dropIndex(['uploaded_at']);
            $table->dropColumn([
                'total_institutions',
                'total_students',
                'document_type',
                'processing_status',
                'uploaded_at'
            ]);
        });
    }

    private function indexExists($table, $index)
    {
        $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))
            ->pluck('Key_name')
            ->toArray();
        
        return in_array($index, $indexes);
    }
};