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

});
