<?php
// filepath: c:\laragon\www\EBR\app\Services\ExcelProcessorService.php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ExcelProcessorService
{
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
            'quinto_mujeres'
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

    public function processExcel(string $filePath, string $expectedType): array
    {
        try {
            Log::info("=== INICIANDO PROCESAMIENTO EXCEL ===");
            Log::info("Archivo: $filePath");
            Log::info("Tipo esperado: $expectedType");

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // 1. Separar todas las celdas agrupadas
            Log::info("PASO 1: Separando celdas agrupadas...");
            $this->ungroupAllMergedCells($worksheet);
            
            $data = $worksheet->toArray();
            Log::info("Datos cargados: " . count($data) . " filas");
            
            // 2. Encontrar fila con DRE y validar nivel educativo
            Log::info("PASO 2: Buscando fila DRE y validando nivel...");
            $dreRowInfo = $this->findDreRowAndValidateLevel($data, $expectedType);
            
            if (!$dreRowInfo['valid']) {
                return ['valid' => false, 'error' => $dreRowInfo['error']];
            }
            
            $dreRowIndex = $dreRowInfo['row_index'];
            Log::info("Fila DRE encontrada en índice: $dreRowIndex");
            
            // 3. Analizar columnas vacías en la fila DRE
            Log::info("PASO 3: Analizando columnas vacías...");
            $emptyColumns = $this->analyzeEmptyColumns($data, $dreRowIndex);
            
            // 4. Filtrar datos: eliminar filas desde 0 hasta DRE y columnas vacías
            Log::info("PASO 4: Filtrando datos...");
            $filteredData = $this->filterExcelData($data, $dreRowIndex, $emptyColumns);
            
            // 5. Aplicar encabezados según nivel educativo
            Log::info("PASO 5: Aplicando encabezados...");
            $processedData = $this->applyHeaders($filteredData, $expectedType);
            
            // 6. Extraer estadísticas
            Log::info("PASO 6: Extrayendo estadísticas...");
            $summary = $this->extractSummary($processedData, $expectedType);
            
            Log::info("=== PROCESAMIENTO COMPLETADO EXITOSAMENTE ===");
            
            return [
                'valid' => true,
                'data' => $processedData,
                'summary' => $summary,
                'debug_info' => [
                    'dre_row_found' => $dreRowIndex,
                    'empty_columns_removed' => $emptyColumns,
                    'total_data_rows' => count($processedData) - 1,
                    'headers_applied' => count(self::HEADERS[$expectedType])
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('ERROR PROCESANDO EXCEL: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ['valid' => false, 'error' => 'Error procesando archivo: ' . $e->getMessage()];
        }
    }

    private function ungroupAllMergedCells($worksheet): void
    {
        try {
            $mergedCells = $worksheet->getMergeCells();
            Log::info("Celdas agrupadas encontradas: " . count($mergedCells));
            
            foreach ($mergedCells as $mergedRange) {
                $topLeftCell = explode(':', $mergedRange)[0];
                $cellValue = $worksheet->getCell($topLeftCell)->getValue();
                
                // Desagrupar
                $worksheet->unmergeCells($mergedRange);
                
                // Solo mantener valor en primera celda
                $rangeParts = explode(':', $mergedRange);
                $startCell = $rangeParts[0];
                $endCell = $rangeParts[1];
                
                $startCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($startCell);
                $endCoords = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($endCell);
                
                $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startCoords[0]);
                $startRow = $startCoords[1];
                $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCoords[0]);
                $endRow = $endCoords[1];
                
                // Establecer valor solo en primera celda
                $worksheet->setCellValue($startCell, $cellValue);
                
                // Limpiar el resto
                for ($row = $startRow; $row <= $endRow; $row++) {
                    for ($col = $startCol; $col <= $endCol; $col++) {
                        $cellRef = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                        if ($cellRef !== $startCell) {
                            $worksheet->setCellValue($cellRef, '');
                        }
                    }
                }
                
                Log::info("Celda agrupada procesada: $mergedRange -> valor mantenido en $startCell");
            }
            
        } catch (\Exception $e) {
            Log::warning('Error separando celdas agrupadas: ' . $e->getMessage());
        }
    }

    private function findDreRowAndValidateLevel(array $data, string $expectedType): array
    {
        $levelKeywords = [
            'inicial' => ['inicial', 'educación inicial', 'educacion inicial', 'jardín', 'jardin'],
            'primaria' => ['primaria', 'educación primaria', 'educacion primaria', 'primary'],
            'secundaria' => ['secundaria', 'educación secundaria', 'educacion secundaria', 'secondary']
        ];

        foreach ($data as $rowIndex => $row) {
            // Buscar DRE en primera columna (índice 0)
            if (isset($row[0]) && stripos($row[0], 'DRE') !== false) {
                Log::info("DRE encontrado en fila $rowIndex, columna 0: '{$row[0]}'");
                
                // Validar nivel educativo en columna 10 (índice 9)
                if (isset($row[9])) {
                    $cellValue = strtolower(trim($row[9]));
                    Log::info("Validando nivel en columna 10: '$cellValue'");
                    
                    foreach ($levelKeywords[$expectedType] as $keyword) {
                        if (stripos($cellValue, $keyword) !== false) {
                            Log::info("✓ Nivel educativo '$expectedType' validado con palabra clave: '$keyword'");
                            return ['valid' => true, 'row_index' => $rowIndex];
                        }
                    }
                }
                
                Log::error("✗ El archivo no corresponde al nivel '$expectedType'. Valor en columna 10: '{$row[9]}'");
                return [
                    'valid' => false, 
                    'error' => "El archivo no corresponde al nivel '$expectedType'. Verificar columna 10 (valor encontrado: '{$row[9]}')."
                ];
            }
        }

        return ['valid' => false, 'error' => 'No se encontró la palabra "DRE" en la primera columna.'];
    }

    private function analyzeEmptyColumns(array $data, int $dreRowIndex): array
    {
        $dreRow = $data[$dreRowIndex];
        $emptyColumns = [];
        
        Log::info("=== ANÁLISIS DE COLUMNAS VACÍAS EN FILA DRE ($dreRowIndex) ===");
        
        foreach ($dreRow as $colIndex => $cellValue) {
            $cleanValue = trim(strval($cellValue));
            
            // Considerar vacía si: es null, string vacío, o valores especiales
            // PERO no si es 0 (número cero)
            if ($cleanValue === '' || 
                $cleanValue === 'NULL' || 
                $cleanValue === '#N/A' || 
                is_null($cellValue)) {
                
                $emptyColumns[] = $colIndex;
                Log::info("Columna $colIndex: VACÍA (valor: '$cleanValue')");
            } else {
                Log::info("Columna $colIndex: CON DATOS (valor: '$cleanValue')");
            }
        }
        
        Log::info("Total columnas vacías: " . count($emptyColumns));
        Log::info("Columnas a eliminar: [" . implode(', ', $emptyColumns) . "]");
        
        return $emptyColumns;
    }

    private function filterExcelData(array $data, int $dreRowIndex, array $emptyColumns): array
    {
        // 1. Eliminar filas desde 0 hasta la fila DRE (inclusive)
        $filteredRows = array_slice($data, $dreRowIndex + 1);
        Log::info("Filas eliminadas: 0 hasta $dreRowIndex (" . ($dreRowIndex + 1) . " filas eliminadas)");
        Log::info("Filas restantes: " . count($filteredRows));
        
        // 2. Eliminar columnas vacías (de derecha a izquierda para evitar problemas de índices)
        if (!empty($emptyColumns)) {
            $emptyColumns = array_reverse($emptyColumns);
            
            foreach ($filteredRows as $rowIndex => &$row) {
                foreach ($emptyColumns as $colIndex) {
                    if (isset($row[$colIndex])) {
                        array_splice($row, $colIndex, 1);
                    }
                }
            }
            unset($row);
            
            Log::info("Columnas eliminadas: [" . implode(', ', array_reverse($emptyColumns)) . "]");
        }
        
        // 3. Eliminar filas completamente vacías
        $originalCount = count($filteredRows);
        $filteredRows = array_filter($filteredRows, function($row) {
            foreach ($row as $cell) {
                $cleanValue = trim(strval($cell));
                if ($cleanValue !== '' && $cleanValue !== 'NULL' && $cleanValue !== '#N/A') {
                    return true;
                }
                if ($cleanValue === '0') {
                    return true;
                }
            }
            return false;
        });
        
        $filteredRows = array_values($filteredRows); // Reindexar
        Log::info("Filas vacías eliminadas: " . ($originalCount - count($filteredRows)));
        Log::info("Filas finales: " . count($filteredRows));
        
        return $filteredRows;
    }

    private function applyHeaders(array $data, string $documentType): array
    {
        $headers = self::HEADERS[$documentType];
        Log::info("Aplicando " . count($headers) . " encabezados para '$documentType'");
        
        // Ajustar datos para que coincidan con el número de headers
        $adjustedData = [];
        foreach ($data as $rowIndex => $row) {
            $adjustedRow = [];
            for ($i = 0; $i < count($headers); $i++) {
                $value = isset($row[$i]) ? trim($row[$i]) : '';
                // Limpiar valores especiales pero mantener 0
                if ($value === 'NULL' || $value === '#N/A') {
                    $value = '';
                } elseif ($value === '0' || $value === 0) {
                    $value = '0';
                }
                $adjustedRow[] = $value;
            }
            $adjustedData[] = $adjustedRow;
        }
        
        // Insertar headers al inicio
        array_unshift($adjustedData, $headers);
        
        Log::info("Headers aplicados. Total filas con headers: " . count($adjustedData));
        
        return $adjustedData;
    }

    private function extractSummary(array $data, string $documentType): array
    {
        if (empty($data) || count($data) <= 1) {
            return [
                'institutions' => 0, 
                'students' => 0, 
                'total_records' => 0,
                'document_type' => $documentType
            ];
        }

        $headers = $data[0];
        $dataRows = array_slice($data, 1);
        
        $institutions = [];
        $totalStudents = 0;
        $byDepartment = [];
        $byUgel = [];
        
        // Encontrar índices importantes
        $institutionIndex = array_search('nombre_ie', $headers);
        $matriculadosIndex = array_search('total_matriculados', $headers);
        $departmentIndex = array_search('departamento', $headers);
        $ugelIndex = array_search('ugel', $headers);
        
        Log::info("Índices encontrados - IE: $institutionIndex, Matriculados: $matriculadosIndex, Depto: $departmentIndex, UGEL: $ugelIndex");
        
        foreach ($dataRows as $row) {
            // Contar instituciones únicas
            if ($institutionIndex !== false && !empty($row[$institutionIndex])) {
                $institutions[$row[$institutionIndex]] = true;
            }
            
            // Sumar matriculados
            if ($matriculadosIndex !== false && is_numeric($row[$matriculadosIndex])) {
                $totalStudents += intval($row[$matriculadosIndex]);
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
        
        $summary = [
            'institutions' => count($institutions),
            'students' => $totalStudents,
            'total_records' => count($dataRows),
            'document_type' => $documentType,
            'by_department' => $byDepartment,
            'by_ugel' => $byUgel
        ];
        
        Log::info("Resumen extraído: " . json_encode($summary));
        
        return $summary;
    }
}