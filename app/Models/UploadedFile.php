<?php
// filepath: c:\laragon\www\EBR\app\Models\UploadedFile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UploadedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'file_path',
        'document_type',
        'file_size',
        'total_institutions',
        'total_students',
        'processing_summary',
        'uploaded_at',
        'uploaded_by'
    ];

    protected $casts = [
        'processing_summary' => 'array',
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
        'total_institutions' => 'integer',
        'total_students' => 'integer'
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function inicialData()
    {
        return $this->hasMany(InicialData::class);
    }

    public function primariaData()
    {
        return $this->hasMany(PrimariaData::class);
    }

    public function secundariaData()
    {
        return $this->hasMany(SecundariaData::class);
    }

    // Accessor para el tamaño del archivo en formato legible
    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatBytes($this->file_size)
        );
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Obtener todos los datos educativos del archivo
    public function getEducationalData()
    {
        return match($this->document_type) {
            'inicial' => $this->inicialData,
            'primaria' => $this->primariaData,
            'secundaria' => $this->secundariaData,
            default => collect()
        };
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('uploaded_at', '>=', now()->subDays($days));
    }
}