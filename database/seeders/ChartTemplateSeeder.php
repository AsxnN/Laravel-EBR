<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartTemplate;
use App\Models\ChartConfiguration;
use App\Models\User;

class ChartTemplateSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No se encontraron usuarios. Creando usuario de ejemplo...');
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@ebr.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]);
        }

        // Plantilla de un solo nivel - Inicial
        $template1 = ChartTemplate::create([
            'name' => 'Análisis de Matrícula - Inicial',
            'description' => 'Plantilla para analizar datos de matrícula del nivel inicial',
            'template_type' => 'single_level',
            'education_levels' => ['inicial'],
            'status' => 'active',
            'created_by' => $user->id
        ]);

        ChartConfiguration::create([
            'template_id' => $template1->id,
            'chart_name' => 'Matrícula por DRE',
            'title' => 'Distribución de Matrícula por DRE',
            'description' => 'Gráfico de barras mostrando la matrícula total por DRE',
            'chart_type' => 'bar',
            'education_level' => 'inicial',
            'x_axis_field' => 'dre',
            'y_axis_fields' => ['total_matriculados', 'matricula_definitiva'],
            'chart_config' => [
                'education_levels' => ['inicial'],
                'x_axis_field' => 'dre',
                'y_axis_fields' => ['total_matriculados', 'matricula_definitiva'],
                'chart_options' => [
                    'show_legend' => true,
                    'legend_position' => 'top',
                    'colors' => ['#1f77b4', '#ff7f0e'],
                    'responsive' => true,
                    'maintain_aspect_ratio' => false
                ]
            ],
            'order_position' => 1
        ]);

        // Plantilla multi-nivel
        $template2 = ChartTemplate::create([
            'name' => 'Comparativo Multi-nivel',
            'description' => 'Plantilla para comparar datos entre todos los niveles educativos',
            'template_type' => 'multi_level',
            'education_levels' => ['inicial', 'primaria', 'secundaria'],
            'status' => 'active',
            'created_by' => $user->id
        ]);

        ChartConfiguration::create([
            'template_id' => $template2->id,
            'chart_name' => 'Comparativo por Departamento',
            'title' => 'Matrícula Total por Departamento - Todos los Niveles',
            'description' => 'Comparación de matrícula entre niveles educativos por departamento',
            'chart_type' => 'bar',
            'education_level' => 'multi_level',
            'x_axis_field' => 'departamento',
            'y_axis_fields' => ['total_matriculados'],
            'chart_config' => [
                'education_levels' => ['inicial', 'primaria', 'secundaria'],
                'x_axis_field' => 'departamento',
                'y_axis_fields' => ['total_matriculados'],
                'chart_options' => [
                    'show_legend' => true,
                    'legend_position' => 'top',
                    'colors' => ['#1f77b4', '#ff7f0e', '#2ca02c'],
                    'responsive' => true,
                    'maintain_aspect_ratio' => false
                ]
            ],
            'order_position' => 1
        ]);

        $this->command->info('Plantillas de gráficos creadas exitosamente.');
    }
}