<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'template_type',
        'education_levels',
        'status',
        'created_by'
    ];

    protected $casts = [
        'education_levels' => 'array'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    // ========== RELACIONES ==========

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function charts()
    {
        return $this->hasMany(ChartConfiguration::class, 'template_id')
                   ->orderBy('order_position');
    }

    public function comparisons()
    {
        return $this->hasMany(Comparison::class, 'template_id');
    }

    // ========== ACCESSORS ==========

    public function getEducationLevelsTextAttribute()
    {
        $levels = [
            'inicial' => 'Inicial',
            'primaria' => 'Primaria',
            'secundaria' => 'Secundaria'
        ];

        if (!$this->education_levels || !is_array($this->education_levels)) {
            return 'No definido';
        }

        return collect($this->education_levels)
            ->map(fn($level) => $levels[$level] ?? ucfirst($level))
            ->join(', ');
    }

    public function getTemplateTypeTextAttribute()
    {
        return match($this->template_type) {
            'single_level' => 'Un Solo Nivel',
            'multi_level' => 'Multi-nivel',
            default => 'No definido'
        };
    }

    public function getChartsCountAttribute()
    {
        return $this->charts()->count();
    }

    public function getComparisonsCountAttribute()
    {
        return $this->comparisons()->count();
    }

    public function getLastUsedAttribute()
    {
        return $this->comparisons()->max('created_at');
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            default => 'Desconocido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'gray'
        };
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    public function scopeSingleLevel($query)
    {
        return $query->where('template_type', 'single_level');
    }

    public function scopeMultiLevel($query)
    {
        return $query->where('template_type', 'multi_level');
    }

    public function scopeForEducationLevel($query, $level)
    {
        return $query->whereJsonContains('education_levels', $level);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->whereHas('comparisons', function($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days));
        });
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('comparisons')
                    ->orderBy('comparisons_count', 'desc')
                    ->limit($limit);
    }

    // ========== MÉTODOS ==========

    public function canBeDeleted()
    {
        return $this->comparisons()->count() === 0;
    }

    public function isUsedRecently($days = 30)
    {
        return $this->comparisons()
                   ->where('created_at', '>=', now()->subDays($days))
                   ->exists();
    }

    public function hasEducationLevel($level)
    {
        return is_array($this->education_levels) && in_array($level, $this->education_levels);
    }

    public function isCompatibleWith($files)
    {
        if (!is_array($this->education_levels)) {
            return false;
        }

        foreach ($files as $file) {
            $fileLevel = strtolower($file->document_type);
            if (!in_array($fileLevel, $this->education_levels)) {
                return false;
            }
        }

        return true;
    }

    public function getAvailableFields()
    {
        $commonFields = [
            'total_matriculados', 'matricula_definitiva', 'matricula_proceso',
            'dni_validado', 'dni_sin_validar', 'registro_sin_dni',
            'total_grados', 'total_secciones', 'nomina_generada',
            'nomina_aprobada', 'nomina_por_rectificar'
        ];

        $levelSpecificFields = [];

        if ($this->hasEducationLevel('inicial')) {
            $levelSpecificFields = array_merge($levelSpecificFields, [
                'cero_hombres', 'cero_mujeres', 'primero_hombres', 'primero_mujeres',
                'segundo_hombres', 'segundo_mujeres', 'tercero_hombres', 'tercero_mujeres',
                'cuarto_hombres', 'cuarto_mujeres', 'quinto_hombres', 'quinto_mujeres',
                'mas_quinto_hombres', 'mas_quinto_mujeres'
            ]);
        }

        if ($this->hasEducationLevel('primaria')) {
            $levelSpecificFields = array_merge($levelSpecificFields, [
                'primero_hombres', 'primero_mujeres', 'segundo_hombres', 'segundo_mujeres',
                'tercero_hombres', 'tercero_mujeres', 'cuarto_hombres', 'cuarto_mujeres',
                'quinto_hombres', 'quinto_mujeres', 'sexto_hombres', 'sexto_mujeres'
            ]);
        }

        if ($this->hasEducationLevel('secundaria')) {
            $levelSpecificFields = array_merge($levelSpecificFields, [
                'primero_hombres', 'primero_mujeres', 'segundo_hombres', 'segundo_mujeres',
                'tercero_hombres', 'tercero_mujeres', 'cuarto_hombres', 'cuarto_mujeres',
                'quinto_hombres', 'quinto_mujeres'
            ]);
        }

        return array_unique(array_merge($commonFields, $levelSpecificFields));
    }

    public function generatePreviewData()
    {
        $previewData = [];

        foreach ($this->charts as $chart) {
            $previewData[] = [
                'chart_id' => $chart->id,
                'chart_name' => $chart->chart_name,
                'chart_type' => $chart->chart_type,
                'x_axis_field' => $chart->x_axis_field,
                'y_axis_fields' => $chart->y_axis_fields,
                'education_level' => $chart->education_level,
                'sample_data' => $this->generateSampleChartData($chart)
            ];
        }

        return $previewData;
    }

    private function generateSampleChartData($chart)
    {
        $sampleLabels = ['Lima', 'Arequipa', 'Cusco', 'Piura', 'La Libertad'];
        $sampleData = [];

        $yFields = is_string($chart->y_axis_fields) 
            ? json_decode($chart->y_axis_fields, true) 
            : $chart->y_axis_fields;

        foreach ($yFields as $field) {
            $sampleData[] = [
                'label' => ucfirst(str_replace('_', ' ', $field)),
                'data' => [
                    rand(100, 1000),
                    rand(100, 1000),
                    rand(100, 1000),
                    rand(100, 1000),
                    rand(100, 1000)
                ],
                'backgroundColor' => '#' . substr(md5($field), 0, 6)
            ];
        }

        return [
            'labels' => $sampleLabels,
            'datasets' => $sampleData
        ];
    }

    // ========== BOOT ==========

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            if (!$template->canBeDeleted()) {
                throw new \Exception('No se puede eliminar la plantilla porque tiene comparaciones asociadas.');
            }
        });
    }
}