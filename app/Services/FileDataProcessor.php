<?php

namespace App\Services;

use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileDataProcessor
{
    public function extractFileData($file, $xAxis, $yAxis)
    {
        try {
            $data = [];
            
            // Leer el archivo JSON procesado
            $jsonPath = str_replace('.xlsx', '_processed.json', $file->file_path);
            
            if (!Storage::disk('public')->exists($jsonPath)) {
                Log::warning("Archivo JSON no encontrado: {$jsonPath}");
                return null;
            }
            
            $jsonContent = Storage::disk('public')->get($jsonPath);
            $fileData = json_decode($jsonContent, true);
            
            if (!$fileData || !isset($fileData['data'])) {
                Log::warning("Datos JSON inválidos para archivo: {$file->id}");
                return null;
            }
            
            // Procesar datos según los ejes seleccionados
            foreach ($fileData['data'] as $row) {
                $xValue = $this->getValueFromRow($row, $xAxis);
                $yValue = $this->getValueFromRow($row, $yAxis);
                
                if ($xValue && is_numeric($yValue)) {
                    if (!isset($data[$xValue])) {
                        $data[$xValue] = 0;
                    }
                    $data[$xValue] += (float) $yValue;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error("Error procesando archivo {$file->id}: " . $e->getMessage());
            return null;
        }
    }
    
    public function detectEducationalLevel($file)
    {
        $name = strtolower($file->original_name);
        $type = strtolower($file->document_type);
        
        if (strpos($name, 'inicial') !== false || strpos($type, 'inicial') !== false) {
            return 'Inicial';
        }
        
        if (strpos($name, 'primaria') !== false || strpos($type, 'primaria') !== false) {
            return 'Primaria';
        }
        
        if (strpos($name, 'secundaria') !== false || strpos($type, 'secundaria') !== false) {
            return 'Secundaria';
        }
        
        return 'Inicial'; // Fallback
    }
    
    public function getLevelColor($level)
    {
        $colors = [
            'Inicial' => '#3B82F6',    // Blue
            'Primaria' => '#10B981',   // Green
            'Secundaria' => '#8B5CF6', // Purple
        ];
        
        return $colors[$level] ?? '#6B7280'; // Gray fallback
    }
    
    private function getValueFromRow($row, $axis)
    {
        // Mapear nombres de columnas comunes
        $columnMappings = [
            'ugel' => ['ugel', 'cod_ugel', 'codigo_ugel'],
            'departamento' => ['departamento', 'depart', 'dept'],
            'provincia' => ['provincia', 'prov'],
            'distrito' => ['distrito', 'dist'],
            'total_matriculados' => ['total_matriculados', 'matriculados', 'total', 'estudiantes'],
            'total_secciones' => ['total_secciones', 'secciones', 'secc'],
            'tipo_ie' => ['tipo_ie', 'tipo', 'modalidad']
        ];
        
        $possibleColumns = $columnMappings[$axis] ?? [$axis];
        
        foreach ($possibleColumns as $column) {
            if (isset($row[$column])) {
                return $row[$column];
            }
        }
        
        return null;
    }
}