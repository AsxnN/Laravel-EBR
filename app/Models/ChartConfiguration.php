<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'chart_name',
        'title',
        'description',
        'chart_type',
        'education_level',
        'x_axis_field',
        'y_axis_fields',
        'chart_config',
        'order_position'
    ];

    protected $casts = [
        'y_axis_fields' => 'array',
        'chart_config' => 'array'
    ];

    protected $attributes = [
        'order_position' => 0
    ];

    // ========== RELACIONES ==========

    public function template()
    {
        return $this->belongsTo(ChartTemplate::class, 'template_id');
    }

    // ========== ACCESSORS ==========

    public function getTitleAttribute($value)
    {
        return $value ?: $this->chart_name ?: 'Gráfico sin título';
    }

    public function getChartTypeTextAttribute()
    {
        $types = [
            'bar' => 'Gráfico de Barras',
            'line' => 'Gráfico de Líneas',
            'pie' => 'Gráfico Circular',
            'doughnut' => 'Gráfico de Dona',
            'area' => 'Gráfico de Área',
            'radar' => 'Gráfico Radar',
            'scatter' => 'Gráfico de Dispersión',
            'table' => 'Tabla de Datos'
        ];

        return $types[$this->chart_type] ?? ucfirst($this->chart_type);
    }

    public function getEducationLevelTextAttribute()
    {
        $levels = [
            'inicial' => 'Inicial',
            'primaria' => 'Primaria',
            'secundaria' => 'Secundaria',
            'multi_level' => 'Multi-nivel'
        ];

        return $levels[$this->education_level] ?? ucfirst($this->education_level);
    }

    public function getXAxisFieldTextAttribute()
    {
        $fields = [
            'dre' => 'DRE',
            'ugel' => 'UGEL',
            'departamento' => 'Departamento',
            'provincia' => 'Provincia',
            'distrito' => 'Distrito',
            'mes' => 'Mes',
            'tipo_ie' => 'Tipo de IE',
            'codigo_modular' => 'Código Modular'
        ];

        return $fields[$this->x_axis_field] ?? ucfirst(str_replace('_', ' ', $this->x_axis_field));
    }

    public function getYAxisFieldsTextAttribute()
    {
        if (!$this->y_axis_fields || !is_array($this->y_axis_fields)) {
            return 'No definido';
        }

        $fieldLabels = [
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
            'nomina_por_rectificar' => 'Nómina por Rectificar',
            // Campos específicos por nivel
            'cero_hombres' => '0 años - Hombres',
            'cero_mujeres' => '0 años - Mujeres',
            'primero_hombres' => '1° - Hombres',
            'primero_mujeres' => '1° - Mujeres',
            'segundo_hombres' => '2° - Hombres',
            'segundo_mujeres' => '2° - Mujeres',
            'tercero_hombres' => '3° - Hombres',
            'tercero_mujeres' => '3° - Mujeres',
            'cuarto_hombres' => '4° - Hombres',
            'cuarto_mujeres' => '4° - Mujeres',
            'quinto_hombres' => '5° - Hombres',
            'quinto_mujeres' => '5° - Mujeres',
            'sexto_hombres' => '6° - Hombres',
            'sexto_mujeres' => '6° - Mujeres',
            'mas_quinto_hombres' => 'Más de 5 años - Hombres',
            'mas_quinto_mujeres' => 'Más de 5 años - Mujeres'
        ];

        return collect($this->y_axis_fields)
            ->map(fn($field) => $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)))
            ->join(', ');
    }

    public function getYAxisFieldsCountAttribute()
    {
        return is_array($this->y_axis_fields) ? count($this->y_axis_fields) : 0;
    }

    public function getHasLegendAttribute()
    {
        return $this->y_axis_fields_count > 1 || 
               ($this->template && $this->template->template_type === 'multi_level');
    }

    public function getChartColorsAttribute()
    {
        if (!$this->chart_config || !isset($this->chart_config['chart_options']['colors'])) {
            return $this->generateDefaultColors();
        }

        return $this->chart_config['chart_options']['colors'];
    }

    // ========== SCOPES ==========

    public function scopeForEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('chart_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_position');
    }

    public function scopeWithXAxisField($query, $field)
    {
        return $query->where('x_axis_field', $field);
    }

    public function scopeWithYAxisField($query, $field)
    {
        return $query->whereJsonContains('y_axis_fields', $field);
    }

    // ========== MÉTODOS ==========

    public function hasYAxisField($field)
    {
        return is_array($this->y_axis_fields) && in_array($field, $this->y_axis_fields);
    }

    public function isCompatibleWithLevel($level)
    {
        return $this->education_level === $level || 
               $this->education_level === 'multi_level';
    }

    public function generateDefaultColors()
    {
        $defaultColors = [
            '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd',
            '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf',
            '#aec7e8', '#ffbb78', '#98df8a', '#ff9896', '#c5b0d5'
        ];

        $count = $this->y_axis_fields_count;
        return array_slice($defaultColors, 0, max(1, $count));
    }

    public function getChartOptions()
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => $this->has_legend,
                    'position' => 'top'
                ],
                'title' => [
                    'display' => true,
                    'text' => $this->title
                ]
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => $this->x_axis_field_text
                    ]
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad'
                    ],
                    'beginAtZero' => true
                ]
            ]
        ];

        if ($this->chart_config && isset($this->chart_config['chart_options'])) {
            return array_merge_recursive($defaultOptions, $this->chart_config['chart_options']);
        }

        return $defaultOptions;
    }

    public function generateSampleData()
    {
        $sampleLabels = match($this->x_axis_field) {
            'dre' => ['DRE Lima Metropolitana', 'DRE Arequipa', 'DRE Cusco', 'DRE Piura'],
            'ugel' => ['UGEL 01', 'UGEL 02', 'UGEL 03', 'UGEL 04'],
            'departamento' => ['Lima', 'Arequipa', 'Cusco', 'Piura'],
            'provincia' => ['Lima', 'Arequipa', 'Cusco', 'Piura'],
            'distrito' => ['San Isidro', 'Miraflores', 'Surco', 'La Molina'],
            'mes' => ['Enero', 'Febrero', 'Marzo', 'Abril'],
            'tipo_ie' => ['Pública', 'Privada', 'Parroquial'],
            default => ['Categoría A', 'Categoría B', 'Categoría C', 'Categoría D']
        };

        $datasets = [];
        $colors = $this->chart_colors;

        foreach ($this->y_axis_fields as $index => $field) {
            $datasets[] = [
                'label' => $this->getFieldLabel($field),
                'data' => array_map(fn() => rand(50, 500), $sampleLabels),
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 1
            ];
        }

        return [
            'labels' => $sampleLabels,
            'datasets' => $datasets
        ];
    }

    private function getFieldLabel($field)
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

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    // ========== BOOT ==========

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($chart) {
            if (!$chart->order_position) {
                $chart->order_position = static::where('template_id', $chart->template_id)
                                               ->max('order_position') + 1;
            }
        });
    }
}