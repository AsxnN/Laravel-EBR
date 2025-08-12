<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        // Obtener archivos agrupados por mes
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
            
            // Forzar respuesta JSON
            $request->headers->set('Accept', 'application/json');
            
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'document_type' => 'required|in:inicial,primaria,secundaria'
            ]);

            $file = $request->file('excel_file');
            $documentType = $request->document_type;
            
            Log::info("Archivo: {$file->getClientOriginalName()}, Tipo: $documentType");
            
            // Validar que el archivo sea válido
            if (!$file->isValid()) {
                Log::error('Archivo no válido');
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo subido no es válido'
                ], 400);
            }
            
            // Guardar archivo
            $fileName = time() . '_' . $documentType . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/excel', $fileName, 'public');
            $fullPath = storage_path('app/public/' . $filePath);

            Log::info("Archivo guardado en: $fullPath");

            // Verificar que el archivo se guardó correctamente
            if (!file_exists($fullPath)) {
                Log::error("No se pudo guardar el archivo en: $fullPath");
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el archivo'
                ], 500);
            }

            // Procesar Excel
            $processedData = $this->processAndValidateExcel($fullPath, $documentType);

            Log::info("Resultado del procesamiento:");
            Log::info("- Válido: " . ($processedData['valid'] ? 'SÍ' : 'NO'));
            if (isset($processedData['data'])) {
                Log::info("- Filas de datos: " . count($processedData['data']));
            }
            if (isset($processedData['summary'])) {
                Log::info("- Resumen: " . json_encode($processedData['summary']));
            }

            if (!$processedData['valid']) {
                Storage::disk('public')->delete($filePath);
                Log::error("Error procesando: " . $processedData['error']);
                return response()->json([
                    'success' => false,
                    'message' => $processedData['error']
                ], 400);
            }

            Log::info('Procesamiento exitoso, creando registro...');

            // Crear registro del archivo
            $uploadedFile = UploadedFile::create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'document_type' => $documentType,
                'file_size' => $file->getSize(),
                'total_institutions' => $processedData['summary']['institutions'] ?? 0,
                'total_students' => $processedData['summary']['students'] ?? 0,
                'processing_summary' => $processedData['summary'] ?? [],
                'uploaded_at' => now(),
                'uploaded_by' => auth()->id()
            ]);

            Log::info("Archivo registrado con ID: {$uploadedFile->id}");
            Log::info('=== UPLOAD COMPLETADO ===');

            return response()->json([
                'success' => true,
                'message' => 'Archivo procesado exitosamente',
                'summary' => $processedData['summary'] ?? []
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ERROR DE VALIDACIÓN: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            Log::error('ERROR EN UPLOAD: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Limpiar archivo si existe
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtro final: Eliminar columnas completamente vacías antes de aplicar headers
     * Este es el último filtro antes de estructurar los datos finales
     */
    private function removeEmptyColumnsBeforeHeaders($data)
    {
        if (empty($data)) {
            return $data;
        }
        
        try {
            Log::info("Aplicando filtro final de columnas vacías...");
            
            // Determinar el número máximo de columnas
            $maxCols = 0;
            foreach ($data as $row) {
                $maxCols = max($maxCols, count($row));
            }
            
            if ($maxCols === 0) {
                return $data;
            }
            
            Log::info("Analizando $maxCols columnas para detectar vacías...");
            
            // Identificar columnas que están completamente vacías
            $emptyColumns = [];
            $columnStats = []; // Para debug
            
            for ($col = 0; $col < $maxCols; $col++) {
                $isEmpty = true;
                $nonEmptyCount = 0;
                $sampleValues = [];
                
                foreach ($data as $rowIndex => $row) {
                    if (isset($row[$col])) {
                        $cellValue = $this->cleanCellValue($row[$col]);
                        
                        // Mantener muestra de valores para debug
                        if ($rowIndex < 3 && $cellValue !== '') {
                            $sampleValues[] = $cellValue;
                        }
                        
                        // La columna NO está vacía si tiene contenido real
                        if ($cellValue !== '') {
                            $isEmpty = false;
                            $nonEmptyCount++;
                        }
                    }
                }
                
                // Estadísticas para debug
                $columnStats[$col] = [
                    'is_empty' => $isEmpty,
                    'non_empty_count' => $nonEmptyCount,
                    'sample_values' => $sampleValues
                ];
                
                if ($isEmpty) {
                    $emptyColumns[] = $col;
                    Log::info("Columna $col marcada para eliminar: completamente vacía");
                } else {
                    Log::info("Columna $col mantener: $nonEmptyCount valores no vacíos. Muestras: " . implode(', ', array_slice($sampleValues, 0, 2)));
                }
            }
            
            Log::info("Columnas vacías detectadas: " . count($emptyColumns) . " de $maxCols");
            
            // Si no hay columnas vacías, retornar datos originales
            if (empty($emptyColumns)) {
                Log::info("No hay columnas vacías que eliminar");
                return $data;
            }
            
            // Eliminar columnas vacías (de derecha a izquierda para evitar problemas de índices)
            $emptyColumns = array_reverse($emptyColumns);
            $filteredData = [];
            
            foreach ($data as $rowIndex => $row) {
                $filteredRow = $row; // Copia de la fila
                
                // Eliminar columnas vacías
                foreach ($emptyColumns as $colIndex) {
                    if (isset($filteredRow[$colIndex])) {
                        array_splice($filteredRow, $colIndex, 1);
                    }
                }
                
                $filteredData[] = $filteredRow;
            }
            
            // Log resultado
            if (!empty($filteredData)) {
                $originalCols = count($data[0]);
                $finalCols = count($filteredData[0]);
                Log::info("Columnas reducidas de $originalCols a $finalCols (eliminadas: " . ($originalCols - $finalCols) . ")");
                
                // Debug de las primeras columnas después del filtro
                if (count($filteredData) > 0) {
                    Log::info("Primeras 10 columnas después del filtro:");
                    for ($i = 0; $i < min(10, count($filteredData[0])); $i++) {
                        $sampleValue = isset($filteredData[0][$i]) ? $filteredData[0][$i] : 'N/A';
                        Log::info("  Col $i: '$sampleValue'");
                    }
                }
            }
            
            return $filteredData;
            
        } catch (\Exception $e) {
            Log::error('Error en removeEmptyColumnsBeforeHeaders: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $data; // Retornar datos originales en caso de error
        }
    }

    private function processAndValidateExcel($filePath, $expectedType)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Separar celdas agrupadas ANTES de convertir a array
            $this->ungroupMergedCells($worksheet);
            
            // Eliminar columnas completamente vacías DESPUÉS de separar celdas merged
            $this->removeEmptyColumns($worksheet);
            
            $data = $worksheet->toArray();

            // Paso 1: Validar que el archivo tenga contenido
            if (empty($data)) {
                return ['valid' => false, 'error' => 'El archivo está vacío'];
            }

            // Paso 2: Buscar fila con "DRE" en la primera columna
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

            // Paso 3: Validar nivel educativo
            $levelValidation = $this->validateEducationLevel($data, $expectedType, $dreRowIndex);
            if (!$levelValidation['valid']) {
                return ['valid' => false, 'error' => $levelValidation['error']];
            }

            // Paso 4: Limpiar datos
            $cleanedData = $this->cleanExcelData($data, $dreRowIndex, $expectedType);
            Log::info("Datos después de limpieza: " . count($cleanedData) . " filas");

            // Paso 5: Agregar encabezados específicos (incluye filtro final de columnas)
            $processedData = $this->addHeaders($cleanedData, $expectedType);
            Log::info("Datos después de agregar headers: " . count($processedData) . " filas");

            // Paso 6: Extraer y procesar datos
            $summary = $this->extractDataSummary($processedData, $expectedType);

            return [
                'valid' => true,
                'data' => $processedData,
                'summary' => $summary
            ];
            
        } catch (\Exception $e) {
            Log::error('Error en processAndValidateExcel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ['valid' => false, 'error' => 'Error al procesar el archivo Excel: ' . $e->getMessage()];
        }
    }

    /**
     * Separar todas las celdas agrupadas/merged del worksheet
     */
    private function ungroupMergedCells($worksheet)
    {
        try {
            // Obtener todas las celdas agrupadas
            $mergedCells = $worksheet->getMergeCells();
            
            foreach ($mergedCells as $mergedRange) {
                // Obtener el valor de la celda superior-izquierda del rango agrupado
                $topLeftCell = explode(':', $mergedRange)[0];
                $cellValue = $worksheet->getCell($topLeftCell)->getValue();
                
                // Desagrupar las celdas
                $worksheet->unmergeCells($mergedRange);
                
                // SOLO mantener el valor en la primera celda, dejar las demás vacías
                $this->fillMergedRangeOnlyFirst($worksheet, $mergedRange, $cellValue);
            }
        } catch (\Exception $e) {
            \Log::warning('Error al separar celdas agrupadas: ' . $e->getMessage());
            // Continuar con el procesamiento aunque haya error en merged cells
        }
    }

    /**
     * Llenar solo la primera celda del rango, dejar las demás vacías
     */
    private function fillMergedRangeOnlyFirst($worksheet, $range, $value)
    {
        try {
            $rangeParts = explode(':', $range);
            $startCell = $rangeParts[0];
            $endCell = $rangeParts[1];
            
            // Convertir referencias de celda a coordenadas
            $startCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
            $endCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($endCell);
            
            $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startCoords[0]);
            $startRow = $startCoords[1];
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCoords[0]);
            $endRow = $endCoords[1];
            
            // Solo establecer el valor en la primera celda (top-left)
            $worksheet->setCellValue($startCell, $value);
            
            // Limpiar todas las demás celdas del rango
            for ($row = $startRow; $row <= $endRow; $row++) {
                for ($col = $startCol; $col <= $endCol; $col++) {
                    $cellRef = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                    
                    // Solo limpiar si NO es la primera celda
                    if ($cellRef !== $startCell) {
                        $worksheet->setCellValue($cellRef, '');
                    }
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('Error al procesar rango de celdas: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar columnas completamente vacías del worksheet
     */
    private function removeEmptyColumns($worksheet)
    {
        try {
            $highestColumn = $worksheet->getHighestColumn();
            $highestRow = $worksheet->getHighestRow();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            $columnsToRemove = [];
            
            // Revisar cada columna para ver si está vacía
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $isEmpty = true;
                
                // Revisar todas las filas de esta columna
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    
                    // La columna NO está vacía si:
                    if ($cellValue !== null && 
                        $cellValue !== '' && 
                        trim(strval($cellValue)) !== '') {
                        $isEmpty = false;
                        break;
                    }
                    
                    // Verificar específicamente para valores 0
                    if ($cellValue === 0 || $cellValue === '0') {
                        $isEmpty = false;
                        break;
                    }
                }
                
                // Marcar columna para eliminar si está vacía
                if ($isEmpty) {
                    $columnsToRemove[] = $col;
                }
            }
            
            // Eliminar columnas de derecha a izquierda para evitar problemas con índices
            $columnsToRemove = array_reverse($columnsToRemove);
            foreach ($columnsToRemove as $col) {
                $worksheet->removeColumn($col);
                \Log::info("Columna $col eliminada por estar vacía");
            }
            
        } catch (\Exception $e) {
            \Log::warning('Error al eliminar columnas vacías: ' . $e->getMessage());
            // Continuar con el procesamiento aunque haya error
        }
    }

    private function cleanExcelData($data, $startRow, $expectedType)
    {
        try {
            Log::info("=== INICIANDO LIMPIEZA DE DATOS ===");
            Log::info("Tipo esperado: $expectedType");
            Log::info("Fila de inicio: $startRow");
            Log::info("Total de filas en datos: " . count($data));
            
            // Obtener datos desde la fila donde encontramos DRE
            $relevantData = array_slice($data, $startRow);
            Log::info("Datos relevantes desde DRE: " . count($relevantData) . " filas");
            
            // Encontrar la primera fila con datos reales (código modular)
            $dataStartIndex = 0;
            for ($i = 0; $i < count($relevantData); $i++) {
                $row = $relevantData[$i];
                // Buscar fila que tenga código modular en columna 7 (índice 6)
                if (isset($row[6]) && !empty(trim($row[6]))) {
                    $codeValue = str_replace([' ', '-'], '', trim($row[6]));
                    if (is_numeric($codeValue)) {
                        $dataStartIndex = $i;
                        Log::info("Primera fila con código modular encontrada en índice: $dataStartIndex");
                        break;
                    }
                }
            }

            // Tomar solo los datos relevantes
            $dataRows = array_slice($relevantData, $dataStartIndex);
            Log::info("Filas de datos después de encontrar código modular: " . count($dataRows));
            
            // Eliminar filas completamente vacías primero
            $dataRows = $this->removeEmptyRows($dataRows);
            Log::info("Filas después de eliminar filas vacías: " . count($dataRows));
            
            // Eliminar columnas completamente vacías de los datos
            $dataRows = $this->removeEmptyColumnsFromData($dataRows);
            Log::info("Datos después de eliminar columnas vacías");
            
            // Determinar el número máximo de columnas útiles
            $maxUsefulColumns = $this->findMaxUsefulColumns($dataRows, $expectedType);
            Log::info("Máximo de columnas útiles determinadas: $maxUsefulColumns");
            
            // Limpiar y estructurar datos
            $cleanedData = [];
            foreach ($dataRows as $rowIndex => $row) {
                $cleanedRow = [];
                $hasRelevantData = false;
                
                // Verificar si la fila tiene datos útiles
                $hasCodeModular = isset($row[6]) && !empty(trim($row[6])) && 
                                is_numeric(str_replace([' ', '-'], '', trim($row[6])));
                $hasInstitutionName = isset($row[8]) && !empty(trim($row[8]));
                
                if ($hasCodeModular || $hasInstitutionName) {
                    // Procesar solo las columnas útiles
                    for ($i = 0; $i < min($maxUsefulColumns, count($row)); $i++) {
                        $cellValue = isset($row[$i]) ? $row[$i] : '';
                        
                        // Limpiar el valor
                        $cellValue = $this->cleanCellValue($cellValue);
                        
                        $cleanedRow[] = $cellValue;
                        
                        // Marcar que hay datos relevantes si es código modular o nombre IE
                        if (!empty($cellValue) && ($i == 6 || $i == 8)) {
                            $hasRelevantData = true;
                        }
                    }
                    
                    // Solo agregar filas que tienen datos relevantes
                    if ($hasRelevantData) {
                        $cleanedData[] = $cleanedRow;
                        
                        // Debug para las primeras filas
                        if (count($cleanedData) <= 3) {
                            Log::info("Fila procesada " . count($cleanedData) . ": " . count($cleanedRow) . " columnas");
                            Log::info("Código modular: " . ($cleanedRow[6] ?? 'N/A'));
                            Log::info("Nombre IE: " . ($cleanedRow[8] ?? 'N/A'));
                        }
                    }
                }
            }

            Log::info("=== RESULTADO LIMPIEZA ===");
            Log::info("Filas de datos limpias: " . count($cleanedData));
            
            if (!empty($cleanedData)) {
                Log::info("Columnas por fila: " . count($cleanedData[0]));
            } else {
                Log::warning("⚠️ NO SE ENCONTRARON DATOS VÁLIDOS");
            }

            return $cleanedData;
        } catch (\Exception $e) {
            Log::error('Error en cleanExcelData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Limpiar valor de celda individual
     */
    private function cleanCellValue($cellValue)
    {
        // Convertir a string si no lo es
        $cellValue = strval($cellValue);
        
        // Limpiar espacios
        $cellValue = trim($cellValue);
        
        // Limpiar valores especiales PERO mantener el 0
        if ($cellValue === 'NULL' || $cellValue === '#N/A' || $cellValue === 'null') {
            return '';
        }
        
        // Mantener valores 0
        if ($cellValue === '0' || $cellValue === 0) {
            return '0';
        }
        
        // Devolver valor limpio
        return $cellValue;
    }

    /**
     * Eliminar filas completamente vacías
     */
    private function removeEmptyRows($dataRows)
    {
        Log::info("Eliminando filas vacías de " . count($dataRows) . " filas");
        
        $filteredRows = array_filter($dataRows, function($row, $index) {
            // Una fila NO está vacía si tiene al menos un valor que no sea vacío
            $hasData = false;
            foreach ($row as $cellIndex => $cell) {
                $cleanValue = trim(strval($cell));
                if ($cleanValue !== '' && $cleanValue !== 'NULL' && $cleanValue !== '#N/A') {
                    $hasData = true;
                    break;
                }
                // Mantener filas que tienen 0
                if ($cleanValue === '0') {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData && $index < 5) { // Log para las primeras filas si están vacías
                Log::info("Fila $index eliminada por estar vacía");
            }
            
            return $hasData;
        }, ARRAY_FILTER_USE_BOTH);
        
        // Reindexar array
        $result = array_values($filteredRows);
        Log::info("Filas después de filtrar: " . count($result));
        
        return $result;
    }

    /**
     * Eliminar columnas completamente vacías de un array de datos
     */
    private function removeEmptyColumnsFromData($dataRows)
    {
        if (empty($dataRows)) {
            return $dataRows;
        }
        
        try {
            // Determinar el número máximo de columnas
            $maxCols = 0;
            foreach ($dataRows as $row) {
                $maxCols = max($maxCols, count($row));
            }
            
            if ($maxCols === 0) {
                return $dataRows;
            }
            
            // Identificar columnas vacías
            $emptyColumns = [];
            for ($col = 0; $col < $maxCols; $col++) {
                $isEmpty = true;
                
                foreach ($dataRows as $row) {
                    if (isset($row[$col])) {
                        $cellValue = $this->cleanCellValue($row[$col]);
                        
                        // La columna NO está vacía si tiene contenido
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
            
            // Eliminar columnas vacías (de derecha a izquierda para evitar problemas de índices)
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
            \Log::warning('Error al eliminar columnas vacías de datos: ' . $e->getMessage());
            return $dataRows;
        }
    }

    private function findMaxUsefulColumns($dataRows, $documentType = 'inicial')
    {
        $maxColumns = 0;
        
        foreach ($dataRows as $row) {
            // Buscar la última columna con datos significativos
            for ($i = count($row) - 1; $i >= 0; $i--) {
                $cellValue = isset($row[$i]) ? trim($row[$i]) : '';
                
                // Considerar como datos útiles: cualquier valor que no sea NULL o #N/A
                // Incluir específicamente el valor 0
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
        
        // Determinar columnas mínimas según el tipo de documento
        $minColumnsByType = [
            'inicial' => 37,    // Hasta mas_quinto_mujeres
            'primaria' => 35,   // Hasta sexto_mujeres  
            'secundaria' => 33  // Hasta quinto_mujeres
        ];
        
        $minRequiredColumns = $minColumnsByType[$documentType] ?? 37;
        $finalColumns = max($maxColumns, $minRequiredColumns);
        
        Log::info("Tipo: $documentType, Columnas detectadas: $maxColumns, Mínimas requeridas: $minRequiredColumns, Final: $finalColumns");
        
        return $finalColumns;
    }

    private function addHeaders($data, $documentType)
    {
        try {
            // Obtener encabezados específicos
            $headers = self::HEADERS[$documentType] ?? self::HEADERS['inicial'];
            
            Log::info("=== AGREGANDO HEADERS ===");
            Log::info("Tipo: $documentType");
            Log::info("Headers disponibles: " . count($headers));
            Log::info("Datos recibidos: " . count($data) . " filas");
            
            // Verificar si hay datos para procesar
            if (empty($data)) {
                Log::warning("⚠️ No hay datos para procesar. Retornando solo headers.");
                return [$headers]; // Solo devolver los headers
            }
            
            if (!empty($data)) {
                Log::info("Columnas en primera fila de datos: " . count($data[0]));
            }
            
            // ========== ÚLTIMO FILTRO: ELIMINAR COLUMNAS VACÍAS ==========
            Log::info("=== APLICANDO FILTRO FINAL DE COLUMNAS VACÍAS ===");
            $data = $this->removeEmptyColumnsBeforeHeaders($data);
            
            if (!empty($data)) {
                Log::info("Columnas después del filtro final: " . count($data[0]));
            }
            // ============================================================
            
            // Ajustar datos según el número de headers
            $adjustedData = [];
            foreach ($data as $rowIndex => $row) {
                $adjustedRow = [];
                
                // Procesar cada columna según los headers definidos
                for ($i = 0; $i < count($headers); $i++) {
                    if (isset($row[$i])) {
                        $value = $row[$i];
                        
                        // Limpiar valores especiales pero mantener 0
                        if ($value === 'NULL' || $value === '#N/A' || $value === null) {
                            $value = '';
                        } elseif ($value === 0 || $value === '0') {
                            $value = '0';
                        } else {
                            $value = trim(strval($value));
                        }
                    } else {
                        // Si no hay datos para esta columna, usar valor vacío
                        $value = '';
                    }
                    
                    $adjustedRow[] = $value;
                }
                
                // Log para debug específico de columnas importantes en las primeras filas
                if ($rowIndex < 2) { 
                    Log::info("Fila $rowIndex debugging:");
                    Log::info("  - Total columnas ajustadas: " . count($adjustedRow));
                    Log::info("  - Codigo modular (col 6): '" . ($adjustedRow[6] ?? 'NO_EXISTE') . "'");
                    Log::info("  - Nombre IE (col 8): '" . ($adjustedRow[8] ?? 'NO_EXISTE') . "'");
                    Log::info("  - Columna 15 (registro_sin_dni): '" . ($adjustedRow[15] ?? 'NO_EXISTE') . "'");
                }
                
                $adjustedData[] = $adjustedRow;
            }

            // Insertar encabezados al inicio
            array_unshift($adjustedData, $headers);
            
            Log::info("=== RESULTADO HEADERS ===");
            Log::info("Datos finales: " . count($adjustedData) . " filas con " . count($headers) . " columnas cada una");
            
            return $adjustedData;
        } catch (\Exception $e) {
            Log::error('Error en addHeaders: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [$headers ?? []]; // Devolver al menos los headers en caso de error
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

            // Encontrar índices de columnas importantes
            $institutionIndex = array_search('nombre_ie', $headers);
            $matriculadosIndex = array_search('total_matriculados', $headers);
            $departmentIndex = array_search('departamento', $headers);
            $ugelIndex = array_search('ugel', $headers);

            foreach ($dataRows as $row) {
                // Contar instituciones únicas
                if ($institutionIndex !== false && !empty($row[$institutionIndex])) {
                    $institutions[$row[$institutionIndex]] = true;
                }

                // Sumar matriculados
                if ($matriculadosIndex !== false && is_numeric($row[$matriculadosIndex])) {
                    $matriculados = intval($row[$matriculadosIndex]);
                    $totalMatriculados += $matriculados;
                    $totalStudents += $matriculados;
                }

                // Agrupar por departamento
                if ($departmentIndex !== false && !empty($row[$departmentIndex])) {
                    $dept = $row[$departmentIndex];
                    $byDepartment[$dept] = ($byDepartment[$dept] ?? 0) + 1;
                }

                // Agrupar por UGEL
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
            \Log::error('Error en extractDataSummary: ' . $e->getMessage());
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
            
            return Storage::disk('public')->download($file->file_path, $file->original_name);
        } catch (\Exception $e) {
            abort(404, 'Archivo no encontrado');
        }
    }

    public function delete($id)
    {
        try {
            $file = UploadedFile::findOrFail($id);
            
            // Eliminar archivo físico
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
            
            // Procesar el archivo para mostrar todos los datos
            $fullPath = storage_path('app/public/' . $file->file_path);
            $processedData = $this->processAndValidateExcel($fullPath, $file->document_type);
            
            if (!$processedData['valid']) {
                return response()->json([
                    'success' => false, 
                    'message' => $processedData['error']
                ], 400);
            }
            
            // Validar que tenemos datos
            if (!isset($processedData['data']) || !is_array($processedData['data'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No se pudieron procesar los datos del archivo'
                ], 400);
            }
            
            $allData = $processedData['data'];
            
            // Validar que tenemos al menos headers
            if (empty($allData)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'El archivo no contiene datos válidos'
                ], 400);
            }
            
            // Separar headers y datos
            $headers = array_shift($allData); // Primer elemento son los headers
            $dataRows = $allData; // El resto son los datos
            
            // Validar headers
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
            
            // Ajustar página si está fuera de rango
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
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Archivo no encontrado en la base de datos'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error en viewData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
            
            // Procesar archivo
            $fullPath = storage_path('app/public/' . $file->file_path);
            $processedData = $this->processAndValidateExcel($fullPath, $file->document_type);
            
            if (!$processedData['valid']) {
                abort(400, 'Error al procesar archivo');
            }
            
            // Crear nuevo archivo Excel con datos procesados
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Agregar datos procesados
            $rowNum = 1;
            foreach ($processedData['data'] as $row) {
                $colNum = 1;
                foreach ($row as $cell) {
                    $worksheet->setCellValueByColumnAndRow($colNum, $rowNum, $cell);
                    $colNum++;
                }
                $rowNum++;
            }
            
            // Estilo para headers
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ];
            $worksheet->getStyle('A1:' . $worksheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
            
            // Crear archivo temporal
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'processed_') . '.xlsx';
            $writer->save($tempFile);
            
            // Descargar archivo
            $downloadName = 'procesado_' . $file->document_type . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return response()->download($tempFile, $downloadName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error en exportProcessedData: ' . $e->getMessage());
            abort(500, 'Error al exportar datos: ' . $e->getMessage());
        }
    }

    private function validateEducationLevel($data, $expectedType, $startRow)
    {
        $levelMappings = [
            'inicial' => ['inicial', 'educación inicial', 'educacion inicial', 'jardín', 'jardin', 'cuna', 'cunas'],
            'primaria' => ['primaria', 'educación primaria', 'educacion primaria', 'primary', 'elem'],
            'secundaria' => ['secundaria', 'educación secundaria', 'educacion secundaria', 'secondary', 'sec']
        ];

        Log::info("=== VALIDANDO NIVEL EDUCATIVO ===");
        Log::info("Tipo esperado: $expectedType");
        Log::info("Buscando desde fila: $startRow");

        $foundLevels = [];
        
        // Buscar en las filas cercanas a donde encontramos DRE
        for ($i = max(0, $startRow - 5); $i < min(count($data), $startRow + 15); $i++) {
            // Verificar en múltiples columnas donde podría estar el nivel
            for ($col = 9; $col <= 12; $col++) { // Columnas 10, 11, 12, 13 (índices 9-11)
                if (isset($data[$i][$col])) {
                    $cellValue = strtolower(trim($data[$i][$col]));
                    
                    if (!empty($cellValue)) {
                        Log::info("Fila $i, Columna $col: '$cellValue'");

                        // Verificar todos los tipos de educación para encontrar coincidencias
                        foreach ($levelMappings as $levelType => $keywords) {
                            foreach ($keywords as $keyword) {
                                if (stripos($cellValue, $keyword) !== false) {
                                    $foundLevels[$levelType] = $cellValue;
                                    Log::info("Encontrado nivel '$levelType' con palabra clave '$keyword' en '$cellValue'");
                                }
                            }
                        }
                    }
                }
            }
        }

        Log::info("Niveles encontrados: " . json_encode($foundLevels));

        // Verificar si encontramos el tipo esperado
        if (isset($foundLevels[$expectedType])) {
            Log::info("✅ Validación exitosa: tipo '$expectedType' encontrado");
            return ['valid' => true];
        }

        // Si encontramos otros tipos pero no el esperado, dar error específico
        if (!empty($foundLevels)) {
            $foundTypes = array_keys($foundLevels);
            $foundTypesList = implode(', ', $foundTypes);
            
            Log::error("❌ Validación fallida: se esperaba '$expectedType' pero se encontró: $foundTypesList");
            
            return [
                'valid' => false,
                'error' => "Error de validación: El archivo contiene datos de nivel '$foundTypesList' pero seleccionaste '$expectedType'. Por favor verifica que el archivo y la selección coincidan."
            ];
        }

        // Si no encontramos ningún nivel, dar advertencia pero permitir (podría ser formato diferente)
        Log::warning("⚠️ No se pudo detectar el nivel educativo automáticamente. Continuando con validación manual...");
        
        // Hacer una búsqueda más amplia en todo el archivo
        $manualValidation = $this->manualLevelValidation($data, $expectedType);
        
        if (!$manualValidation['valid']) {
            return [
                'valid' => false,
                'error' => "No se pudo confirmar que el archivo corresponda al nivel '$expectedType'. " . $manualValidation['error']
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validación manual más exhaustiva
     */
    private function manualLevelValidation($data, $expectedType)
    {
        Log::info("=== INICIANDO VALIDACIÓN MANUAL ===");
        
        // Buscar patrones específicos según el tipo esperado
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
        $totalSearched = 0;

        // Buscar en las primeras 20 filas
        for ($i = 0; $i < min(20, count($data)); $i++) {
            $row = $data[$i];
            
            foreach ($row as $cell) {
                $cellValue = strtolower(trim($cell));
                $totalSearched++;
                
                // Buscar palabras clave
                foreach ($currentPatterns['keywords'] as $keyword) {
                    if (stripos($cellValue, $keyword) !== false) {
                        $foundPatterns++;
                        Log::info("Patrón encontrado: '$keyword' en '$cellValue'");
                    }
                }
                
                // Buscar headers específicos
                foreach ($currentPatterns['headers'] as $header) {
                    if (stripos($cellValue, $header) !== false) {
                        $foundPatterns++;
                        Log::info("Header encontrado: '$header' en '$cellValue'");
                    }
                }
            }
        }

        Log::info("Patrones encontrados: $foundPatterns de $totalSearched buscados");

        if ($foundPatterns > 0) {
            return ['valid' => true];
        }

        return [
            'valid' => false,
            'error' => "El archivo no parece contener datos del nivel '$expectedType'. Verifica que sea el archivo correcto."
        ];
    }
}