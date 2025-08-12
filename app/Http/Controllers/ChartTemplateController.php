<?php
// filepath: app/Http/Controllers/ChartTemplateController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartTemplate;
use App\Models\ChartConfiguration;
use App\Models\UploadedFile;
use App\Models\Comparison;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartTemplateController extends Controller
{
    // Campos geográficos para eje X
    private const GEOGRAPHIC_FIELDS = [
        'dre' => 'DRE',
        'ugel' => 'UGEL',
        'departamento' => 'Departamento',
        'provincia' => 'Provincia',
        'distrito' => 'Distrito',
        'mes' => 'Mes',
        'tipo_ie' => 'Tipo de IE',
        'codigo_modular' => 'Código Modular'
    ];

    // Campos de cantidad comunes para eje Y
    private const QUANTITY_FIELDS = [
        'total_matriculados' => 'Total Matriculados',
        'matricula_definitiva' => 'Matrícula Definitiva',
        'matricula_proceso' => 'Matrícula en Proceso',
        'dni_validado' => 'DNI Validado',
        'dni_sin_validar' => 'DNI Sin Validar',
        'registro_sin_dni' => 'Registro Sin DNI',
        'total_grados' => 'Total Grados',
        'total_secciones' => 'Total Secciones',
        'nomina_generada' => 'Nómina Generada',
        'nomina_aprobada' => 'Nómina Aprobada',
        'nomina_por_rectificar' => 'Nómina por Rectificar'
    ];

    // Campos específicos por nivel educativo para eje Y
    private const LEVEL_SPECIFIC_FIELDS = [
        'inicial' => [
            'cero_hombres' => '0 años - Hombres',
            'cero_mujeres' => '0 años - Mujeres',
            'primero_hombres' => '3 años - Hombres',
            'primero_mujeres' => '3 años - Mujeres',
            'segundo_hombres' => '4 años - Hombres',
            'segundo_mujeres' => '4 años - Mujeres',
            'tercero_hombres' => '5 años - Hombres',
            'tercero_mujeres' => '5 años - Mujeres',
            'cuarto_hombres' => '6 años - Hombres',
            'cuarto_mujeres' => '6 años - Mujeres',
            'quinto_hombres' => '7+ años - Hombres',
            'quinto_mujeres' => '7+ años - Mujeres',
            'mas_quinto_hombres' => 'Más de 7 años - Hombres',
            'mas_quinto_mujeres' => 'Más de 7 años - Mujeres'
        ],
        'primaria' => [
            'primero_hombres' => '1° Grado - Hombres',
            'primero_mujeres' => '1° Grado - Mujeres',
            'segundo_hombres' => '2° Grado - Hombres',
            'segundo_mujeres' => '2° Grado - Mujeres',
            'tercero_hombres' => '3° Grado - Hombres',
            'tercero_mujeres' => '3° Grado - Mujeres',
            'cuarto_hombres' => '4° Grado - Hombres',
            'cuarto_mujeres' => '4° Grado - Mujeres',
            'quinto_hombres' => '5° Grado - Hombres',
            'quinto_mujeres' => '5° Grado - Mujeres',
            'sexto_hombres' => '6° Grado - Hombres',
            'sexto_mujeres' => '6° Grado - Mujeres'
        ],
        'secundaria' => [
            'primero_hombres' => '1° Año - Hombres',
            'primero_mujeres' => '1° Año - Mujeres',
            'segundo_hombres' => '2° Año - Hombres',
            'segundo_mujeres' => '2° Año - Mujeres',
            'tercero_hombres' => '3° Año - Hombres',
            'tercero_mujeres' => '3° Año - Mujeres',
            'cuarto_hombres' => '4° Año - Hombres',
            'cuarto_mujeres' => '4° Año - Mujeres',
            'quinto_hombres' => '5° Año - Hombres',
            'quinto_mujeres' => '5° Año - Mujeres'
        ]
    ];

    // Tipos de gráficos disponibles
    private const CHART_TYPES = [
        'bar' => 'Gráfico de Barras',
        'line' => 'Gráfico de Líneas',
        'pie' => 'Gráfico Circular',
        'doughnut' => 'Gráfico de Dona',
        'area' => 'Gráfico de Área',
        'radar' => 'Gráfico Radar',
        'scatter' => 'Gráfico de Dispersión',
        'table' => 'Tabla de Datos'
    ];

    // Niveles educativos
    private const EDUCATION_LEVELS = [
        'inicial' => 'Inicial',
        'primaria' => 'Primaria',
        'secundaria' => 'Secundaria'
    ];

    public function index()
    {
        $templates = ChartTemplate::with(['charts', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas
        $totalTemplates = $templates->count();
        $activeTemplates = $templates->where('status', 'active')->count();
        $singleLevelTemplates = $templates->where('template_type', 'single_level')->count();
        $multiLevelTemplates = $templates->where('template_type', 'multi_level')->count();
        
        // Estadísticas de uso
        $totalUsageCount = 0;
        foreach ($templates as $template) {
            $totalUsageCount += Comparison::where('template_id', $template->id)->count();
        }

        return view('chart-templates.index', compact(
            'templates',
            'totalTemplates',
            'activeTemplates',
            'singleLevelTemplates',
            'multiLevelTemplates',
            'totalUsageCount'
        ));
    }

    public function create()
    {
        $educationLevels = self::EDUCATION_LEVELS;
        $chartTypes = self::CHART_TYPES;
        $geographicFields = self::GEOGRAPHIC_FIELDS;
        $quantityFields = self::QUANTITY_FIELDS;
        $levelSpecificFields = self::LEVEL_SPECIFIC_FIELDS;

        return view('chart-templates.create', compact(
            'educationLevels',
            'chartTypes',
            'geographicFields',
            'quantityFields',
            'levelSpecificFields'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'education_levels' => 'required|array|min:1',
            'education_levels.*' => 'in:inicial,primaria,secundaria',
            'charts' => 'required|array|min:1',
            'charts.*.chart_name' => 'required|string|max:255',
            'charts.*.description' => 'nullable|string',
            'charts.*.chart_type' => 'required|in:bar,line,pie,doughnut,area,radar,scatter,table',
            'charts.*.x_axis_field' => 'required|string',
            'charts.*.y_axis_fields' => 'required|array|min:1',
            'charts.*.y_axis_fields.*' => 'string'
        ]);

        try {
            DB::beginTransaction();

            // Determinar tipo de plantilla
            $templateType = count($request->education_levels) > 1 ? 'multi_level' : 'single_level';

            // Crear plantilla
            $template = ChartTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'template_type' => $templateType,
                'education_levels' => $request->education_levels,
                'status' => 'active',
                'created_by' => Auth::id()
            ]);

            // Crear configuraciones de gráficos
            foreach ($request->charts as $index => $chartData) {
                $chartConfig = [
                    'education_levels' => $request->education_levels,
                    'x_axis_field' => $chartData['x_axis_field'],
                    'y_axis_fields' => $chartData['y_axis_fields'],
                    'chart_options' => [
                        'show_legend' => $templateType === 'multi_level' || count($chartData['y_axis_fields']) > 1,
                        'legend_position' => 'top',
                        'colors' => $this->generateColors(count($chartData['y_axis_fields'])),
                        'responsive' => true,
                        'maintain_aspect_ratio' => false
                    ]
                ];

                ChartConfiguration::create([
                    'template_id' => $template->id,
                    'chart_name' => $chartData['chart_name'],
                    'title' => $chartData['chart_name'],
                    'description' => $chartData['description'],
                    'chart_type' => $chartData['chart_type'],
                    'education_level' => $templateType === 'single_level' ? $request->education_levels[0] : 'multi_level',
                    'x_axis_field' => $chartData['x_axis_field'],
                    'y_axis_fields' => $chartData['y_axis_fields'],
                    'chart_config' => $chartConfig,
                    'order_position' => $index + 1
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plantilla de gráfico creada exitosamente',
                'redirect_url' => route('chart-templates.show', $template)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating chart template: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(ChartTemplate $template)
    {
        $template->load(['charts', 'creator']);

        // Estadísticas de uso
        $usageStats = [
            'total_comparisons' => Comparison::where('template_id', $template->id)->count(),
            'last_used' => Comparison::where('template_id', $template->id)->max('created_at'),
            'avg_charts' => $template->charts->count()
        ];

        // Obtener archivos disponibles por nivel educativo
        $availableFiles = [];
        foreach ($template->education_levels as $level) {
            $availableFiles[$level] = UploadedFile::where('document_type', $level)
                ->orderBy('uploaded_at', 'desc')
                ->get();
        }

        return view('chart-templates.show', compact('template', 'usageStats', 'availableFiles'));
    }

    public function edit(ChartTemplate $template)
    {
        $template->load('charts');
        
        $educationLevels = self::EDUCATION_LEVELS;
        $chartTypes = self::CHART_TYPES;
        $geographicFields = self::GEOGRAPHIC_FIELDS;
        $quantityFields = self::QUANTITY_FIELDS;
        $levelSpecificFields = self::LEVEL_SPECIFIC_FIELDS;

        return view('chart-templates.edit', compact(
            'template',
            'educationLevels',
            'chartTypes',
            'geographicFields',
            'quantityFields',
            'levelSpecificFields'
        ));
    }

    public function update(Request $request, ChartTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'charts' => 'required|array|min:1',
            'charts.*.chart_name' => 'required|string|max:255',
            'charts.*.description' => 'nullable|string',
            'charts.*.chart_type' => 'required|in:bar,line,pie,doughnut,area,radar,scatter,table',
            'charts.*.x_axis_field' => 'required|string',
            'charts.*.y_axis_fields' => 'required|array|min:1',
            'charts.*.y_axis_fields.*' => 'string'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar plantilla
            $template->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status
            ]);

            // Eliminar configuraciones existentes
            $template->charts()->delete();

            // Crear nuevas configuraciones
            foreach ($request->charts as $index => $chartData) {
                $chartConfig = [
                    'education_levels' => $template->education_levels,
                    'x_axis_field' => $chartData['x_axis_field'],
                    'y_axis_fields' => $chartData['y_axis_fields'],
                    'chart_options' => [
                        'show_legend' => $template->template_type === 'multi_level' || count($chartData['y_axis_fields']) > 1,
                        'legend_position' => 'top',
                        'colors' => $this->generateColors(count($chartData['y_axis_fields'])),
                        'responsive' => true,
                        'maintain_aspect_ratio' => false
                    ]
                ];

                ChartConfiguration::create([
                    'template_id' => $template->id,
                    'chart_name' => $chartData['chart_name'],
                    'title' => $chartData['chart_name'],
                    'description' => $chartData['description'],
                    'chart_type' => $chartData['chart_type'],
                    'education_level' => $template->template_type === 'single_level' ? $template->education_levels[0] : 'multi_level',
                    'x_axis_field' => $chartData['x_axis_field'],
                    'y_axis_fields' => $chartData['y_axis_fields'],
                    'chart_config' => $chartConfig,
                    'order_position' => $index + 1
                ]);
            }

            DB::commit();

            return redirect()->route('chart-templates.show', $template)
                ->with('success', 'Plantilla actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating chart template: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al actualizar la plantilla')
                ->withInput();
        }
    }

    public function destroy(ChartTemplate $template)
    {
        try {
            // Verificar si tiene comparaciones asociadas
            $comparisonsCount = Comparison::where('template_id', $template->id)->count();
            if ($comparisonsCount > 0) {
                return redirect()->route('chart-templates.index')
                    ->with('error', 'No se puede eliminar la plantilla porque tiene ' . $comparisonsCount . ' comparaciones asociadas');
            }

            $template->delete();
            
            return redirect()->route('chart-templates.index')
                ->with('success', 'Plantilla eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error deleting chart template: ' . $e->getMessage());
            
            return redirect()->route('chart-templates.index')
                ->with('error', 'Error al eliminar la plantilla');
        }
    }

    public function generatePreview(Request $request, ChartTemplate $template)
    {
        $request->validate([
            'selected_files' => 'required|array|min:1',
            'selected_files.*' => 'exists:uploaded_files,id'
        ]);

        try {
            $files = UploadedFile::whereIn('id', $request->selected_files)->get();
            $previewCharts = [];

            foreach ($template->charts as $chart) {
                $chartData = $this->generateChartData($chart, $files);
                if ($chartData) {
                    $previewCharts[] = $chartData;
                }
            }

            return response()->json([
                'success' => true,
                'charts' => $previewCharts,
                'files_count' => $files->count(),
                'template_name' => $template->name
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating preview: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar vista previa: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== MÉTODOS PRIVADOS ==========

    private function generateColors($count)
    {
        $colors = [
            '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd',
            '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf',
            '#aec7e8', '#ffbb78', '#98df8a', '#ff9896', '#c5b0d5',
            '#c49c94', '#f7b6d3', '#c7c7c7', '#dbdb8d', '#9edae5'
        ];

        return array_slice($colors, 0, $count);
    }

    private function generateChartData($chart, $files)
    {
        try {
            // Aquí implementarías la lógica de procesamiento de datos
            // Por ahora retornamos datos de ejemplo
            return [
                'chart_id' => $chart->id,
                'chart_name' => $chart->chart_name,
                'chart_type' => $chart->chart_type,
                'config' => $chart->chart_config,
                'data' => [
                    'labels' => ['Ejemplo 1', 'Ejemplo 2', 'Ejemplo 3'],
                    'datasets' => [
                        [
                            'label' => 'Dataset de ejemplo',
                            'data' => [10, 20, 30],
                            'backgroundColor' => '#1f77b4'
                        ]
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error generating chart data: ' . $e->getMessage());
            return null;
        }
    }
}