<?php
// filepath: c:\laragon\www\EBR\app\Services\DataPersistenceService.php

namespace App\Services;

use App\Models\UploadedFile;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataPersistenceService
{
    public function saveProcessedData(array $processedData, UploadedFile $uploadedFile): array
    {
        return DB::transaction(function() use ($processedData, $uploadedFile) {
            Log::info("=== INICIANDO GUARDADO DE DATOS ===");
            
            $data = $processedData['data'];
            $headers = array_shift($data); // Remover headers
            $documentType = $uploadedFile->document_type;
            
            Log::info("Tipo de documento: $documentType");
            Log::info("Headers: " . implode(', ', $headers));
            Log::info("Registros a procesar: " . count($data));
            
            $savedRecords = 0;
            $institutionsCreated = 0;
            $institutionsUpdated = 0;
            $errors = [];
            
            foreach ($data as $rowIndex => $row) {
                try {
                    // Crear array asociativo con headers
                    $rowData = array_combine($headers, $row);
                    
                    // Validar que tenemos código modular
                    if (empty($rowData['codigo_modular'])) {
                        $errors[] = "Fila " . ($rowIndex + 1) . ": Código modular vacío";
                        continue;
                    }
                    
                    // Crear o actualizar institución
                    $institution = $this->createOrUpdateInstitution($rowData);
                    if ($institution->wasRecentlyCreated) {
                        $institutionsCreated++;
                    } else {
                        $institutionsUpdated++;
                    }
                    
                    // Crear registro de datos educativos
                    $educationalModel = EducationalDataFactory::create($documentType);
                    $educationalModel->fill($rowData);
                    $educationalModel->uploaded_file_id = $uploadedFile->id;
                    $educationalModel->save();
                    
                    $savedRecords++;
                    
                    if ($savedRecords % 100 == 0) {
                        Log::info("Procesados $savedRecords registros...");
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($rowIndex + 1) . ": " . $e->getMessage();
                    Log::error("Error procesando fila " . ($rowIndex + 1) . ": " . $e->getMessage());
                }
            }
            
            $result = [
                'saved_records' => $savedRecords,
                'institutions_created' => $institutionsCreated,
                'institutions_updated' => $institutionsUpdated,
                'errors' => $errors,
                'success_rate' => count($data) > 0 ? round(($savedRecords / count($data)) * 100, 2) : 0
            ];
            
            Log::info("=== GUARDADO COMPLETADO ===");
            Log::info("Registros guardados: $savedRecords");
            Log::info("Instituciones creadas: $institutionsCreated");
            Log::info("Instituciones actualizadas: $institutionsUpdated");
            Log::info("Errores: " . count($errors));
            
            return $result;
        });
    }

    private function createOrUpdateInstitution(array $rowData): Institution
    {
        return Institution::updateOrCreate(
            ['codigo_modular' => $rowData['codigo_modular']],
            [
                'anexo' => $rowData['anexo'] ?? '',
                'nombre_ie' => $rowData['nombre_ie'] ?? '',
                'nivel' => $rowData['nivel'] ?? '',
                'modalidad' => $rowData['modalidad'] ?? '',
                'tipo_ie' => $rowData['tipo_ie'] ?? '',
                'dre' => $rowData['dre'] ?? '',
                'ugel' => $rowData['ugel'] ?? '',
                'departamento' => $rowData['departamento'] ?? '',
                'provincia' => $rowData['provincia'] ?? '',
                'distrito' => $rowData['distrito'] ?? '',
                'centro_poblado' => $rowData['centro_poblado'] ?? ''
            ]
        );
    }
}