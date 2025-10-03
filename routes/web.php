<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\ChartTemplateController;
use App\Http\Controllers\ReportController;

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

    // Rutas de archivos (extracción y filtrado de Excel)
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/create', [FileController::class, 'create'])->name('files.create');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
    Route::delete('/files/{id}', [FileController::class, 'delete'])->name('files.delete');
    Route::get('/files/{id}/view-data', [FileController::class, 'viewData'])->name('files.view-data');
    Route::get('/files/{id}/export', [FileController::class, 'exportProcessedData'])->name('files.export');

    // Rutas de gráficos
    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/charts/create', [ChartController::class, 'create'])->name('charts.create');
    Route::post('/charts/store', [ChartController::class, 'store'])->name('charts.store');
    Route::get('/charts/template/{id}', [ChartController::class, 'useTemplate'])->name('charts.use-template');
    Route::post('/charts/template/{id}/generate', [ChartController::class, 'generateFromTemplate'])->name('charts.generate-from-template');
    Route::get('/charts/{id}', [ChartController::class, 'showCharts'])->name('charts.show');
    Route::post('/charts/generate', [ChartController::class, 'generateCustomChart'])->name('charts.generate');
    Route::post('/charts/preview', [ChartController::class, 'getPreviewData'])->name('charts.preview');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');
        
        // Acciones de gráficos
        Route::post('/{id}/charts', [ReportController::class, 'addChart'])->name('charts.add');
        Route::delete('/{reportId}/charts/{chartId}', [ReportController::class, 'removeChart'])->name('charts.remove');
        Route::post('/{id}/charts/reorder', [ReportController::class, 'reorderCharts'])->name('charts.reorder');
        
        // Acciones de reporte
        Route::post('/{id}/publish', [ReportController::class, 'publish'])->name('publish');
        Route::post('/{id}/send', [ReportController::class, 'sendToExternalSystem'])->name('send');
        Route::get('/{id}/export/{format?}', [ReportController::class, 'export'])->name('export');
        Route::post('/{id}/duplicate', [ReportController::class, 'duplicate'])->name('duplicate');
    });
});

// API para el otro sistema (sin auth)
Route::prefix('api/reports')->name('api.reports.')->group(function () {
    Route::get('/{slug}', [ReportController::class, 'apiShow'])->name('show');
});
