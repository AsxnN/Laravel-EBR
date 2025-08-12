<?php
// filepath: c:\laragon\www\EBR\app\Models\SecundariaData.php

namespace App\Models;

class SecundariaData extends BaseEducationalData
{
    protected $table = 'secundaria_data';

    protected $fillable = [
        'uploaded_file_id',
        'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
        'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
        'total_matriculados', 'matricula_definitiva', 'matricula_proceso',
        'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
        'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
        'nomina_por_rectificar',
        'primero_hombres', 'primero_mujeres',
        'segundo_hombres', 'segundo_mujeres',
        'tercero_hombres', 'tercero_mujeres',
        'cuarto_hombres', 'cuarto_mujeres',
        'quinto_hombres', 'quinto_mujeres'
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
        'primero_hombres' => 'integer',
        'primero_mujeres' => 'integer',
        'segundo_hombres' => 'integer',
        'segundo_mujeres' => 'integer',
        'tercero_hombres' => 'integer',
        'tercero_mujeres' => 'integer',
        'cuarto_hombres' => 'integer',
        'cuarto_mujeres' => 'integer',
        'quinto_hombres' => 'integer',
        'quinto_mujeres' => 'integer',
    ];

    public function getSpecificFields(): array
    {
        return [
            'primero_hombres', 'primero_mujeres',
            'segundo_hombres', 'segundo_mujeres',
            'tercero_hombres', 'tercero_mujeres',
            'cuarto_hombres', 'cuarto_mujeres',
            'quinto_hombres', 'quinto_mujeres'
        ];
    }

    public function getEducationLevel(): string
    {
        return 'secundaria';
    }

    public function getGrades()
    {
        return [
            '1° Año' => ['hombres' => $this->primero_hombres, 'mujeres' => $this->primero_mujeres],
            '2° Año' => ['hombres' => $this->segundo_hombres, 'mujeres' => $this->segundo_mujeres],
            '3° Año' => ['hombres' => $this->tercero_hombres, 'mujeres' => $this->tercero_mujeres],
            '4° Año' => ['hombres' => $this->cuarto_hombres, 'mujeres' => $this->cuarto_mujeres],
            '5° Año' => ['hombres' => $this->quinto_hombres, 'mujeres' => $this->quinto_mujeres],
        ];
    }
}