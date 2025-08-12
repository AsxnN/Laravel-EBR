<?php
// filepath: c:\laragon\www\EBR\app\Models\BaseEducationalData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseEducationalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_file_id',
        'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
        'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
        'total_matriculados', 'matricula_definitiva', 'matricula_proceso',
        'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
        'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
        'nomina_por_rectificar'
    ];

    protected $casts = [
        'total_matriculados' => 'integer',
        'matricula_definitiva' => 'integer',
        'matricula_proceso' => 'integer',
        'dni_validado' => 'integer',
        'dni_sin_validar' => 'integer',
        'registro_sin_dni' => 'integer',
        'total_grados' => 'integer',
        'total_secciones' => 'integer',
        'nomina_generada' => 'integer',
        'nomina_aprobada' => 'integer',
        'nomina_por_rectificar' => 'integer',
    ];

    // Template Methods - cada modelo hijo implementa sus métodos específicos
    abstract public function getSpecificFields(): array;
    abstract public function getEducationLevel(): string;

    // Relaciones comunes
    public function uploadedFile()
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'codigo_modular', 'codigo_modular');
    }

    // Métodos de análisis comunes
    public function getTotalStudentsByGender()
    {
        $specificFields = $this->getSpecificFields();
        $hombres = 0;
        $mujeres = 0;
        
        foreach ($specificFields as $field) {
            if (str_contains($field, 'hombres')) {
                $hombres += $this->$field ?? 0;
            } elseif (str_contains($field, 'mujeres')) {
                $mujeres += $this->$field ?? 0;
            }
        }
        
        return ['hombres' => $hombres, 'mujeres' => $mujeres];
    }

    // Scope para filtros comunes
    public function scopeByDepartment($query, $department)
    {
        return $query->where('departamento', $department);
    }

    public function scopeByUgel($query, $ugel)
    {
        return $query->where('ugel', $ugel);
    }

    public function scopeByUploadedFile($query, $fileId)
    {
        return $query->where('uploaded_file_id', $fileId);
    }
}