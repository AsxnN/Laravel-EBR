<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use App\Models\ChartTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ChartController extends Controller
{
    // Definir opciones disponibles para ejes
    private const AXIS_OPTIONS = [
        'x_axis' => [
            'dre' => 'DRE',
            'ugel' => 'UGEL', 
            'departamento' => 'Departamento',
            'provincia' => 'Provincia',
            'distrito' => 'Distrito',
            'centro_poblado' => 'Centro Poblado',
            'codigo_modular' => 'Código Modular',
            'anexo' => 'Anexo',
            'nombre_ie' => 'Nombre IE',
            'modalidad' => 'Modalidad',
            'tipo_ie' => 'Tipo IE'
        ],
        'y_axis' => [
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
        ]
    ];

    public function index()
    {
        $templates = ChartTemplate::with('creator')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $filesCount = UploadedFile::count();

        return view('charts.index', compact('templates', 'filesCount'));
    }

    public function create()
    {
        $files = UploadedFile::with('user')
            ->orderBy('uploaded_at', 'desc')
            ->get();

        $axisOptions = self::AXIS_OPTIONS;
        
        return view('charts.create', compact('files', 'axisOptions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'x_axis' => 'required|string',
                'y_axis' => 'required|string',
                'chart_type' => 'required|in:bar,line,pie,column',
                'purpose' => 'required|string|max:500'
            ]);

            // Validar que los ejes son válidos
            if (!isset(self::AXIS_OPTIONS['x_axis'][$request->x_axis]) || 
                !isset(self::AXIS_OPTIONS['y_axis'][$request->y_axis])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ejes seleccionados no válidos'
                ], 400);
            }

            $template = ChartTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'x_axis' => $request->x_axis,
                'y_axis' => $request->y_axis,
                'chart_type' => $request->chart_type,
                'purpose' => $request->purpose,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla de gráfico creada exitosamente',
                'template' => $template
            ]);

        } catch (\Exception $e) {
            Log::error('Error en store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    public function useTemplate($templateId)
    {
        try {
            $template = ChartTemplate::findOrFail($templateId);
            
            $files = UploadedFile::with('user')
                ->orderBy('uploaded_at', 'desc')
                ->get();

            $axisOptions = self::AXIS_OPTIONS;
            
            return view('charts.use-template', compact('template', 'files', 'axisOptions'));

        } catch (\Exception $e) {
            abort(404, 'Plantilla no encontrada');
        }
    }

    // Asegurar que generateFromTemplate llame al método correcto:
    public function generateFromTemplate(Request $request, $templateId)
    {
        try {
            $template = ChartTemplate::findOrFail($templateId);
            
            $request->validate([
                'file_ids' => 'required|array|min:1',
                'file_ids.*' => 'exists:uploaded_files,id'
            ]);

            $fileIds = $request->file_ids;

            Log::info('=== GENERATE FROM TEMPLATE START ===', [
                'template_id' => $templateId,
                'file_ids' => $fileIds,
                'x_axis' => $template->x_axis,
                'y_axis' => $template->y_axis,
                'chart_type' => $template->chart_type,
                'request_data' => $request->all()
            ]);

            // USAR EL MÉTODO CON NIVELES ASIGNADOS
            $chartData = $this->processMultipleFilesWithLevels($fileIds, $template->x_axis, $template->y_axis, $request);

            Log::info('=== CHART DATA GENERATED ===', [
                'categories_count' => count($chartData['categories']),
                'series_count' => count($chartData['series']),
                'chart_data_structure' => [
                    'categories' => $chartData['categories'],
                    'series' => array_map(function($series) {
                        return [
                            'name' => $series['name'],
                            'data_count' => count($series['data']),
                            'total_value' => array_sum($series['data'])
                        ];
                    }, $chartData['series'])
                ]
            ]);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'config' => [
                    'x_axis' => $template->x_axis,
                    'y_axis' => $template->y_axis,
                    'chart_type' => $template->chart_type,
                    'x_label' => $template->x_axis_label,
                    'y_label' => $template->y_axis_label
                ],
                'template' => [
                    'name' => $template->name,
                    'description' => $template->description,
                    'purpose' => $template->purpose
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('=== GENERATE FROM TEMPLATE ERROR ===', [
                'template_id' => $templateId,
                'file_ids' => $request->file_ids ?? [],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al generar gráfico desde plantilla: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processMultipleFiles($fileIds, $xAxis, $yAxis, $limit = null)
    {
        Log::info('Procesando múltiples archivos', [
            'file_ids' => $fileIds,
            'x_axis' => $xAxis,
            'y_axis' => $yAxis,
            'limit' => $limit
        ]);

        $allData = [];
        $levelOrder = ['Inicial', 'Primaria', 'Secundaria']; // Orden específico
        $processedLevels = [];

        foreach ($fileIds as $fileId) {
            try {
                $file = UploadedFile::findOrFail($fileId);
                
                Log::info('Procesando archivo', [
                    'file_id' => $fileId,
                    'file_path' => $file->file_path,
                    'document_type' => $file->document_type,
                    'original_name' => $file->original_name
                ]);

                if (!Storage::disk('public')->exists($file->file_path)) {
                    Log::warning("Archivo no existe en storage: {$file->file_path}");
                    continue;
                }

                $fileData = $this->extractFileData($file, $xAxis, $yAxis);
                
                Log::info('Datos extraídos del archivo', [
                    'file_id' => $fileId,
                    'document_type' => $file->document_type,
                    'data_count' => count($fileData),
                    'sample_data' => array_slice($fileData, 0, 5, true)
                ]);

                if (!empty($fileData)) {
                    // Detectar el nivel educativo del archivo basado en su contenido o nombre
                    $level = $this->detectEducationalLevel($file);
                    $processedLevels[] = $level;
                    
                    Log::info('Nivel educativo detectado', [
                        'file_id' => $fileId,
                        'original_document_type' => $file->document_type,
                        'original_name' => $file->original_name,
                        'detected_level' => $level
                    ]);
                    
                    foreach ($fileData as $xValue => $yValue) {
                        if (!isset($allData[$xValue])) {
                            $allData[$xValue] = [];
                        }
                        
                        if (!isset($allData[$xValue][$level])) {
                            $allData[$xValue][$level] = 0;
                        }
                        
                        $allData[$xValue][$level] += $yValue;
                        
                        Log::debug('Agregando datos', [
                            'x_value' => $xValue,
                            'level' => $level,
                            'y_value' => $yValue,
                            'accumulated' => $allData[$xValue][$level]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error procesando archivo {$fileId}: " . $e->getMessage());
                continue;
            }
        }

        Log::info('Datos consolidados', [
            'total_categories' => count($allData),
            'processed_levels' => array_unique($processedLevels),
            'sample_consolidated' => array_slice($allData, 0, 3, true)
        ]);

        // Convertir a formato para gráficos
        $chartData = [
            'categories' => [],
            'series' => [],
            'levels' => array_unique($processedLevels)
        ];

        if (empty($allData)) {
            Log::warning('No se encontraron datos para procesar');
            return $chartData;
        }

        // Ordenar por categorías alfabéticamente
        ksort($allData);

        // Limitar resultados si se especifica
        if ($limit) {
            $allData = array_slice($allData, 0, $limit, true);
        }

        // Preparar categorías (eje X)
        $chartData['categories'] = array_keys($allData);

        // Preparar series por nivel educativo en el orden correcto
        $uniqueLevels = array_unique($processedLevels);
        $orderedLevels = [];
        
        // Mantener el orden: Inicial, Primaria, Secundaria
        foreach ($levelOrder as $orderLevel) {
            if (in_array($orderLevel, $uniqueLevels)) {
                $orderedLevels[] = $orderLevel;
            }
        }
        
        // Agregar cualquier nivel que no esté en el orden predefinido
        foreach ($uniqueLevels as $level) {
            if (!in_array($level, $orderedLevels)) {
                $orderedLevels[] = $level;
            }
        }

        Log::info('Niveles ordenados', [
            'processed_levels' => $uniqueLevels,
            'ordered_levels' => $orderedLevels
        ]);

        foreach ($orderedLevels as $level) {
            $seriesData = [];
            
            foreach ($chartData['categories'] as $category) {
                $value = $allData[$category][$level] ?? 0;
                $seriesData[] = $value;
            }
            
            $chartData['series'][] = [
                'name' => $level,
                'data' => $seriesData,
                'color' => $this->getLevelColor($level)
            ];
            
            Log::info('Serie creada', [
                'level' => $level,
                'data_points' => count($seriesData),
                'total_value' => array_sum($seriesData)
            ]);
        }

        $chartData['levels'] = $orderedLevels;

        Log::info('Datos del gráfico final', [
            'categories_count' => count($chartData['categories']),
            'series_count' => count($chartData['series']),
            'levels' => $chartData['levels'],
            'series_summary' => array_map(function($series) {
                return [
                    'name' => $series['name'],
                    'data_points' => count($series['data']),
                    'total' => array_sum($series['data'])
                ];
            }, $chartData['series'])
        ]);

        return $chartData;
    }

    private function extractFileData($file, $xAxis, $yAxis)
    {
        try {
            $fullPath = storage_path('app/public/' . $file->file_path);
            
            if (!file_exists($fullPath)) {
                Log::error("Archivo no existe en el sistema de archivos: {$fullPath}");
                return [];
            }

            Log::info('=== EXTRACTING FILE DATA ===', [
                'file_id' => $file->id,
                'path' => $fullPath,
                'document_type' => $file->document_type,
                'original_name' => $file->original_name,
                'x_axis' => $xAxis,
                'y_axis' => $yAxis
            ]);
            
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $allData = $worksheet->toArray();

            if (empty($allData)) {
                Log::warning("Archivo Excel vacío: {$file->id}");
                return [];
            }

            $headers = array_shift($allData);
            $dataRows = $allData;

            Log::info('=== HEADERS ANALYSIS ===', [
                'file_id' => $file->id,
                'total_headers' => count($headers),
                'headers' => $headers,
                'looking_for_x' => $xAxis,
                'looking_for_y' => $yAxis,
                'total_data_rows' => count($dataRows)
            ]);

            // Mostrar algunos datos de muestra
            Log::info('=== SAMPLE DATA ROWS ===', [
                'file_id' => $file->id,
                'first_3_rows' => array_slice($dataRows, 0, 3)
            ]);

            // Buscar headers de forma más flexible
            $xIndex = $this->findHeaderIndex($headers, $xAxis);
            $yIndex = $this->findHeaderIndex($headers, $yAxis);

            Log::info('=== HEADER SEARCH RESULTS ===', [
                'file_id' => $file->id,
                'x_axis' => $xAxis,
                'y_axis' => $yAxis,
                'x_index' => $xIndex,
                'y_index' => $yIndex,
                'x_header_found' => $xIndex !== false ? $headers[$xIndex] : 'NOT FOUND',
                'y_header_found' => $yIndex !== false ? $headers[$yIndex] : 'NOT FOUND'
            ]);

            if ($xIndex === false || $yIndex === false) {
                Log::error("=== HEADERS NOT FOUND ===", [
                    'file_id' => $file->id,
                    'x_axis' => $xAxis,
                    'y_axis' => $yAxis,
                    'x_index' => $xIndex,
                    'y_index' => $yIndex,
                    'available_headers' => $headers,
                    'file_path' => $file->file_path
                ]);
                return [];
            }

            $extractedData = [];
            $processedRows = 0;
            $validRows = 0;
            $invalidRows = [];

            foreach ($dataRows as $rowIndex => $row) {
                $processedRows++;
                
                Log::debug("=== PROCESSING ROW {$rowIndex} ===", [
                    'file_id' => $file->id,
                    'row_data' => $row,
                    'x_index' => $xIndex,
                    'y_index' => $yIndex,
                    'x_value_raw' => $row[$xIndex] ?? 'MISSING',
                    'y_value_raw' => $row[$yIndex] ?? 'MISSING'
                ]);
                
                if (isset($row[$xIndex]) && isset($row[$yIndex])) {
                    $xValue = trim($row[$xIndex]);
                    $yValue = $row[$yIndex];

                    Log::debug("=== ROW VALUES ===", [
                        'file_id' => $file->id,
                        'row' => $rowIndex,
                        'x_value_trimmed' => $xValue,
                        'y_value_original' => $yValue,
                        'y_value_is_numeric' => is_numeric($yValue)
                    ]);

                    // Convertir Y a número
                    if (is_numeric($yValue)) {
                        $yValue = floatval($yValue);
                    } else {
                        $yValue = 0;
                        Log::debug("Y value converted to 0 (not numeric)", [
                            'file_id' => $file->id,
                            'row' => $rowIndex,
                            'original_y' => $row[$yIndex]
                        ]);
                    }

                    if (!empty($xValue) && $yValue >= 0) {
                        if (!isset($extractedData[$xValue])) {
                            $extractedData[$xValue] = 0;
                        }
                        $extractedData[$xValue] += $yValue;
                        $validRows++;
                        
                        Log::debug("=== VALID ROW ADDED ===", [
                            'file_id' => $file->id,
                            'row' => $rowIndex,
                            'x_value' => $xValue,
                            'y_value' => $yValue,
                            'accumulated' => $extractedData[$xValue]
                        ]);
                    } else {
                        $invalidRows[] = [
                            'row' => $rowIndex,
                            'x_value' => $xValue,
                            'y_value' => $yValue,
                            'x_empty' => empty($xValue),
                            'y_negative' => $yValue < 0
                        ];
                    }
                } else {
                    $invalidRows[] = [
                        'row' => $rowIndex,
                        'missing_x' => !isset($row[$xIndex]),
                        'missing_y' => !isset($row[$yIndex]),
                        'row_length' => count($row)
                    ];
                }
            }

            Log::info('=== EXTRACTION SUMMARY ===', [
                'file_id' => $file->id,
                'document_type' => $file->document_type,
                'processed_rows' => $processedRows,
                'valid_rows' => $validRows,
                'invalid_rows_count' => count($invalidRows),
                'extracted_data_count' => count($extractedData),
                'extracted_data_sample' => array_slice($extractedData, 0, 10, true),
                'first_5_invalid_rows' => array_slice($invalidRows, 0, 5)
            ]);

            if (empty($extractedData)) {
                Log::error("=== NO DATA EXTRACTED ===", [
                    'file_id' => $file->id,
                    'headers' => $headers,
                    'x_axis' => $xAxis,
                    'y_axis' => $yAxis,
                    'x_index' => $xIndex,
                    'y_index' => $yIndex,
                    'total_rows' => count($dataRows),
                    'sample_invalid_rows' => array_slice($invalidRows, 0, 10)
                ]);
            }

            return $extractedData;

        } catch (\Exception $e) {
            Log::error("=== EXTRACTION ERROR ===", [
                'file_id' => $file->id,
                'file_path' => $file->file_path,
                'document_type' => $file->document_type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    private function findHeaderIndex($headers, $searchHeader)
    {
        Log::info("=== SEARCHING FOR HEADER ===", [
            'search_header' => $searchHeader,
            'available_headers' => $headers
        ]);

        // Mapeo de alias para headers comunes
        $headerAliases = [
            'ugel' => ['ugel', 'UGEL',  'dre_ugel', 'cod_ugel', 'codigo_ugel', 'ugel_codigo', 'nombre_ugel'],
            'dre' => ['dre', 'direccion_regional', 'dir_regional'],
            'departamento' => ['departamento', 'dept', 'departament'],
            'provincia' => ['provincia', 'prov'],
            'distrito' => ['distrito', 'dist'],
            'codigo_modular' => ['codigo_modular', 'cod_mod', 'codigo_mod', 'cod_modular'],
            'nombre_ie' => ['nombre_ie', 'nombre_institucion', 'institucion_educativa', 'ie'],
            'total_matriculados' => ['total_matriculados', 'matriculados', 'total_matric', 'tot_matriculados'],
            'matricula_definitiva' => ['matricula_definitiva', 'matric_definitiva', 'def'],
            'matricula_proceso' => ['matricula_proceso', 'matric_proceso', 'proceso']
        ];

        // Obtener aliases para el header buscado
        $searchAliases = $headerAliases[$searchHeader] ?? [$searchHeader];
        
        Log::info("=== HEADER ALIASES ===", [
            'search_header' => $searchHeader,
            'aliases' => $searchAliases
        ]);

        // Buscar coincidencia exacta primero
        foreach ($searchAliases as $alias) {
            $exactIndex = array_search($alias, $headers);
            if ($exactIndex !== false) {
                Log::info("=== EXACT MATCH FOUND ===", [
                    'alias' => $alias,
                    'index' => $exactIndex,
                    'header_value' => $headers[$exactIndex]
                ]);
                return $exactIndex;
            }
        }

        // Buscar ignorando case y espacios
        foreach ($headers as $index => $header) {
            foreach ($searchAliases as $alias) {
                if (strcasecmp(trim($header), trim($alias)) === 0) {
                    Log::info("=== CASE INSENSITIVE MATCH FOUND ===", [
                        'alias' => $alias,
                        'header' => $header,
                        'index' => $index
                    ]);
                    return $index;
                }
            }
        }

        // Buscar contenido parcial
        foreach ($headers as $index => $header) {
            foreach ($searchAliases as $alias) {
                if (stripos(trim($header), trim($alias)) !== false) {
                    Log::info("=== PARTIAL MATCH FOUND ===", [
                        'alias' => $alias,
                        'header' => $header,
                        'index' => $index
                    ]);
                    return $index;
                }
            }
        }

        // Buscar en contenido del header (el alias dentro del header)
        foreach ($headers as $index => $header) {
            foreach ($searchAliases as $alias) {
                if (stripos(trim($alias), trim($header)) !== false) {
                    Log::info("=== REVERSE PARTIAL MATCH FOUND ===", [
                        'alias' => $alias,
                        'header' => $header,
                        'index' => $index
                    ]);
                    return $index;
                }
            }
        }

        Log::error("=== NO HEADER MATCH FOUND ===", [
            'search_header' => $searchHeader,
            'aliases_tried' => $searchAliases,
            'available_headers' => $headers
        ]);

        return false;
    }

    private function getLevelColor($level)
    {
        $colors = [
            'Inicial' => '#3B82F6',   // Azul
            'Primaria' => '#10B981',  // Verde
            'Secundaria' => '#8B5CF6' // Púrpura
        ];

        return $colors[$level] ?? '#6B7280'; // Gris por defecto
    }

    // Agregar este nuevo método para detectar el nivel educativo:
    private function detectEducationalLevel($file)
    {
        $originalName = strtolower($file->original_name);
        $documentType = strtolower($file->document_type);
        
        // Patrones para detectar nivel inicial
        $inicialPatterns = [
            'inicial',
            'jardin',
            'jardín',
            'cuna',
            'pre escolar',
            'preescolar',
            '3 años',
            '4 años',
            '5 años',
            'set-cunas',
            'pronoei'
        ];
        
        // Patrones para detectar nivel primaria
        $primariaPatterns = [
            'primaria',
            'primary',
            '1er grado',
            '2do grado',
            '3er grado',
            '4to grado',
            '5to grado',
            '6to grado',
            'primer grado',
            'segundo grado',
            'tercer grado',
            'cuarto grado',
            'quinto grado',
            'sexto grado'
        ];
        
        // Patrones para detectar nivel secundaria
        $secundariaPatterns = [
            'secundaria',
            'secondary',
            'secundario',
            '1° sec',
            '2° sec',
            '3° sec',
            '4° sec',
            '5° sec',
            'primero de secundaria',
            'segundo de secundaria',
            'tercero de secundaria',
            'cuarto de secundaria',
            'quinto de secundaria'
        ];
        
        // Verificar en el nombre del archivo
        foreach ($inicialPatterns as $pattern) {
            if (strpos($originalName, $pattern) !== false) {
                return 'Inicial';
            }
        }
        
        foreach ($primariaPatterns as $pattern) {
            if (strpos($originalName, $pattern) !== false) {
                return 'Primaria';
            }
        }
        
        foreach ($secundariaPatterns as $pattern) {
            if (strpos($originalName, $pattern) !== false) {
                return 'Secundaria';
            }
        }
        
        // Verificar en el document_type
        foreach ($inicialPatterns as $pattern) {
            if (strpos($documentType, $pattern) !== false) {
                return 'Inicial';
            }
        }
        
        foreach ($primariaPatterns as $pattern) {
            if (strpos($documentType, $pattern) !== false) {
                return 'Primaria';
            }
        }
        
        foreach ($secundariaPatterns as $pattern) {
            if (strpos($documentType, $pattern) !== false) {
                return 'Secundaria';
            }
        }
        
        // Si no se puede detectar automáticamente, usar el document_type original
        $normalizedType = ucfirst(strtolower($documentType));
        
        // Mapeo directo si coincide con los valores esperados
        if (in_array($normalizedType, ['Inicial', 'Primaria', 'Secundaria'])) {
            return $normalizedType;
        }
        
        // Si no se puede determinar, preguntar al usuario (por ahora retornar un valor por defecto)
        Log::warning("No se pudo determinar el nivel educativo del archivo", [
            'file_id' => $file->id,
            'original_name' => $file->original_name,
            'document_type' => $file->document_type
        ]);
        
        // Por defecto, intentar usar el document_type tal como está
        return ucfirst(strtolower($file->document_type));
    }

    private function processMultipleFilesWithLevels($fileIds, $xAxis, $yAxis, $request, $limit = null)
    {
        Log::info('Procesando múltiples archivos con niveles asignados', [
            'file_ids' => $fileIds,
            'x_axis' => $xAxis,
            'y_axis' => $yAxis,
            'limit' => $limit
        ]);

        $allData = [];
        $levelOrder = ['Inicial', 'Primaria', 'Secundaria'];
        $processedLevels = [];

        // Obtener los niveles asignados desde el frontend
        $assignedLevelsJson = $request->input('assigned_levels', '{}');
        $assignedLevels = json_decode($assignedLevelsJson, true) ?? [];

        Log::info('Niveles asignados desde frontend', [
            'assigned_levels_raw' => $assignedLevelsJson,
            'assigned_levels_parsed' => $assignedLevels
        ]);

        foreach ($fileIds as $fileId) {
            try {
                $file = UploadedFile::findOrFail($fileId);
                
                // Usar el nivel asignado por el usuario, o detectar automáticamente
                $level = $assignedLevels[$fileId] ?? $this->detectEducationalLevel($file);
                
                Log::info('Procesando archivo con nivel asignado', [
                    'file_id' => $fileId,
                    'file_path' => $file->file_path,
                    'document_type' => $file->document_type,
                    'original_name' => $file->original_name,
                    'assigned_level' => $level
                ]);

                if (!Storage::disk('public')->exists($file->file_path)) {
                    Log::warning("Archivo no existe en storage: {$file->file_path}");
                    continue;
                }

                $fileData = $this->extractFileData($file, $xAxis, $yAxis);
                
                Log::info('Datos extraídos del archivo', [
                    'file_id' => $fileId,
                    'document_type' => $file->document_type,
                    'assigned_level' => $level,
                    'data_count' => count($fileData),
                    'sample_data' => array_slice($fileData, 0, 5, true)
                ]);

                if (!empty($fileData)) {
                    $processedLevels[] = $level;
                    
                    foreach ($fileData as $xValue => $yValue) {
                        if (!isset($allData[$xValue])) {
                            $allData[$xValue] = [];
                        }
                        
                        if (!isset($allData[$xValue][$level])) {
                            $allData[$xValue][$level] = 0;
                        }
                        
                        $allData[$xValue][$level] += $yValue;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error procesando archivo {$fileId}: " . $e->getMessage());
                continue;
            }
        }

        Log::info('Datos consolidados con niveles asignados', [
            'total_categories' => count($allData),
            'processed_levels' => array_unique($processedLevels),
            'sample_consolidated' => array_slice($allData, 0, 3, true)
        ]);

        $chartData = [
            'categories' => [],
            'series' => [],
            'levels' => array_unique($processedLevels)
        ];

        if (empty($allData)) {
            Log::warning('No se encontraron datos para procesar');
            return $chartData;
        }

        ksort($allData);

        if ($limit) {
            $allData = array_slice($allData, 0, $limit, true);
        }

        $chartData['categories'] = array_keys($allData);

        $uniqueLevels = array_unique($processedLevels);
        $orderedLevels = [];
        
        foreach ($levelOrder as $orderLevel) {
            if (in_array($orderLevel, $uniqueLevels)) {
                $orderedLevels[] = $orderLevel;
            }
        }
        
        foreach ($uniqueLevels as $level) {
            if (!in_array($level, $orderedLevels)) {
                $orderedLevels[] = $level;
            }
        }

        foreach ($orderedLevels as $level) {
            $seriesData = [];
            
            foreach ($chartData['categories'] as $category) {
                $value = $allData[$category][$level] ?? 0;
                $seriesData[] = $value;
            }
            
            $chartData['series'][] = [
                'name' => $level,
                'data' => $seriesData,
                'color' => $this->getLevelColor($level)
            ];
        }

        $chartData['levels'] = $orderedLevels;

        Log::info('Datos finales del gráfico', [
            'categories_count' => count($chartData['categories']),
            'series_count' => count($chartData['series']),
            'levels' => $chartData['levels']
        ]);

        return $chartData;
    }

    // Agregar un nuevo método para procesar datos específicamente para gráficos de pie:
    private function processDataForPieChart($chartData)
    {
        // Para gráficos de pie, necesitamos convertir las series múltiples en una sola serie
        $pieData = [];
        
        foreach ($chartData['series'] as $series) {
            $total = array_sum($series['data']);
            if ($total > 0) {
                $pieData[] = [
                    'name' => $series['name'],
                    'y' => $total,
                    'color' => $series['color']
                ];
            }
        }
        
        return [
            'series' => $pieData,
            'categories' => array_column($pieData, 'name')
        ];
    }
}