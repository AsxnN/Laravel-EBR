<?php
// filepath: c:\laragon\www\EBR\app\Models\InicialData.php

namespace App\Models;

class InicialData extends BaseEducationalData
{
    protected $table = 'inicial_data';

    protected $fillable = [
        'uploaded_file_id',
        'dre', 'ugel', 'departamento', 'provincia', 'distrito', 'centro_poblado',
        'codigo_modular', 'anexo', 'nombre_ie', 'nivel', 'modalidad', 'tipo_ie',
        'total_matriculados', 'matricula_definitiva', 'matricula_proceso',
        'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
        'total_grados', 'total_secciones', 'nomina_generada', 'nomina_aprobada',
        'nomina_por_rectificar',
        'cero_hombres', 'cero_mujeres',
        'primero_hombres', 'primero_mujeres',
        'segundo_hombres', 'segundo_mujeres',
        'tercero_hombres', 'tercero_mujeres',
        'cuarto_hombres', 'cuarto_mujeres',
        'quinto_hombres', 'quinto_mujeres',
        'mas_quinto_hombres', 'mas_quinto_mujeres' // Asegurar que estén incluidos
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
        'cero_hombres' => 'integer',
        'cero_mujeres' => 'integer',
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
        'mas_quinto_hombres' => 'integer',
        'mas_quinto_mujeres' => 'integer',
    ];

    public function getSpecificFields(): array
    {
        return [
            'cero_hombres', 'cero_mujeres',
            'primero_hombres', 'primero_mujeres',
            'segundo_hombres', 'segundo_mujeres',
            'tercero_hombres', 'tercero_mujeres',
            'cuarto_hombres', 'cuarto_mujeres',
            'quinto_hombres', 'quinto_mujeres'
        ];
    }

    public function getEducationLevel(): string
    {
        return 'inicial';
    }

    public function getAgeGroups()
    {
        return [
            '0 años' => ['hombres' => $this->cero_hombres, 'mujeres' => $this->cero_mujeres],
            '3 años' => ['hombres' => $this->primero_hombres, 'mujeres' => $this->primero_mujeres],
            '4 años' => ['hombres' => $this->segundo_hombres, 'mujeres' => $this->segundo_mujeres],
            '5 años' => ['hombres' => $this->tercero_hombres, 'mujeres' => $this->tercero_mujeres],
        ];
    }
}