<?php
// filepath: c:\laragon\www\EBR\app\Services\EducationalDataFactory.php

namespace App\Services;

use App\Models\InicialData;
use App\Models\PrimariaData;
use App\Models\SecundariaData;
use App\Models\BaseEducationalData;

class EducationalDataFactory
{
    public static function create(string $type): BaseEducationalData
    {
        return match($type) {
            'inicial' => new InicialData(),
            'primaria' => new PrimariaData(),
            'secundaria' => new SecundariaData(),
            default => throw new \InvalidArgumentException("Tipo de educación no válido: $type")
        };
    }

    public static function getModelClass(string $type): string
    {
        return match($type) {
            'inicial' => InicialData::class,
            'primaria' => PrimariaData::class,
            'secundaria' => SecundariaData::class,
            default => throw new \InvalidArgumentException("Tipo de educación no válido: $type")
        };
    }
}