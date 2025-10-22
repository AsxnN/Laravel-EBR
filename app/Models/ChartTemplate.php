<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'x_axis',
        'y_axis',
        'chart_type',
        'level_type', // Agregar este campo
        'config',
        'purpose',
        'is_active',
        'created_by'
    ];

    public function getLevelTypeLabelAttribute()
    {
        $labels = [
            'single' => 'Un Solo Nivel',
            'multiple' => 'Múltiples Niveles'
        ];
        
        return $labels[$this->level_type] ?? 'Múltiples Niveles';
    }

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getXAxisLabelAttribute()
    {
        $labels = [
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
        ];
        
        return $labels[$this->x_axis] ?? ucfirst(str_replace('_', ' ', $this->x_axis));
    }

    public function getYAxisLabelAttribute()
    {
        $labels = [
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
        ];
        
        return $labels[$this->y_axis] ?? ucfirst(str_replace('_', ' ', $this->y_axis));
    }

    public function getChartTypeLabelAttribute()
    {
        $labels = [
            'column' => 'Gráfico de Columnas',
            'bar' => 'Gráfico de Barras',
            'line' => 'Gráfico de Líneas',
            'pie' => 'Gráfico Circular',
            'table' => 'Tabla de Datos'
        ];
        
        return $labels[$this->chart_type] ?? 'Desconocido';
    }
}