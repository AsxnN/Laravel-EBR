<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use App\Models\ChartTemplate;
use App\Services\FileDataProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SingleLevelChartController extends Controller
{
    protected $fileProcessor;

    public function __construct(FileDataProcessor $fileProcessor)
    {
        $this->fileProcessor = $fileProcessor;
    }

    public function generateFromTemplate(Request $request, $templateId)
    {
        try {
            $template = ChartTemplate::findOrFail($templateId);
            
            if ($template->level_type !== 'single') {
                Log::warning('Plantilla no es de tipo single', ['level_type' => $template->level_type]);
                return response()->json([
                    'success' => false,
                    'message' => 'Esta plantilla no es para un solo nivel'
                ]);
            }

            $fileIds = $request->input('file_ids', []);
            $selectedLevel = $request->input('selected_level');
            
            Log::info('Single Level - Datos recibidos:', [
                'file_ids' => $fileIds,
                'selected_level' => $selectedLevel,
                'chart_type' => $template->chart_type
            ]);
            
            if (empty($fileIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecciona al menos un archivo'
                ]);
            }

            if (!$selectedLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selecciona un nivel educativo'
                ]);
            }

            Log::info('Generando gráfico single-level', [
                'template_id' => $templateId,
                'file_ids' => $fileIds,
                'selected_level' => $selectedLevel,
                'chart_type' => $template->chart_type
            ]);

            if ($template->chart_type === 'table') {
                $chartData = $this->processDataForTable($fileIds, $template->x_axis, $template->y_axis, $selectedLevel);
            } else {
                $chartData = $this->processSingleLevelFiles($fileIds, $template->x_axis, $template->y_axis, $selectedLevel);
            }
            
            if (!$chartData || empty($chartData['series'])) {
                Log::error('No se generaron datos para el gráfico single-level');
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron procesar los datos de los archivos seleccionados'
                ]);
            }

            $config = [
                'chart_type' => $template->chart_type,
                'x_axis' => $template->x_axis,
                'y_axis' => $template->y_axis,
                'x_label' => $template->x_axis_label,
                'y_label' => $template->y_axis_label,
                'selected_level' => $selectedLevel,
                'level_type' => 'single'
            ];

            Log::info('Respuesta single-level exitosa preparada', [
                'categories_count' => count($chartData['categories'] ?? []),
                'series_count' => count($chartData['series'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'config' => $config,
                'template' => $template
            ]);

        } catch (\Exception $e) {
            Log::error('Error en generateFromTemplate (SingleLevel)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'template_id' => $templateId ?? 'unknown',
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processDataForTable($fileIds, $xAxis, $yAxis, $selectedLevel)
    {
        Log::info('Procesando datos para tabla single-level', [
            'file_ids' => $fileIds,
            'x_axis' => $xAxis,
            'y_axis' => $yAxis,
            'selected_level' => $selectedLevel
        ]);

        $allData = [];
        $fileNames = [];

        foreach ($fileIds as $fileId) {
            $file = UploadedFile::find($fileId);
            if (!$file) {
                Log::warning("Archivo no encontrado: {$fileId}");
                continue;
            }

            // ✅ Usar extractReadableName
            $fileName = $this->extractReadableName($file);
            $fileNames[$fileId] = $fileName;
            
            Log::info("Procesando archivo {$fileId} con nombre: {$fileName}");

            $fileData = $this->extractFileData($file, $xAxis, $yAxis);
            
            if ($fileData && !empty($fileData)) {
                foreach ($fileData as $category => $value) {
                    if (!isset($allData[$category])) {
                        $allData[$category] = [];
                    }
                    $allData[$category][$fileId] = $value;
                }
            } else {
                Log::warning("No se pudieron extraer datos del archivo: {$fileId}");
            }
        }

        if (empty($allData)) {
            Log::error('No se procesaron datos de ningún archivo');
            return null;
        }

        ksort($allData);
        $categories = array_keys($allData);

        $series = [];
        $colorPalette = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316'];
        $colorIndex = 0;

        foreach ($fileIds as $fileId) {
            $fileData = [];
            foreach ($categories as $category) {
                $fileData[] = $allData[$category][$fileId] ?? 0;
            }
            
            $series[] = [
                'name' => $fileNames[$fileId],
                'data' => $fileData,
                'color' => $colorPalette[$colorIndex % count($colorPalette)]
            ];
            $colorIndex++;
        }

        $totals = [];
        foreach ($categories as $category) {
            $total = 0;
            foreach ($fileIds as $fileId) {
                $total += $allData[$category][$fileId] ?? 0;
            }
            $totals[] = $total;
        }

        return [
            'categories' => $categories,
            'series' => $series,
            'totals' => $totals,
            'level' => $selectedLevel,
            'type' => 'single_level_table'
        ];
    }

    private function processSingleLevelFiles($fileIds, $xAxis, $yAxis, $selectedLevel)
    {
        try {
            $allData = [];
            $fileNames = [];

            Log::info('Procesando archivos single-level:', [
                'fileIds' => $fileIds,
                'selectedLevel' => $selectedLevel
            ]);

            foreach ($fileIds as $fileId) {
                $file = UploadedFile::find($fileId);
                if (!$file) {
                    Log::warning("Archivo no encontrado: {$fileId}");
                    continue;
                }

                // ✅ Usar extractReadableName
                $fileName = $this->extractReadableName($file);
                $fileNames[$fileId] = $fileName;
                
                Log::info("Procesando archivo {$fileId} con nombre: {$fileName}");

                $fileData = $this->extractFileData($file, $xAxis, $yAxis);
                
                if ($fileData && !empty($fileData)) {
                    foreach ($fileData as $category => $value) {
                        if (!isset($allData[$category])) {
                            $allData[$category] = [];
                        }
                        $allData[$category][$fileId] = $value;
                    }
                } else {
                    Log::warning("No se pudieron extraer datos del archivo: {$fileId}");
                }
            }

            if (empty($allData)) {
                Log::error('No se procesaron datos de ningún archivo');
                return null;
            }

            ksort($allData);
            $categories = array_keys($allData);

            $series = [];
            $colorPalette = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316'];
            $colorIndex = 0;

            foreach ($fileIds as $fileId) {
                $fileData = [];
                foreach ($categories as $category) {
                    $fileData[] = $allData[$category][$fileId] ?? 0;
                }
                
                $series[] = [
                    'name' => $fileNames[$fileId],
                    'data' => $fileData,
                    'color' => $colorPalette[$colorIndex % count($colorPalette)]
                ];
                $colorIndex++;
            }

            $result = [
                'categories' => $categories,
                'series' => $series,
                'level' => $selectedLevel,
                'type' => 'single_level'
            ];
            
            Log::info('Resultado single-level procesado exitosamente:', [
                'categories_count' => count($result['categories']),
                'series_count' => count($result['series'])
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Error en processSingleLevelFiles', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * ✅ FUNCIÓN MEJORADA: Extraer nombre legible del archivo
     */
    private function extractReadableName($file)
    {
        $fileName = basename($file->file_path ?? $file->original_name ?? 'Archivo');
        $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
        
        Log::info("🔍 Extrayendo nombre legible de: {$nameWithoutExt}");
        
        // Intentar extraer año (4 dígitos)
        if (preg_match('/(\d{4})/', $nameWithoutExt, $matches)) {
            $year = $matches[1];
            
            $monthNames = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            
            // Patrón: _MM_YYYY
            if (preg_match('/_(\d{2})_' . $year . '/', $nameWithoutExt, $monthMatch)) {
                $month = $monthMatch[1];
                if (isset($monthNames[$month])) {
                    $result = $monthNames[$month] . ' ' . $year;
                    Log::info("✅ Patrón _MM_YYYY: {$result}");
                    return $result;
                }
            }
            
            // Patrón: MM_YYYY
            if (preg_match('/(\d{2})_' . $year . '/', $nameWithoutExt, $monthMatch)) {
                $month = $monthMatch[1];
                if (isset($monthNames[$month])) {
                    $result = $monthNames[$month] . ' ' . $year;
                    Log::info("✅ Patrón MM_YYYY: {$result}");
                    return $result;
                }
            }
            
            // Patrón: YYYY-MM-DD
            if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $nameWithoutExt, $dateMatch)) {
                $month = $dateMatch[2];
                if (isset($monthNames[$month])) {
                    $result = $monthNames[$month] . ' ' . $dateMatch[1];
                    Log::info("✅ Patrón YYYY-MM-DD: {$result}");
                    return $result;
                }
            }
            
            // Patrón: DD-MM-YYYY
            if (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $nameWithoutExt, $dateMatch)) {
                $month = $dateMatch[2];
                $year = $dateMatch[3];
                if (isset($monthNames[$month])) {
                    $result = $monthNames[$month] . ' ' . $year;
                    Log::info("✅ Patrón DD-MM-YYYY: {$result}");
                    return $result;
                }
            }
            
            Log::info("✅ Solo año: {$year}");
            return $year;
        }
        
        // Si no hay año, usar fecha de carga
        if ($file->uploaded_at) {
            $uploadDate = $file->uploaded_at;
            $monthNames = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            
            $result = $monthNames[$uploadDate->month] . ' ' . $uploadDate->year;
            Log::info("✅ Fecha de carga: {$result}");
            return $result;
        }
        
        // Último recurso
        if (strlen($nameWithoutExt) > 20) {
            $result = substr($nameWithoutExt, 0, 17) . '...';
            Log::info("⚠️ Truncado: {$result}");
            return $result;
        }
        
        Log::info("ℹ️ Nombre original: {$nameWithoutExt}");
        return $nameWithoutExt;
    }

    private function extractFileData($file, $xAxis, $yAxis)
    {
        try {
            $fullPath = storage_path('app/public/' . $file->file_path);
            
            if (!file_exists($fullPath)) {
                Log::error("Archivo no existe: {$fullPath}");
                return [];
            }

            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $allData = $worksheet->toArray();

            if (empty($allData)) {
                Log::warning("Excel vacío: {$file->id}");
                return [];
            }

            $headers = array_shift($allData);
            $dataRows = $allData;

            $xIndex = $this->findHeaderIndex($headers, $xAxis);
            $yIndex = $this->findHeaderIndex($headers, $yAxis);

            if ($xIndex === false || $yIndex === false) {
                Log::error("Headers no encontrados", [
                    'x_axis' => $xAxis,
                    'y_axis' => $yAxis,
                    'available' => $headers
                ]);
                return [];
            }

            $extractedData = [];

            foreach ($dataRows as $row) {
                if (isset($row[$xIndex]) && isset($row[$yIndex])) {
                    $xValue = trim($row[$xIndex]);
                    $yValue = is_numeric($row[$yIndex]) ? floatval($row[$yIndex]) : 0;

                    if (!empty($xValue) && $yValue >= 0) {
                        if (!isset($extractedData[$xValue])) {
                            $extractedData[$xValue] = 0;
                        }
                        $extractedData[$xValue] += $yValue;
                    }
                }
            }

            return $extractedData;

        } catch (\Exception $e) {
            Log::error("Error extrayendo datos", [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    private function findHeaderIndex($headers, $searchHeader)
    {
        $headerAliases = [
            'ugel' => ['ugel', 'UGEL', 'dre_ugel', 'cod_ugel'],
            'total_matriculados' => ['total_matriculados', 'matriculados', 'total_matric'],
            'total_secciones' => ['total_secciones', 'secciones', 'secc'],
        ];

        $searchAliases = $headerAliases[$searchHeader] ?? [$searchHeader];

        foreach ($searchAliases as $alias) {
            $exactIndex = array_search($alias, $headers);
            if ($exactIndex !== false) return $exactIndex;
        }

        foreach ($headers as $index => $header) {
            foreach ($searchAliases as $alias) {
                if (strcasecmp(trim($header), trim($alias)) === 0) return $index;
                if (stripos(trim($header), trim($alias)) !== false) return $index;
            }
        }

        return false;
    }
}