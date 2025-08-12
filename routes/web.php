<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\ChartTemplateController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard'); 

    // ==========================================
    // 📁 RUTAS DE ARCHIVOS
    // ==========================================
    Route::prefix('files')->name('files.')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('/upload', [FileController::class, 'create'])->name('create');
        Route::post('/upload', [FileController::class, 'upload'])->name('upload');
        Route::get('/{id}', [FileController::class, 'show'])->name('show');
        Route::get('/{id}/download', [FileController::class, 'download'])->name('download');
        Route::get('/{id}/data', [FileController::class, 'viewData'])->name('data');
        Route::get('/{id}/export', [FileController::class, 'exportProcessedData'])->name('export');
        Route::delete('/{id}', [FileController::class, 'delete'])->name('delete');
    });

    // ==========================================
    // 📊 RUTAS DE GRÁFICOS
    // ==========================================
    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');

    // ==========================================
    // 🔄 RUTAS DE COMPARATIVAS
    // ==========================================
    Route::prefix('comparisons')->name('comparisons.')->group(function () {
        Route::get('/', [ComparisonController::class, 'index'])->name('index');
        Route::get('/create', [ComparisonController::class, 'create'])->name('create');
        Route::post('/generate', [ComparisonController::class, 'generate'])->name('generate');
        Route::get('/download/{filePath}', [ComparisonController::class, 'download'])->name('download');
        Route::get('/{comparison}', [ComparisonController::class, 'show'])->name('show');
        Route::delete('/{comparison}', [ComparisonController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // 📋 RUTAS DE PLANTILLAS DE GRÁFICOS
    // ==========================================
    Route::prefix('chart-templates')->name('chart-templates.')->group(function () {
        Route::get('/', [ChartTemplateController::class, 'index'])->name('index');
        Route::get('/create', [ChartTemplateController::class, 'create'])->name('create');
        Route::post('/', [ChartTemplateController::class, 'store'])->name('store');
        Route::get('/{template}', [ChartTemplateController::class, 'show'])->name('show');
        Route::get('/{template}/edit', [ChartTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [ChartTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [ChartTemplateController::class, 'destroy'])->name('destroy');

        Route::post('/{template}/generate-charts', [ChartTemplateController::class, 'generateCharts'])->name('generate-charts');
        Route::post('/{template}/download-report', [ChartTemplateController::class, 'downloadReport'])->name('download-report');
        
        // Acciones especiales para plantillas
        Route::post('/{template}/generate-charts', [ChartTemplateController::class, 'generateCharts'])->name('generate-charts');
        Route::get('/{template}/download-report', [ChartTemplateController::class, 'downloadReport'])->name('download-report');
        Route::post('/{template}/duplicate', [ChartTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/{template}/toggle-status', [ChartTemplateController::class, 'toggleStatus'])->name('toggle-status');
    });
});
