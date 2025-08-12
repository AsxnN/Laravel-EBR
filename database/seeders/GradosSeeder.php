<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradosSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = [
            'inicial' => [
                'primero', 'segundo', 'tercero'
            ],
            'primaria' => [
                'primero', 'segundo', 'tercero',
                'cuarto', 'quinto', 'sexto'
            ],
            'secundaria' => [
                'primero', 'segundo', 'tercero',
                'cuarto', 'quinto'
            ],
        ];

        // Asumiendo que la tabla `niveles` tiene una columna 'nombre' con esos valores
        foreach ($niveles as $nivelNombre => $grados) {
            $nivel = DB::table('niveles')->where('nombre', $nivelNombre)->first();

            if ($nivel) {
                foreach ($grados as $gradoNombre) {
                    DB::table('grados')->insert([
                        'nombre' => $gradoNombre,
                        'nivel_id' => $nivel->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
