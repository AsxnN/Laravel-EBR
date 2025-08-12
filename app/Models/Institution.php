<?php
// filepath: c:\laragon\www\EBR\app\Models\Institution.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_modular',
        'anexo',
        'nombre_ie',
        'nivel',
        'modalidad',
        'tipo_ie',
        'dre',
        'ugel',
        'departamento',
        'provincia',
        'distrito',
        'centro_poblado'
    ];

    // Relaciones con datos educativos
    public function inicialData()
    {
        return $this->hasMany(InicialData::class, 'codigo_modular', 'codigo_modular');
    }

    public function primariaData()
    {
        return $this->hasMany(PrimariaData::class, 'codigo_modular', 'codigo_modular');
    }

    public function secundariaData()
    {
        return $this->hasMany(SecundariaData::class, 'codigo_modular', 'codigo_modular');
    }

    // Obtener todos los datos educativos de la institución
    public function getAllEducationalData()
    {
        return [
            'inicial' => $this->inicialData,
            'primaria' => $this->primariaData,
            'secundaria' => $this->secundariaData
        ];
    }

    // Scopes para filtros
    public function scopeByDepartment($query, $department)
    {
        return $query->where('departamento', $department);
    }

    public function scopeByUgel($query, $ugel)
    {
        return $query->where('ugel', $ugel);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('nivel', 'like', "%$level%");
    }
}