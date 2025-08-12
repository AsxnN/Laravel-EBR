<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Comparison extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'template_id',
        'comparison_type',
        'education_levels',
        'comparison_period',
        'geo_filters',
        'charts_data',
        'status',
        'total_institutions',
        'total_students',
        'dataset_path',
        'created_by'
    ];

    protected $casts = [
        'education_levels' => 'array',
        'geo_filters' => 'array',
        'charts_data' => 'array'
    ];

    protected $attributes = [
        'status' => 'processing'
    ];

    // ========== RELACIONES ==========

    public function template()
    {
        return $this->belongsTo(ChartTemplate::class, 'template_id');
    }

    public function files()
    {
        return $this->belongsToMany(UploadedFile::class, 'comparison_file', 'comparison_id', 'uploaded_file_id')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========== ACCESSORS ==========

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'processing' => 'Procesando',
            'ready' => 'Listo',
            'error' => 'Error',
            'draft' => 'Borrador',
            default => 'Desconocido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'processing' => 'yellow',
            'ready' => 'green',
            'error' => 'red',
            'draft' => 'gray',
            default => 'gray'
        };
    }

    public function getComparisonTypeTextAttribute()
    {
        return match($this->comparison_type) {
            'single_level' => 'Un Solo Nivel',
            'multi_level' => 'Multi-nivel',
            default => 'No definido'
        };
    }

    public function getEducationLevelsTextAttribute()
    {
        if (!$this->education_levels || !is_array($this->education_levels)) {
            return 'No definido';
        }

        $levels = [
            'inicial' => 'Inicial',
            'primaria' => 'Primaria',
            'secundaria' => 'Secundaria'
        ];

        return collect($this->education_levels)
            ->map(fn($level) => $levels[$level] ?? ucfirst($level))
            ->join(', ');
    }

    public function getFilesCountAttribute()
    {
        return $this->files()->count();
    }

    public function getChartsCountAttribute()
    {
        return is_array($this->charts_data) ? count($this->charts_data) : 0;
    }

    public function getProcessingTimeAttribute()
    {
        if (!$this->created_at || !$this->updated_at) {
            return 'No disponible';
        }

        $seconds = $this->updated_at->diffInSeconds($this->created_at);
        
        if ($seconds < 60) {
            return $seconds . ' segundos';
        } elseif ($seconds < 3600) {
            return round($seconds / 60, 1) . ' minutos';
        } else {
            return round($seconds / 3600, 1) . ' horas';
        }
    }

    public function getComparisonPeriodTextAttribute()
    {
        if (!$this->comparison_period) {
            return 'No especificado';
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $this->comparison_period);
            return $date->locale('es')->isoFormat('MMMM [de] YYYY');
        } catch (\Exception $e) {
            return $this->comparison_period;
        }
    }

    public function getGeoFiltersTextAttribute()
    {
        if (!$this->geo_filters || !is_array($this->geo_filters)) {
            return 'Sin filtros geográficos';
        }

        $filters = [];
        
        foreach ($this->geo_filters as $type => $values) {
            if (!empty($values) && is_array($values)) {
                $filterText = ucfirst($type) . ': ' . implode(', ', array_slice($values, 0, 3));
                if (count($values) > 3) {
                    $filterText .= ' y ' . (count($values) - 3) . ' más';
                }
                $filters[] = $filterText;
            }
        }

        return empty($filters) ? 'Sin filtros geográficos' : implode(' | ', $filters);
    }

    public function getTotalInstitutionsFormattedAttribute()
    {
        return number_format($this->total_institutions ?? 0);
    }

    public function getTotalStudentsFormattedAttribute()
    {
        return number_format($this->total_students ?? 0);
    }

    public function getHasDatasetFileAttribute()
    {
        return !empty($this->dataset_path) && 
               file_exists(storage_path('app/' . $this->dataset_path));
    }

    public function getDatasetFileSizeAttribute()
    {
        if (!$this->has_dataset_file) {
            return 'No disponible';
        }

        $bytes = filesize(storage_path('app/' . $this->dataset_path));
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // ========== SCOPES ==========

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeWithErrors($query)
    {
        return $query->where('status', 'error');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('comparison_type', $type);
    }

    public function scopeSingleLevel($query)
    {
        return $query->where('comparison_type', 'single_level');
    }

    public function scopeMultiLevel($query)
    {
        return $query->where('comparison_type', 'multi_level');
    }

    public function scopeForEducationLevel($query, $level)
    {
        return $query->whereJsonContains('education_levels', $level);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeInPeriod($query, $period)
    {
        return $query->where('comparison_period', $period);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    public function scopeWithGeoFilter($query, $type, $value)
    {
        return $query->whereJsonContains("geo_filters->$type", $value);
    }

    // ========== MÉTODOS ==========

    public function isReady()
    {
        return $this->status === 'ready';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function hasErrors()
    {
        return $this->status === 'error';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function canBeDeleted()
    {
        return in_array($this->status, ['ready', 'error', 'draft']);
    }

    public function canBeRegenerated()
    {
        return in_array($this->status, ['ready', 'error']);
    }

    public function hasEducationLevel($level)
    {
        return is_array($this->education_levels) && in_array($level, $this->education_levels);
    }

    public function hasGeoFilter($type)
    {
        return is_array($this->geo_filters) && 
               isset($this->geo_filters[$type]) && 
               !empty($this->geo_filters[$type]);
    }

    public function getGeoFilterValues($type)
    {
        if (!$this->hasGeoFilter($type)) {
            return [];
        }

        return $this->geo_filters[$type];
    }

    public function markAsReady($chartsData = null, $totalInstitutions = null, $totalStudents = null)
    {
        $updateData = ['status' => 'ready'];
        
        if ($chartsData !== null) {
            $updateData['charts_data'] = $chartsData;
        }
        
        if ($totalInstitutions !== null) {
            $updateData['total_institutions'] = $totalInstitutions;
        }
        
        if ($totalStudents !== null) {
            $updateData['total_students'] = $totalStudents;
        }

        return $this->update($updateData);
    }

    public function markAsError($errorMessage = null)
    {
        $updateData = ['status' => 'error'];
        
        if ($errorMessage) {
            $updateData['error_message'] = $errorMessage;
        }

        return $this->update($updateData);
    }

    public function markAsProcessing()
    {
        return $this->update(['status' => 'processing']);
    }

    public function getChartData($chartId)
    {
        if (!is_array($this->charts_data)) {
            return null;
        }

        foreach ($this->charts_data as $chart) {
            if (isset($chart['chart_id']) && $chart['chart_id'] == $chartId) {
                return $chart;
            }
        }

        return null;
    }

    public function updateChartData($chartId, $newData)
    {
        $chartsData = $this->charts_data ?: [];
        $updated = false;

        foreach ($chartsData as &$chart) {
            if (isset($chart['chart_id']) && $chart['chart_id'] == $chartId) {
                $chart = array_merge($chart, $newData);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            $chartsData[] = array_merge(['chart_id' => $chartId], $newData);
        }

        return $this->update(['charts_data' => $chartsData]);
    }

    public function getStatistics()
    {
        return [
            'files_count' => $this->files_count,
            'charts_count' => $this->charts_count,
            'total_institutions' => $this->total_institutions_formatted,
            'total_students' => $this->total_students_formatted,
            'processing_time' => $this->processing_time,
            'creation_date' => $this->created_at->format('d/m/Y H:i'),
            'last_update' => $this->updated_at->format('d/m/Y H:i'),
            'has_dataset_file' => $this->has_dataset_file,
            'dataset_file_size' => $this->dataset_file_size,
            'geo_filters_text' => $this->geo_filters_text,
            'comparison_period_text' => $this->comparison_period_text
        ];
    }

    public function generateFileName($extension = 'xlsx')
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->name);
        $date = $this->created_at->format('Y-m-d');
        
        return "comparacion_{$safeName}_{$date}.{$extension}";
    }

    public function isCompatibleWithTemplate(ChartTemplate $template)
    {
        // Verificar si los niveles educativos son compatibles
        if (!is_array($this->education_levels) || !is_array($template->education_levels)) {
            return false;
        }

        foreach ($this->education_levels as $level) {
            if (!in_array($level, $template->education_levels)) {
                return false;
            }
        }

        return true;
    }

    public function cleanup()
    {
        // Eliminar archivo de dataset si existe
        if ($this->has_dataset_file) {
            try {
                unlink(storage_path('app/' . $this->dataset_path));
            } catch (\Exception $e) {
                \Log::warning('Could not delete dataset file: ' . $e->getMessage());
            }
        }
    }

    // ========== BOOT ==========

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($comparison) {
            $comparison->cleanup();
        });

        static::creating(function ($comparison) {
            if (!$comparison->created_by && auth()->check()) {
                $comparison->created_by = auth()->id();
            }
        });
    }
}