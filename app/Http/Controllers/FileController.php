<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class FileController extends Controller
{
    // Definir encabezados específicos por nivel académico
    private const HEADERS = [
        'inicial' => [
            'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
            'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
            'total_matriculados', 'matricula_definitiva', 'matricula_proceso', 
            'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
            'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
            'nomina_por_rectificar', 'cero_hombres', 'cero_mujeres', 'primero_hombres',
            'primero_mujeres', 'segundo_hombres', 'segundo_mujeres', 'tercero_hombres',
            'tercero_mujeres', 'cuarto_hombres', 'cuarto_mujeres', 'quinto_hombres',
            'quinto_mujeres', 'mas_quinto_hombres', 'mas_quinto_mujeres'
        ],
        'primaria' => [
            'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
            'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
            'total_matriculados', 'matricula_definitiva', 'matricula_proceso', 
            'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
            'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
            'nomina_por_rectificar', 'primero_hombres', 'primero_mujeres', 'segundo_hombres',
            'segundo_mujeres', 'tercero_hombres', 'tercero_mujeres', 'cuarto_hombres',
            'cuarto_mujeres', 'quinto_hombres', 'quinto_mujeres', 'sexto_hombres',
            'sexto_mujeres'
        ],
        'secundaria' => [
            'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
            'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
            'total_matriculados', 'matricula_definitiva', 'matricula_proceso', 
            'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
            'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
            'nomina_por_rectificar', 'primero_hombres', 'primero_mujeres', 'segundo_hombres',
            'segundo_mujeres', 'tercero_hombres', 'tercero_mujeres', 'cuarto_hombres',
            'cuarto_mujeres', 'quinto_hombres', 'quinto_mujeres'
        ]
    ];

    public function index()
    {
        $filesByMonth = UploadedFile::with('user')
            ->orderBy('uploaded_at', 'desc')
            ->get()
            ->groupBy(function($file) {
                return $file->uploaded_at->format('Y-m');
            });

        return view('files.index', compact('filesByMonth'));
    }

    public function create()
    {
        return view('files.upload');
    }

    public function upload(Request $request)
    {
        try {
            Log::info('=== INICIANDO UPLOAD ===');
            
            $request->headers->set('Accept', 'application/json');
            
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'document_type' => 'required|in:inicial,primaria,secundaria'
            ]);

            $file = $request->file('excel_file');
            $documentType = $request->document_type;
            
            Log::info("Archivo: {$file->getClientOriginalName()}, Tipo: $documentType");
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo subido no es válido'
                ], 400);
            }
            
            // Guardar archivo temporal para procesamiento
            $tempFileName = 'temp_' . time() . '_' . $file->getClientOriginalName();
            $tempFilePath = $file->storeAs('temp', $tempFileName, 'public');
            $tempFullPath = storage_path('app/public/' . $tempFilePath);

            Log::info("Archivo temporal guardado en: $tempFullPath");

            if (!file_exists($tempFullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el archivo'
                ], 500);
            }

            // Procesar Excel
            $processedData = $this->processAndValidateExcel($tempFullPath, $documentType);

            Log::info("Resultado del procesamiento:");
            Log::info("- Válido: " . ($processedData['valid'] ? 'SÍ' : 'NO'));
            if (isset($processedData['data'])) {
                Log::info("- Filas de datos: " . count($processedData['data']));
            }

            if (!$processedData['valid']) {
                Storage::disk('public')->delete($tempFilePath);
                return response()->json([
                    'success' => false,
                    'message' => $processedData['error']
                ], 400);
            }

            // Guardar SOLO el archivo procesado
            $processedFilePath = $this->saveProcessedFile($processedData['data'], $file->getClientOriginalName(), $documentType);

            if (!$processedFilePath) {
                Storage::disk('public')->delete($tempFilePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el archivo procesado'
                ], 500);
            }

            // Crear registro del archivo (solo el procesado)
            $uploadedFile = UploadedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $processedFilePath,
                'document_type' => $documentType,
                'file_size' => Storage::disk('public')->size($processedFilePath),
                'total_institutions' => $processedData['summary']['institutions'] ?? 0,
                'total_students' => $processedData['summary']['students'] ?? 0,
                'processing_summary' => $processedData['summary'] ?? [],
                'uploaded_at' => now(),
                'uploaded_by' => auth()->id()
            ]);

            // Eliminar archivo temporal
            Storage::disk('public')->delete($tempFilePath);

            Log::info("Archivo registrado con ID: {$uploadedFile->id}");
            Log::info('=== UPLOAD COMPLETADO ===');

            return response()->json([
                'success' => true,
                'message' => 'Archivo procesado exitosamente',
                'summary' => $processedData['summary'] ?? []
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ERROR DE VALIDACIÓN: ' . json_encode($e->errors()));
            if (isset($tempFilePath) && Storage::disk('public')->exists($tempFilePath)) {
                Storage::disk('public')->delete($tempFilePath);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            Log::error('ERROR EN UPLOAD: ' . $e->getMessage());
            if (isset($tempFilePath) && Storage::disk('public')->exists($tempFilePath)) {
                Storage::disk('public')->delete($tempFilePath);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar archivo procesado en storage/app/public/processed
     */
    private function saveProcessedFile($processedData, $originalName, $documentType)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Agregar datos procesados al worksheet
            $rowNum = 1;
            foreach ($processedData as $row) {
                $colNum = 1;
                foreach ($row as $cell) {
                    $worksheet->setCellValueByColumnAndRow($colNum, $rowNum, $cell);
                    $colNum++;
                }
                $rowNum++;
            }
            
            // Estilo para headers (primera fila)
            if (!empty($processedData)) {
                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E3F2FD']
                    ]
                ];
                $worksheet->getStyle('A1:' . $worksheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
            }
            
            // Guardar en storage/app/public/processed
            $fileName = 'procesado_' . time() . '_' . $documentType . '_' . $originalName;
            $filePath = 'processed/' . $fileName;
            $fullPath = storage_path('app/public/' . $filePath);
            
            // Crear directorio si no existe
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fullPath);
            
            Log::info('Archivo procesado guardado en: ' . $fullPath);
            
            return $filePath; // Retornar ruta relativa para almacenar en BD
            
        } catch (\Exception $e) {
            Log::error('Error al guardar archivo procesado: ' . $e->getMessage());
            return false;
        }
    }

    public function show($id)
    {
        try {
            $file = UploadedFile::with('user')->findOrFail($id);
            return response()->json($file);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
    }

    public function download($id)
    {
        try {
            $file = UploadedFile::findOrFail($id);
            
            if (!Storage::disk('public')->exists($file->file_path)) {
                abort(404, 'Archivo no encontrado');
            }
            
            $downloadName = 'procesado_' . $file->document_type . '_' . $file->original_name;
            return Storage::disk('public')->download($file->file_path, $downloadName);
        } catch (\Exception $e) {
            abort(404, 'Archivo no encontrado');
        }
    }

    public function delete($id)
    {
        try {
            $file = UploadedFile::findOrFail($id);
            
            // Eliminar archivo físico procesado
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Eliminar registro de BD
            $file->delete();
            
            return response()->json(['success' => true, 'message' => 'Archivo eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar archivo'], 500);
        }
    }

    public function viewData($id)
    {
        try {
            $file = UploadedFile::findOrFail($id);
            
            if (!Storage::disk('public')->exists($file->file_path)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Archivo físico no encontrado'
                ], 404);
            }
            
            // Leer el archivo procesado directamente
            $fullPath = storage_path('app/public/' . $file->file_path);
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $allData = $worksheet->toArray();
            
            if (empty($allData)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'El archivo no contiene datos válidos'
                ], 400);
            }
            
            // Separar headers y datos
            $headers = array_shift($allData);
            $dataRows = $allData;
            
            if (!is_array($headers) || empty($headers)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No se pudieron extraer los encabezados del archivo'
                ], 400);
            }
            
            // Paginar los datos
            $page = max(1, intval(request()->get('page', 1)));
            $perPage = 50;
            $totalRecords = count($dataRows);
            $totalPages = max(1, ceil($totalRecords / $perPage));
            
            $page = min($page, $totalPages);
            
            $offset = ($page - 1) * $perPage;
            $paginatedData = array_slice($dataRows, $offset, $perPage);
            
            return response()->json([
                'success' => true,
                'headers' => $headers,
                'data' => $paginatedData,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_records' => $totalRecords,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ],
                'file_info' => [
                    'name' => $file->original_name,
                    'type' => $file->document_type,
                    'uploaded_at' => $file->uploaded_at->format('d/m/Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en viewData: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportProcessedData($id)
    {
        try {
            $file = UploadedFile::findOrFail($id);
            
            if (!Storage::disk('public')->exists($file->file_path)) {
                abort(404, 'Archivo no encontrado');
            }
            
            // El archivo ya está procesado, solo descargarlo con nombre personalizado
            $downloadName = 'exportado_' . $file->document_type . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Storage::disk('public')->download($file->file_path, $downloadName);
            
        } catch (\Exception $e) {
            Log::error('Error en exportProcessedData: ' . $e->getMessage());
            abort(500, 'Error al exportar datos: ' . $e->getMessage());
        }
    }

    // ==================== MÉTODOS DE PROCESAMIENTO DE EXCEL ====================

    private function processAndValidateExcel($filePath, $expectedType)
{
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $this->ungroupMergedCells($worksheet);
        $this->removeEmptyColumns($worksheet);
        
        $data = $worksheet->toArray();

        if (empty($data)) {
            return ['valid' => false, 'error' => 'El archivo está vacío'];
        }

        // Buscar fila con "DRE" en la primera columna
        $dreRowIndex = null;
        foreach ($data as $index => $row) {
            if (isset($row[0]) && stripos($row[0], 'DRE') !== false) {
                $dreRowIndex = $index;
                break;
            }
        }

        if ($dreRowIndex === null) {
            return ['valid' => false, 'error' => 'No se encontró la palabra "DRE" en la primera columna. Verifique que sea el archivo correcto.'];
        }

        // Validar nivel educativo
        $levelValidation = $this->validateEducationLevel($data, $expectedType, $dreRowIndex);
        if (!$levelValidation['valid']) {
            return ['valid' => false, 'error' => $levelValidation['error']];
        }

        // Limpiar datos
        $cleanedData = $this->cleanExcelData($data, $dreRowIndex, $expectedType);
        Log::info("Datos después de limpieza: " . count($cleanedData) . " filas");

        // === FILTRAR COLUMNAS VACÍAS ANTES DE AGREGAR HEADERS ===
        $filteredData = $this->filterEmptyColumns($cleanedData);

        // Agregar encabezados específicos
        $processedData = $this->addHeaders($filteredData, $expectedType);
        Log::info("Datos después de agregar headers: " . count($processedData) . " filas");

        // Extraer resumen
        $summary = $this->extractDataSummary($processedData, $expectedType);

        return [
            'valid' => true,
            'data' => $processedData,
            'summary' => $summary
        ];
        
    } catch (\Exception $e) {
        Log::error('Error en processAndValidateExcel: ' . $e->getMessage());
        return ['valid' => false, 'error' => 'Error al procesar el archivo Excel: ' . $e->getMessage()];
    }
}
    private function ungroupMergedCells($worksheet)
    {
        try {
            $mergedCells = $worksheet->getMergeCells();
            
            foreach ($mergedCells as $mergedRange) {
                $topLeftCell = explode(':', $mergedRange)[0];
                $cellValue = $worksheet->getCell($topLeftCell)->getValue();
                
                $worksheet->unmergeCells($mergedRange);
                $this->fillMergedRangeOnlyFirst($worksheet, $mergedRange, $cellValue);
            }
        } catch (\Exception $e) {
            Log::warning('Error al separar celdas agrupadas: ' . $e->getMessage());
        }
    }

    private function filterEmptyColumns($data)
{
    if (empty($data)) return $data;

    $numCols = 0;
    foreach ($data as $row) {
        $numCols = max($numCols, count($row));
    }

    $validColumns = [];
    for ($col = 0; $col < $numCols; $col++) {
        $hasRealData = false;
        foreach ($data as $row) {
            if (isset($row[$col])) {
                $cellValue = $this->cleanCellValue($row[$col]);
                // Considera vacío si es '' o null, pero NO si es '0'
                if ($cellValue !== '' && $cellValue !== null && $cellValue !== 'NULL' && $cellValue !== '#N/A') {
                    $hasRealData = true;
                    break;
                }
            }
        }
        if ($hasRealData) {
            $validColumns[] = $col;
        }
    }

    // Filtra solo las columnas válidas
    $filteredData = [];
    foreach ($data as $row) {
        $filteredRow = [];
        foreach ($validColumns as $col) {
            $filteredRow[] = isset($row[$col]) ? $row[$col] : '';
        }
        $filteredData[] = $filteredRow;
    }

    return $filteredData;
}

    private function fillMergedRangeOnlyFirst($worksheet, $range, $value)
    {
        try {
            $rangeParts = explode(':', $range);
            $startCell = $rangeParts[0];
            $endCell = $rangeParts[1];
            
            $startCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
            $endCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($endCell);
            
            $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startCoords[0]);
            $startRow = $startCoords[1];
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCoords[0]);
            $endRow = $endCoords[1];
            
            $worksheet->setCellValue($startCell, $value);
            
            for ($row = $startRow; $row <= $endRow; $row++) {
                for ($col = $startCol; $col <= $endCol; $col++) {
                    $cellRef = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                    
                    if ($cellRef !== $startCell) {
                        $worksheet->setCellValue($cellRef, '');
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Error al procesar rango de celdas: ' . $e->getMessage());
        }
    }

    private function removeEmptyColumns($worksheet)
    {
        try {
            $highestColumn = $worksheet->getHighestColumn();
            $highestRow = $worksheet->getHighestRow();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            $columnsToRemove = [];
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $isEmpty = true;
                
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    
                    if ($cellValue !== null && 
                        $cellValue !== '' && 
                        trim(strval($cellValue)) !== '') {
                        $isEmpty = false;
                        break;
                    }
                    
                    if ($cellValue === 0 || $cellValue === '0') {
                        $isEmpty = false;
                        break;
                    }
                }
                
                if ($isEmpty) {
                    $columnsToRemove[] = $col;
                }
            }
            
            $columnsToRemove = array_reverse($columnsToRemove);
            foreach ($columnsToRemove as $col) {
                $worksheet->removeColumn($col);
                Log::info("Columna $col eliminada por estar vacía");
            }
            
        } catch (\Exception $e) {
            Log::warning('Error al eliminar columnas vacías: ' . $e->getMessage());
        }
    }

    private function cleanExcelData($data, $startRow, $expectedType)
    {
        try {
            Log::info("=== INICIANDO LIMPIEZA DE DATOS ===");
            
            $relevantData = array_slice($data, $startRow);
            
            // Encontrar la primera fila con datos reales (código modular)
            $dataStartIndex = 0;
            for ($i = 0; $i < count($relevantData); $i++) {
                $row = $relevantData[$i];
                if (isset($row[6]) && !empty(trim($row[6]))) {
                    $codeValue = str_replace([' ', '-'], '', trim($row[6]));
                    if (is_numeric($codeValue)) {
                        $dataStartIndex = $i;
                        break;
                    }
                }
            }

            $dataRows = array_slice($relevantData, $dataStartIndex);
            $dataRows = $this->removeEmptyRows($dataRows);
            $dataRows = $this->removeEmptyColumnsFromData($dataRows);
            
            $maxUsefulColumns = $this->findMaxUsefulColumns($dataRows, $expectedType);
            
            $cleanedData = [];
            foreach ($dataRows as $rowIndex => $row) {
                $cleanedRow = [];
                $hasRelevantData = false;
                
                $hasCodeModular = isset($row[6]) && !empty(trim($row[6])) && 
                                is_numeric(str_replace([' ', '-'], '', trim($row[6])));
                $hasInstitutionName = isset($row[8]) && !empty(trim($row[8]));
                
                if ($hasCodeModular || $hasInstitutionName) {
                    for ($i = 0; $i < min($maxUsefulColumns, count($row)); $i++) {
                        $cellValue = isset($row[$i]) ? $row[$i] : '';
                        $cellValue = $this->cleanCellValue($cellValue);
                        $cleanedRow[] = $cellValue;
                        
                        if (!empty($cellValue) && ($i == 6 || $i == 8)) {
                            $hasRelevantData = true;
                        }
                    }
                    
                    if ($hasRelevantData) {
                        $cleanedData[] = $cleanedRow;
                    }
                }
            }

            return $cleanedData;
        } catch (\Exception $e) {
            Log::error('Error en cleanExcelData: ' . $e->getMessage());
            return [];
        }
    }

    private function cleanCellValue($cellValue)
    {
        $cellValue = strval($cellValue);
        $cellValue = trim($cellValue);
        
        if ($cellValue === 'NULL' || $cellValue === '#N/A' || $cellValue === 'null') {
            return '';
        }
        
        if ($cellValue === '0' || $cellValue === 0) {
            return '0';
        }
        
        return $cellValue;
    }

    private function removeEmptyRows($dataRows)
    {
        $filteredRows = array_filter($dataRows, function($row) {
            $hasData = false;
            foreach ($row as $cell) {
                $cleanValue = trim(strval($cell));
                if ($cleanValue !== '' && $cleanValue !== 'NULL' && $cleanValue !== '#N/A') {
                    $hasData = true;
                    break;
                }
                if ($cleanValue === '0') {
                    $hasData = true;
                    break;
                }
            }
            return $hasData;
        });
        
        return array_values($filteredRows);
    }

    private function removeEmptyColumnsFromData($dataRows)
    {
        if (empty($dataRows)) {
            return $dataRows;
        }
        
        try {
            $maxCols = 0;
            foreach ($dataRows as $row) {
                $maxCols = max($maxCols, count($row));
            }
            
            if ($maxCols === 0) {
                return $dataRows;
            }
            
            $emptyColumns = [];
            for ($col = 0; $col < $maxCols; $col++) {
                $isEmpty = true;
                
                foreach ($dataRows as $row) {
                    if (isset($row[$col])) {
                        $cellValue = $this->cleanCellValue($row[$col]);
                        
                        if ($cellValue !== '') {
                            $isEmpty = false;
                            break;
                        }
                    }
                }
                
                if ($isEmpty) {
                    $emptyColumns[] = $col;
                }
            }
            
            $emptyColumns = array_reverse($emptyColumns);
            foreach ($emptyColumns as $colIndex) {
                foreach ($dataRows as $rowIndex => &$row) {
                    if (isset($row[$colIndex])) {
                        array_splice($row, $colIndex, 1);
                    }
                }
                unset($row);
            }
            
            return $dataRows;
            
        } catch (\Exception $e) {
            Log::warning('Error al eliminar columnas vacías de datos: ' . $e->getMessage());
            return $dataRows;
        }
    }

    private function findMaxUsefulColumns($dataRows, $documentType = 'inicial')
    {
        $maxColumns = 0;
        
        foreach ($dataRows as $row) {
            for ($i = count($row) - 1; $i >= 0; $i--) {
                $cellValue = isset($row[$i]) ? trim($row[$i]) : '';
                
                if (($cellValue !== null && 
                     $cellValue !== '' && 
                     $cellValue !== 'NULL' && 
                     $cellValue !== '#N/A') ||
                    $cellValue === '0' || 
                    $cellValue === 0) {
                    $maxColumns = max($maxColumns, $i + 1);
                    break;
                }
            }
        }
        
        $minColumnsByType = [
            'inicial' => 37,
            'primaria' => 35,
            'secundaria' => 33
        ];
        
        $minRequiredColumns = $minColumnsByType[$documentType] ?? 37;
        return max($maxColumns, $minRequiredColumns);
    }

    private function addHeaders($data, $documentType)
    {
        try {
            $headers = self::HEADERS[$documentType] ?? self::HEADERS['inicial'];

            if (empty($data)) {
                return [$headers];
            }

            // Determinar cuántas columnas tiene realmente la primera fila de datos
            $numCols = 0;
            foreach ($data as $row) {
                $numCols = max($numCols, count($row));
            }

            // IMPORTANTE: Solo usar encabezados para columnas que realmente existen
            $adjustedHeaders = [];
            
            // Verificar qué columnas tienen datos reales
            for ($colIndex = 0; $colIndex < $numCols; $colIndex++) {
                $hasData = false;
                
                // Verificar si esta columna tiene datos en alguna fila
                foreach ($data as $row) {
                    if (isset($row[$colIndex])) {
                        $cellValue = $this->cleanCellValue($row[$colIndex]);
                        if ($cellValue !== '') {
                            $hasData = true;
                            break;
                        }
                    }
                }
                
                // Solo agregar header si la columna tiene datos
                if ($hasData && isset($headers[$colIndex])) {
                    $adjustedHeaders[$colIndex] = $headers[$colIndex];
                } elseif ($hasData) {
                    // Si no hay header predefinido, usar un genérico
                    $adjustedHeaders[$colIndex] = 'columna_' . ($colIndex + 1);
                }
            }

            // Reorganizar datos para que coincidan con los headers válidos
            $validColumnIndexes = array_keys($adjustedHeaders);
            $finalHeaders = array_values($adjustedHeaders);
            
            $adjustedData = [];
            foreach ($data as $row) {
                $adjustedRow = [];
                
                foreach ($validColumnIndexes as $colIndex) {
                    $value = isset($row[$colIndex]) ? $row[$colIndex] : '';
                    if ($value === 'NULL' || $value === '#N/A' || $value === null) {
                        $value = '';
                    } elseif ($value === 0 || $value === '0') {
                        $value = '0';
                    } else {
                        $value = trim(strval($value));
                    }
                    $adjustedRow[] = $value;
                }
                
                $adjustedData[] = $adjustedRow;
            }

            // Poner los headers al inicio
            array_unshift($adjustedData, $finalHeaders);

            Log::info("Headers ajustados:", [
                'total_columns' => count($finalHeaders),
                'headers' => $finalHeaders,
                'valid_column_indexes' => $validColumnIndexes
            ]);

            return $adjustedData;
            
        } catch (\Exception $e) {
            Log::error('Error en addHeaders: ' . $e->getMessage());
            return [$headers ?? []];
        }
    }
    private function extractDataSummary($data, $documentType)
    {
        try {
            if (empty($data) || count($data) <= 1) {
                return [
                    'institutions' => 0, 
                    'students' => 0, 
                    'total_records' => 0,
                    'by_department' => [],
                    'by_ugel' => [],
                    'total_matriculados' => 0
                ];
            }

            $headers = $data[0];
            $dataRows = array_slice($data, 1);
            
            $institutions = [];
            $totalStudents = 0;
            $totalMatriculados = 0;
            $byDepartment = [];
            $byUgel = [];

            $institutionIndex = array_search('nombre_ie', $headers);
            $matriculadosIndex = array_search('total_matriculados', $headers);
            $departmentIndex = array_search('departamento', $headers);
            $ugelIndex = array_search('ugel', $headers);

            foreach ($dataRows as $row) {
                if ($institutionIndex !== false && !empty($row[$institutionIndex])) {
                    $institutions[$row[$institutionIndex]] = true;
                }

                if ($matriculadosIndex !== false && is_numeric($row[$matriculadosIndex])) {
                    $matriculados = intval($row[$matriculadosIndex]);
                    $totalMatriculados += $matriculados;
                    $totalStudents += $matriculados;
                }

                if ($departmentIndex !== false && !empty($row[$departmentIndex])) {
                    $dept = $row[$departmentIndex];
                    $byDepartment[$dept] = ($byDepartment[$dept] ?? 0) + 1;
                }

                if ($ugelIndex !== false && !empty($row[$ugelIndex])) {
                    $ugel = $row[$ugelIndex];
                    $byUgel[$ugel] = ($byUgel[$ugel] ?? 0) + 1;
                }
            }

            return [
                'institutions' => count($institutions),
                'students' => $totalStudents,
                'total_matriculados' => $totalMatriculados,
                'total_records' => count($dataRows),
                'by_department' => $byDepartment,
                'by_ugel' => $byUgel,
                'document_type' => $documentType,
                'columns_processed' => count($headers)
            ];
        } catch (\Exception $e) {
            Log::error('Error en extractDataSummary: ' . $e->getMessage());
            return [
                'institutions' => 0,
                'students' => 0,
                'total_records' => 0,
                'by_department' => [],
                'by_ugel' => [],
                'total_matriculados' => 0
            ];
        }
    }

    private function validateEducationLevel($data, $expectedType, $startRow)
    {
        $levelMappings = [
            'inicial' => ['inicial', 'educación inicial', 'educacion inicial', 'jardín', 'jardin', 'cuna', 'cunas'],
            'primaria' => ['primaria', 'educación primaria', 'educacion primaria', 'primary', 'elem'],
            'secundaria' => ['secundaria', 'educación secundaria', 'educacion secundaria', 'secondary', 'sec']
        ];

        $foundLevels = [];
        
        for ($i = max(0, $startRow - 5); $i < min(count($data), $startRow + 15); $i++) {
            for ($col = 9; $col <= 12; $col++) {
                if (isset($data[$i][$col])) {
                    $cellValue = strtolower(trim($data[$i][$col]));
                    
                    if (!empty($cellValue)) {
                        foreach ($levelMappings as $levelType => $keywords) {
                            foreach ($keywords as $keyword) {
                                if (stripos($cellValue, $keyword) !== false) {
                                    $foundLevels[$levelType] = $cellValue;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($foundLevels[$expectedType])) {
            return ['valid' => true];
        }

        if (!empty($foundLevels)) {
            $foundTypes = array_keys($foundLevels);
            $foundTypesList = implode(', ', $foundTypes);
            
            return [
                'valid' => false,
                'error' => "Error de validación: El archivo contiene datos de nivel '$foundTypesList' pero seleccionaste '$expectedType'. Por favor verifica que el archivo y la selección coincidan."
            ];
        }

        $manualValidation = $this->manualLevelValidation($data, $expectedType);
        
        if (!$manualValidation['valid']) {
            return [
                'valid' => false,
                'error' => "No se pudo confirmar que el archivo corresponda al nivel '$expectedType'. " . $manualValidation['error']
            ];
        }

        return ['valid' => true];
    }

    private function manualLevelValidation($data, $expectedType)
    {
        $patterns = [
            'inicial' => [
                'headers' => ['cero_hombres', 'cero_mujeres', '0 años', '3 años', '4 años', '5 años'],
                'keywords' => ['inicial', 'jardín', 'cuna']
            ],
            'primaria' => [
                'headers' => ['sexto_hombres', 'sexto_mujeres', '1°', '2°', '3°', '4°', '5°', '6°'],
                'keywords' => ['primaria', 'elementary']
            ],
            'secundaria' => [
                'headers' => ['1°', '2°', '3°', '4°', '5°', 'secundaria'],
                'keywords' => ['secundaria', 'secondary']
            ]
        ];

        $currentPatterns = $patterns[$expectedType];
        $foundPatterns = 0;

        for ($i = 0; $i < min(20, count($data)); $i++) {
            $row = $data[$i];
            
            foreach ($row as $cell) {
                $cellValue = strtolower(trim($cell));
                
                foreach ($currentPatterns['keywords'] as $keyword) {
                    if (stripos($cellValue, $keyword) !== false) {
                        $foundPatterns++;
                    }
                }
                
                foreach ($currentPatterns['headers'] as $header) {
                    if (stripos($cellValue, $header) !== false) {
                        $foundPatterns++;
                    }
                }
            }
        }

        if ($foundPatterns > 0) {
            return ['valid' => true];
        }

        return [
            'valid' => false,
            'error' => "El archivo no parece contener datos del nivel '$expectedType'. Verifica que sea el archivo correcto."
        ];
    }
}