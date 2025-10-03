<?php
// filepath: c:\laragon\www\Laravel-EBR\app\Http\Controllers\ReportController.php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportChart;
use App\Models\ChartTemplate;
use App\Models\UploadedFile;
use App\Http\Controllers\ChartController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['creator', 'charts'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $templates = ChartTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $files = UploadedFile::orderBy('uploaded_at', 'desc')
            ->get()
            ->groupBy('document_type');

        return view('reports.create', compact('templates', 'files'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'status' => 'in:draft,published'
            ]);

            $report = Report::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->input('status', 'draft'),
                'created_by' => auth()->id(),
                'metadata' => [
                    'created_at_formatted' => now()->format('d/m/Y H:i')
                ]
            ]);

            if ($request->status === 'published') {
                $report->update(['published_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reporte creado exitosamente',
                'report_id' => $report->id,
                'redirect_url' => route('reports.show', $report->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $report = Report::with(['creator', 'charts.template'])->findOrFail($id);
        
        return view('reports.show', compact('report'));
    }

public function edit($id)
{
    try {
        $report = Report::with(['charts.template', 'creator'])->findOrFail($id);
        
        $templates = ChartTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Agrupar archivos por tipo de documento para la vista
        $files = collect([
            'inicial' => UploadedFile::where('document_type', 'LIKE', '%inicial%')
                ->orWhere('original_name', 'LIKE', '%inicial%')
                ->orderBy('uploaded_at', 'desc')
                ->get(),
            'primaria' => UploadedFile::where('document_type', 'LIKE', '%primaria%')
                ->orWhere('original_name', 'LIKE', '%primaria%')
                ->orderBy('uploaded_at', 'desc')
                ->get(),
            'secundaria' => UploadedFile::where('document_type', 'LIKE', '%secundaria%')
                ->orWhere('original_name', 'LIKE', '%secundaria%')
                ->orderBy('uploaded_at', 'desc')
                ->get()
        ]);
        
        Log::info('Edit report - datos cargados:', [
            'report_id' => $report->id,
            'templates_count' => $templates->count(),
            'files_count' => $files->map(fn($group) => $group->count())->toArray(),
            'existing_charts' => $report->charts->count()
        ]);

        return view('reports.edit', compact('report', 'templates', 'files'));
    } catch (\Exception $e) {
        Log::error('Error en edit report:', $e->getMessage());
        return back()->with('error', 'Error al cargar el reporte para edición');
    }
}

    public function update(Request $request, $id)
    {
        try {
            $report = Report::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'status' => 'in:draft,published'
            ]);

            $report->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status
            ]);

            if ($request->status === 'published' && !$report->published_at) {
                $report->update(['published_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reporte actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    // Agregar gráfico al reporte
    public function addChart(Request $request, $reportId)
    {
        try {
            $report = Report::findOrFail($reportId);

            // Validación de entrada
            $validatedData = $request->validate([
                'template_id' => 'required|exists:chart_templates,id',
                'chart_title' => 'required|string|max:255',
                'file_ids' => 'required|array|min:1',
                'file_ids.*' => 'exists:uploaded_files,id',
                'notes' => 'nullable|string',
                'assigned_levels' => 'nullable|string'
            ]);

            Log::info('AddChart - Datos recibidos:', $validatedData);

            $template = ChartTemplate::findOrFail($validatedData['template_id']);
            Log::info('AddChart - Template encontrado:', ['template' => $template->toArray()]);

            // **USAR EL MISMO MÉTODO QUE FUNCIONA EN CHARTS**
            $chartController = new ChartController();
            
            // Crear un request simulado con los niveles asignados
            $mockRequest = new Request();
            $mockRequest->merge([
                'file_ids' => $validatedData['file_ids'],
                'assigned_levels' => $validatedData['assigned_levels'] ?? '{}'
            ]);
            
            // **LLAMAR AL MÉTODO QUE FUNCIONA EN USE-TEMPLATE**
            $chartData = $chartController->processMultipleFilesWithLevels(
                $validatedData['file_ids'],
                $template->x_axis,
                $template->y_axis,
                $mockRequest
            );
            
            Log::info('AddChart - Datos del gráfico generados:', ['chart_data' => $chartData]);

            // Verificar que se generaron datos
            if (empty($chartData['series']) || empty($chartData['categories'])) {
                Log::error('AddChart - No se generaron datos válidos:', ['chart_data' => $chartData]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron generar datos válidos para el gráfico. Verifica que los archivos contengan datos compatibles con la plantilla.'
                ], 400);
            }

            // Obtener el siguiente número de orden
            $nextOrder = $report->charts()->max('order') + 1;

            // Crear el gráfico del reporte
            $reportChart = ReportChart::create([
                'report_id' => $report->id,
                'template_id' => $template->id,
                'chart_title' => $validatedData['chart_title'],
                'file_ids' => $validatedData['file_ids'],
                'chart_data' => $chartData,
                'chart_config' => [
                    'x_axis' => $template->x_axis,
                    'y_axis' => $template->y_axis,
                    'chart_type' => $template->chart_type,
                    'x_label' => $template->x_axis_label,
                    'y_label' => $template->y_axis_label
                ],
                'notes' => $validatedData['notes'] ?? null,
                'order' => $nextOrder
            ]);

            Log::info('AddChart - Gráfico creado exitosamente:', [
                'chart_id' => $reportChart->id,
                'series_count' => count($chartData['series']),
                'categories_count' => count($chartData['categories'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gráfico agregado exitosamente al reporte',
                'chart' => $reportChart->load('template')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('AddChart - Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('AddChart - Error general:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

// Método auxiliar para generar datos del gráfico
private function generateChartDataForReport($fileIds, $template, $assignedLevels = [])
{
    try {
        // Obtener archivos
        $files = UploadedFile::whereIn('id', $fileIds)->get();
        
        if ($files->isEmpty()) {
            throw new \Exception('No se encontraron archivos válidos');
        }

        // Simular la generación de datos del gráfico
        // Aquí deberías usar la misma lógica que en ChartController
        $chartData = [
            'series' => [],
            'categories' => []
        ];

        // Procesar archivos según el template
        foreach ($files as $file) {
            $level = $assignedLevels[$file->id] ?? 'General';
            
            // Aquí iría la lógica específica para procesar cada archivo
            // Por ahora, datos de ejemplo:
            $chartData['series'][] = [
                'name' => $level . ' - ' . $file->original_name,
                'data' => [10, 20, 30, 40, 50], // Datos de ejemplo
                'color' => $this->getColorForLevel($level)
            ];
        }

        $chartData['categories'] = ['Cat 1', 'Cat 2', 'Cat 3', 'Cat 4', 'Cat 5']; // Categorías de ejemplo

        return $chartData;

    } catch (\Exception $e) {
        Log::error('Error generando datos del gráfico:', [
            'message' => $e->getMessage(),
            'template_id' => $template->id,
            'file_ids' => $fileIds
        ]);
        
        // Retornar datos vacíos en caso de error
        return [
            'series' => [],
            'categories' => []
        ];
    }
}

// Método auxiliar para obtener colores por nivel
private function getColorForLevel($level)
{
    $colors = [
        'Inicial' => '#3B82F6',
        'Primaria' => '#10B981',
        'Secundaria' => '#8B5CF6',
        'General' => '#6B7280'
    ];

    return $colors[$level] ?? '#6B7280';
}

    // Eliminar gráfico del reporte
    public function removeChart($reportId, $chartId)
    {
        try {
            $report = Report::findOrFail($reportId);
            $chart = ReportChart::where('report_id', $reportId)->findOrFail($chartId);
            
            $chart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gráfico eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando gráfico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar gráfico: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reordenar gráficos
    public function reorderCharts(Request $request, $reportId)
    {
        try {
            $report = Report::findOrFail($reportId);

            $request->validate([
                'chart_orders' => 'required|array',
                'chart_orders.*.id' => 'required|exists:report_charts,id',
                'chart_orders.*.order' => 'required|integer|min:0'
            ]);

            foreach ($request->chart_orders as $chartOrder) {
                ReportChart::where('id', $chartOrder['id'])
                    ->where('report_id', $reportId)
                    ->update(['order' => $chartOrder['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden de gráficos actualizado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error reordenando gráficos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar gráficos: ' . $e->getMessage()
            ], 500);
        }
    }

    // Publicar reporte
    public function publish($id)
    {
        try {
            $report = Report::findOrFail($id);
            
            if ($report->charts()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede publicar un reporte sin gráficos'
                ], 400);
            }

            $report->update([
                'status' => 'published',
                'published_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reporte publicado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error publicando reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    // Enviar al otro sistema
    public function sendToExternalSystem($id)
    {
        try {
            $report = Report::with(['creator', 'charts.template'])->findOrFail($id);

            if ($report->status !== 'published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden enviar reportes publicados'
                ], 400);
            }

            // Preparar datos para envío
            $exportData = [
                'report' => [
                    'title' => $report->title,
                    'description' => $report->description,
                    'slug' => $report->slug,
                    'published_at' => $report->published_at->toISOString(),
                    'creator' => [
                        'name' => $report->creator->name,
                        'email' => $report->creator->email
                    ],
                    'metadata' => $report->metadata
                ],
                'charts' => $report->charts->map(function ($chart) {
                    return [
                        'title' => $chart->chart_title,
                        'template_name' => $chart->template->name,
                        'chart_type' => $chart->template->chart_type,
                        'chart_data' => $chart->chart_data,
                        'chart_config' => $chart->chart_config,
                        'notes' => $chart->notes,
                        'order' => $chart->order,
                        'files_info' => $chart->files->map(function ($file) {
                            return [
                                'name' => $file->original_name,
                                'type' => $file->document_type,
                                'uploaded_at' => $file->uploaded_at->toISOString()
                            ];
                        })
                    ];
                })
            ];

            // Guardar archivo JSON para el otro sistema
            $filename = 'exports/report_' . $report->slug . '.json';
            Storage::disk('public')->put($filename, json_encode($exportData, JSON_PRETTY_PRINT));

            // Opcional: Enviar vía HTTP al otro sistema
            // $response = Http::post('http://otro-sistema.local/api/reports/import', $exportData);

            $report->update([
                'status' => 'sent',
                'sent_at' => now(),
                'external_id' => $report->slug // o el ID que devuelva el otro sistema
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reporte enviado exitosamente al sistema externo',
                'export_url' => Storage::disk('public')->url($filename),
                'export_path' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando reporte al sistema externo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    // Exportar reporte
    public function export($id, $format = 'json')
    {
        $report = Report::with(['creator', 'charts.template'])->findOrFail($id);

        switch ($format) {
            case 'json':
                return $this->exportAsJson($report);
            case 'pdf':
                return $this->exportAsPdf($report);
            default:
                return $this->exportAsJson($report);
        }
    }

    private function exportAsJson($report)
    {
        $exportData = [
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'description' => $report->description,
                'slug' => $report->slug,
                'status' => $report->status,
                'published_at' => $report->published_at,
                'created_at' => $report->created_at,
                'creator' => [
                    'id' => $report->creator->id,
                    'name' => $report->creator->name,
                    'email' => $report->creator->email
                ]
            ],
            'charts' => $report->charts->map(function ($chart) {
                return [
                    'id' => $chart->id,
                    'title' => $chart->chart_title,
                    'template' => [
                        'name' => $chart->template->name,
                        'chart_type' => $chart->template->chart_type
                    ],
                    'chart_data' => $chart->chart_data,
                    'chart_config' => $chart->chart_config,
                    'notes' => $chart->notes,
                    'order' => $chart->order
                ];
            })
        ];

        // Guardar en storage para que el otro sistema pueda acceder
        $filename = 'exports/report_' . $report->slug . '.json';
        Storage::disk('public')->put($filename, json_encode($exportData, JSON_PRETTY_PRINT));

        return response()->download(
            storage_path('app/public/' . $filename),
            'reporte_' . $report->slug . '.json'
        );
    }

    // API para el otro sistema Laravel
    public function apiShow($slug)
    {
        try {
            $report = Report::with(['creator', 'charts.template'])
                ->where('slug', $slug)
                ->where('status', 'sent')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'report' => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'slug' => $report->slug,
                    'published_at' => $report->published_at,
                    'sent_at' => $report->sent_at,
                    'creator' => $report->creator->name,
                    'metadata' => $report->metadata,
                    'charts' => $report->charts->map(function ($chart) {
                        return [
                            'id' => $chart->id,
                            'title' => $chart->chart_title,
                            'template_name' => $chart->template->name,
                            'chart_type' => $chart->template->chart_type,
                            'chart_data' => $chart->chart_data,
                            'chart_config' => $chart->chart_config,
                            'notes' => $chart->notes,
                            'order' => $chart->order
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reporte no encontrado'
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $report = Report::findOrFail($id);
            
            // Los gráficos se eliminan automáticamente por la foreign key cascade
            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reporte eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate($id)
    {
        try {
            $originalReport = Report::with('charts')->findOrFail($id);
            
            $newReport = Report::create([
                'title' => $originalReport->title . ' (Copia)',
                'description' => $originalReport->description,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'metadata' => $originalReport->metadata
            ]);

            // Duplicar gráficos
            foreach ($originalReport->charts as $chart) {
                ReportChart::create([
                    'report_id' => $newReport->id,
                    'template_id' => $chart->template_id,
                    'chart_title' => $chart->chart_title,
                    'file_ids' => $chart->file_ids,
                    'chart_data' => $chart->chart_data,
                    'chart_config' => $chart->chart_config,
                    'notes' => $chart->notes,
                    'order' => $chart->order
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reporte duplicado exitosamente',
                'report_id' => $newReport->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error duplicando reporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al duplicar reporte: ' . $e->getMessage()
            ], 500);
        }
    }
}