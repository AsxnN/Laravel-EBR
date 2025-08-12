<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Models\ChartTemplate;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ComparisonController extends Controller
{
    public function index()
    {
        $comparisons = Comparison::with(['template', 'files', 'creator'])
            ->latest()
            ->paginate(12);

        // Calcular estadísticas
        $totalComparisons = Comparison::count();
        $readyComparisons = Comparison::where('status', 'ready')->count();
        $processingComparisons = Comparison::where('status', 'processing')->count();
        $errorComparisons = Comparison::where('status', 'error')->count();

        // Calcular archivos únicos utilizados en todas las comparaciones
        $totalFilesUsed = DB::table('comparison_file')
            ->distinct('uploaded_file_id')
            ->count('uploaded_file_id');

        // Comparaciones creadas esta semana
        $recentComparisons = Comparison::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        return view('comparisons.index', compact(
            'comparisons',
            'totalComparisons',
            'readyComparisons',
            'processingComparisons',
            'errorComparisons',
            'totalFilesUsed',
            'recentComparisons'
        ));
    }

    public function create()
    {
        // Obtener plantillas activas agrupadas por tipo
        $singleLevelTemplates = ChartTemplate::where('template_type', 'single_level')
            ->where('status', 'active')
            ->with('charts')
            ->get()
            ->groupBy(function($template) {
                return $template->education_levels[0]; // Agrupar por nivel educativo
            });

        $multiLevelTemplates = ChartTemplate::where('template_type', 'multi_level')
            ->where('status', 'active')
            ->with('charts')
            ->get();

        // Obtener archivos disponibles agrupados por tipo de documento
        $availableFiles = $this->getFilesByEducationLevel();

        // Obtener períodos disponibles para filtros
        $availablePeriods = $this->getAvailablePeriods();

        // Obtener filtros geográficos únicos
        $geoFilters = $this->getGeoFilters();

        return view('comparisons.create', compact(
            'singleLevelTemplates',
            'multiLevelTemplates',
            'availableFiles',
            'availablePeriods',
            'geoFilters'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'comparison_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_id' => 'required|exists:chart_templates,id',
            'selected_files' => 'required|array|min:1',
            'selected_files.*' => 'exists:uploaded_files,id',
            'comparison_period' => 'nullable|string',
            'geo_filters' => 'nullable|array',
            'geo_filters.dre' => 'nullable|array',
            'geo_filters.ugel' => 'nullable|array',
            'geo_filters.departamento' => 'nullable|array',
            'geo_filters.provincia' => 'nullable|array',
            'geo_filters.distrito' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $template = ChartTemplate::findOrFail($request->template_id);

            // Crear la comparación
            $comparison = Comparison::create([
                'name' => $request->comparison_name,
                'description' => $request->description,
                'template_id' => $request->template_id,
                'comparison_type' => $template->template_type,
                'education_levels' => $template->education_levels,
                'comparison_period' => $request->comparison_period,
                'geo_filters' => $request->geo_filters ? json_encode($request->geo_filters) : null,
                'created_by' => auth()->id(),
                'status' => 'processing'
            ]);

            // Asociar archivos seleccionados
            $comparison->files()->attach($request->selected_files);

            // Procesar datos y generar gráficos
            $this->processComparisonData($comparison);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comparación creada exitosamente',
                'redirect_url' => route('comparisons.show', $comparison)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating comparison: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la comparación: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Comparison $comparison)
    {
        $comparison->load(['template.charts', 'files', 'creator']);

        // Obtener datos de los gráficos
        $chartsData = [];
        if ($comparison->charts_data) {
            $chartsData = is_string($comparison->charts_data) 
                ? json_decode($comparison->charts_data, true) 
                : $comparison->charts_data;
        }

        // Estadísticas de la comparación
        $stats = [
            'total_files' => $comparison->files->count(),
            'total_institutions' => $comparison->total_institutions ?? 0,
            'total_students' => $comparison->total_students ?? 0,
            'education_levels' => $comparison->education_levels,
            'creation_date' => $comparison->created_at->format('d/m/Y H:i'),
            'processing_time' => $comparison->updated_at->diffInSeconds($comparison->created_at) . ' segundos'
        ];

        return view('comparisons.show', compact('comparison', 'chartsData', 'stats'));
    }

    public function destroy(Comparison $comparison)
    {
        try {
            // Eliminar archivos asociados si existen
            if ($comparison->dataset_path && file_exists(storage_path('app/' . $comparison->dataset_path))) {
                unlink(storage_path('app/' . $comparison->dataset_path));
            }

            $comparison->delete();
            
            return redirect()->route('comparisons.index')
                ->with('success', 'Comparación eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error deleting comparison: ' . $e->getMessage());
            
            return redirect()->route('comparisons.index')
                ->with('error', 'Error al eliminar la comparación');
        }
    }

    public function downloadReport(Comparison $comparison)
    {
        try {
            if (!$comparison->dataset_path || !file_exists(storage_path('app/' . $comparison->dataset_path))) {
                return redirect()->route('comparisons.show', $comparison)
                    ->with('error', 'Archivo de reporte no encontrado');
            }
            
            $fileName = 'reporte_' . $comparison->name . '_' . $comparison->created_at->format('Y-m-d') . '.xlsx';
            
            return response()->download(
                storage_path('app/' . $comparison->dataset_path),
                $fileName
            );
            
        } catch (\Exception $e) {
            Log::error('Error downloading report: ' . $e->getMessage());
            
            return redirect()->route('comparisons.show', $comparison)
                ->with('error', 'Error al descargar el reporte');
        }
    }

    public function regenerateCharts(Request $request, Comparison $comparison)
    {
        $request->validate([
            'chart_ids' => 'nullable|array',
            'chart_ids.*' => 'integer'
        ]);

        try {
            // Si se especifican IDs de gráficos específicos, solo regenerar esos
            $chartsToRegenerate = $request->chart_ids 
                ? $comparison->template->charts->whereIn('id', $request->chart_ids)
                : $comparison->template->charts;

            $newChartsData = [];
            $existingChartsData = $comparison->charts_data ? 
                (is_string($comparison->charts_data) ? json_decode($comparison->charts_data, true) : $comparison->charts_data) 
                : [];

            // Mantener gráficos no regenerados
            foreach ($existingChartsData as $chartData) {
                if (!$request->chart_ids || !in_array($chartData['chart_id'], $request->chart_ids)) {
                    $newChartsData[] = $chartData;
                }
            }

            // Generar nuevos gráficos
            foreach ($chartsToRegenerate as $chart) {
                $chartData = $this->generateChartData($chart, $comparison->files, $comparison);
                if ($chartData) {
                    $newChartsData[] = $chartData;
                }
            }

            // Actualizar comparación
            $comparison->update([
                'charts_data' => $newChartsData,
                'status' => 'ready'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gráficos regenerados exitosamente',
                'charts_count' => count($newChartsData)
            ]);

        } catch (\Exception $e) {
            Log::error('Error regenerating charts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al regenerar los gráficos: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== MÉTODOS PRIVADOS ==========

    private function getFilesByEducationLevel()
    {
        $files = UploadedFile::orderBy('uploaded_at', 'desc')->get();
        
        $grouped = [
            'inicial' => collect([]),
            'primaria' => collect([]),
            'secundaria' => collect([])
        ];

        foreach ($files as $file) {
            $docType = strtolower($file->document_type);
            if (isset($grouped[$docType])) {
                $grouped[$docType]->push($file);
            } else {
                // Si no se puede determinar por document_type, intentar por nombre
                $fileName = strtolower($file->original_name);
                if (str_contains($fileName, 'inicial')) {
                    $grouped['inicial']->push($file);
                } elseif (str_contains($fileName, 'primaria')) {
                    $grouped['primaria']->push($file);
                } elseif (str_contains($fileName, 'secundaria')) {
                    $grouped['secundaria']->push($file);
                }
            }
        }

        return $grouped;
    }

    private function getAvailablePeriods()
    {
        return UploadedFile::select(DB::raw("DATE_FORMAT(uploaded_at, '%Y-%m') as period"))
            ->distinct()
            ->orderBy('period', 'desc')
            ->pluck('period')
            ->toArray();
    }

    private function getGeoFilters()
    {
        $filters = [
            'dre' => [],
            'ugel' => [],
            'departamento' => [],
            'provincia' => [],
            'distrito' => []
        ];

        try {
            $sampleFile = UploadedFile::first();
            if ($sampleFile && $sampleFile->table_name && Schema::hasTable($sampleFile->table_name)) {
                $tableName = $sampleFile->table_name;
                
                foreach (array_keys($filters) as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $filters[$column] = DB::table($tableName)
                            ->whereNotNull($column)
                            ->where($column, '!=', '')
                            ->distinct()
                            ->pluck($column)
                            ->filter()
                            ->sort()
                            ->values()
                            ->toArray();
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error loading geo filters: ' . $e->getMessage());
        }

        return $filters;
    }

    private function processComparisonData(Comparison $comparison)
    {
        try {
            $template = $comparison->template;
            $files = $comparison->files;
            $chartsData = [];

            // Procesar cada gráfico de la plantilla
            foreach ($template->charts as $chart) {
                $chartData = $this->generateChartData($chart, $files, $comparison);
                if ($chartData) {
                    $chartsData[] = $chartData;
                }
            }

            // Calcular estadísticas totales
            $totalInstitutions = $files->sum('total_institutions');
            $totalStudents = $files->sum('total_students');

            // Generar archivo de datos si es necesario
            $datasetPath = null;
            if ($comparison->template->template_type === 'multi_level') {
                $datasetPath = $this->generateDatasetFile($comparison, $files);
            }

            // Actualizar comparación con resultados
            $comparison->update([
                'charts_data' => $chartsData,
                'status' => 'ready',
                'total_institutions' => $totalInstitutions,
                'total_students' => $totalStudents,
                'dataset_path' => $datasetPath
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing comparison data: ' . $e->getMessage());
            $comparison->update(['status' => 'error']);
            throw $e;
        }
    }

    private function generateChartData($chart, $files, $comparison)
    {
        try {
            $chartConfig = is_string($chart->chart_config) ? json_decode($chart->chart_config, true) : $chart->chart_config;
            
            // Obtener configuración de ejes
            $xAxisField = $chart->x_axis_field;
            $yAxisFields = is_string($chart->y_axis_fields) ? json_decode($chart->y_axis_fields, true) : $chart->y_axis_fields;

            // Procesar datos según tipo de plantilla
            if ($comparison->template->template_type === 'single_level') {
                $processedData = $this->processSingleLevelData($files, $xAxisField, $yAxisFields, $comparison);
            } else {
                $processedData = $this->processMultiLevelData($files, $xAxisField, $yAxisFields, $comparison);
            }

            // Preparar configuración del gráfico
            $chartData = [
                'chart_id' => $chart->id,
                'chart_name' => $chart->chart_name,
                'chart_type' => $chart->chart_type,
                'x_axis_field' => $xAxisField,
                'y_axis_fields' => $yAxisFields,
                'education_level' => $chart->education_level,
                'config' => $chartConfig,
                'data' => $processedData
            ];

            return $chartData;

        } catch (\Exception $e) {
            Log::error('Error generating chart data for chart ' . $chart->id . ': ' . $e->getMessage());
            return null;
        }
    }

    private function processSingleLevelData($files, $xAxisField, $yAxisFields, $comparison)
    {
        $labels = [];
        $datasets = [];
        
        // Preparar datasets para cada campo Y
        foreach ($yAxisFields as $yField) {
            $datasets[] = [
                'label' => $this->getFieldLabel($yField),
                'data' => [],
                'backgroundColor' => $this->getFieldColor($yField),
                'borderColor' => $this->getFieldColor($yField),
                'borderWidth' => 1
            ];
        }

        // Procesar cada archivo
        foreach ($files as $file) {
            if (!$file->table_name || !Schema::hasTable($file->table_name)) {
                continue;
            }

            // Obtener datos agrupados por el campo X
            $groupedData = DB::table($file->table_name)
                ->select($xAxisField, ...array_map(function($field) {
                    return DB::raw("SUM($field) as $field");
                }, $yAxisFields))
                ->groupBy($xAxisField)
                ->get();

            foreach ($groupedData as $row) {
                $xValue = $row->{$xAxisField};
                
                if (!in_array($xValue, $labels)) {
                    $labels[] = $xValue;
                }

                $labelIndex = array_search($xValue, $labels);
                
                foreach ($yAxisFields as $index => $yField) {
                    if (!isset($datasets[$index]['data'][$labelIndex])) {
                        $datasets[$index]['data'][$labelIndex] = 0;
                    }
                    $datasets[$index]['data'][$labelIndex] += $row->{$yField} ?? 0;
                }
            }
        }

        // Llenar valores faltantes con 0
        foreach ($datasets as &$dataset) {
            for ($i = 0; $i < count($labels); $i++) {
                if (!isset($dataset['data'][$i])) {
                    $dataset['data'][$i] = 0;
                }
            }
            $dataset['data'] = array_values($dataset['data']);
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function processMultiLevelData($files, $xAxisField, $yAxisFields, $comparison)
    {
        $labels = [];
        $datasets = [];
        
        // Agrupar archivos por nivel educativo
        $filesByLevel = [];
        foreach ($files as $file) {
            $level = strtolower($file->document_type);
            if (!isset($filesByLevel[$level])) {
                $filesByLevel[$level] = [];
            }
            $filesByLevel[$level][] = $file;
        }

        // Procesar cada nivel educativo como una serie de datos
        foreach ($filesByLevel as $level => $levelFiles) {
            foreach ($yAxisFields as $yField) {
                $dataset = [
                    'label' => ucfirst($level) . ' - ' . $this->getFieldLabel($yField),
                    'data' => [],
                    'backgroundColor' => $this->getLevelFieldColor($level, $yField),
                    'borderColor' => $this->getLevelFieldColor($level, $yField),
                    'borderWidth' => 1
                ];

                $levelData = [];
                
                foreach ($levelFiles as $file) {
                    if (!$file->table_name || !Schema::hasTable($file->table_name)) {
                        continue;
                    }

                    $groupedData = DB::table($file->table_name)
                        ->select($xAxisField, DB::raw("SUM($yField) as total"))
                        ->groupBy($xAxisField)
                        ->get();

                    foreach ($groupedData as $row) {
                        $xValue = $row->{$xAxisField};
                        
                        if (!in_array($xValue, $labels)) {
                            $labels[] = $xValue;
                        }

                        if (!isset($levelData[$xValue])) {
                            $levelData[$xValue] = 0;
                        }
                        $levelData[$xValue] += $row->total ?? 0;
                    }
                }

                // Convertir a array ordenado
                foreach ($labels as $label) {
                    $dataset['data'][] = $levelData[$label] ?? 0;
                }

                $datasets[] = $dataset;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function generateDatasetFile($comparison, $files)
    {
        // Implementar generación de archivo Excel/CSV con datos procesados
        // Por ahora retornamos null, se implementará posteriormente
        return null;
    }

    private function getFieldLabel($field)
    {
        $labels = [
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
            'nomina_por_rectificar' => 'Nómina por Rectificar',
            // Campos específicos por nivel se agregarían aquí
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    private function getFieldColor($field)
    {
        $colors = [
            'total_matriculados' => '#1f77b4',
            'matricula_definitiva' => '#ff7f0e',
            'matricula_proceso' => '#2ca02c',
            'dni_validado' => '#d62728',
            'dni_sin_validar' => '#9467bd',
            'registro_sin_dni' => '#8c564b',
            'total_grados' => '#e377c2',
            'total_secciones' => '#7f7f7f',
            'nomina_generada' => '#bcbd22',
            'nomina_aprobada' => '#17becf',
            'nomina_por_rectificar' => '#aec7e8',
        ];

        return $colors[$field] ?? '#' . substr(md5($field), 0, 6);
    }

    private function getLevelFieldColor($level, $field)
    {
        $levelColors = [
            'inicial' => ['#e3f2fd', '#bbdefb', '#90caf9', '#64b5f6', '#42a5f5'],
            'primaria' => ['#e8f5e8', '#c8e6c9', '#a5d6a7', '#81c784', '#66bb6a'],
            'secundaria' => ['#fce4ec', '#f8bbd9', '#f48fb1', '#f06292', '#ec407a']
        ];

        $colors = $levelColors[$level] ?? ['#f5f5f5', '#e0e0e0', '#bdbdbd', '#9e9e9e', '#757575'];
        $index = abs(crc32($field)) % count($colors);
        
        return $colors[$index];
    }
}