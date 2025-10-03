<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\UploadedFile;

class ReportChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'template_id',
        'chart_title',
        'file_ids',
        'chart_data',
        'chart_config',
        'notes',
        'order'
    ];

    protected $casts = [
        'file_ids' => 'array',
        'chart_data' => 'array',
        'chart_config' => 'array'
    ];

    // Relaciones
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function template()
    {
        return $this->belongsTo(ChartTemplate::class, 'template_id');
    }

    // CORREGIR: En lugar de belongsToMany, usar un accessor que obtenga los archivos desde file_ids
    public function getFilesAttribute()
    {
        if (is_array($this->file_ids) && !empty($this->file_ids)) {
            return UploadedFile::whereIn('id', $this->file_ids)->get();
        }
        return collect();
    }

    // Método para obtener archivos (alternativo)
    public function getUploadedFiles()
    {
        if (is_array($this->file_ids) && !empty($this->file_ids)) {
            return UploadedFile::whereIn('id', $this->file_ids)->get();
        }
        return collect();
    }

    // Accessor para contar archivos
    public function getFileCountAttribute()
    {
        return is_array($this->file_ids) ? count($this->file_ids) : 0;
    }
}