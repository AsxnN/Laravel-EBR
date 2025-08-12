<!-- filepath: resources/views/comparisons/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $comparison->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $comparison->comparison_type_text }} • 
                    Creado {{ $comparison->created_at->diffForHumans() }}
                    @if($comparison->selected_period)
                        • Período: {{ $comparison->selected_period_text }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                @if($comparison->status === 'ready')
                    <button onclick="exportToPDF()" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar PDF
                    </button>
                @endif
                <a href="{{ route('comparisons.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($comparison->status === 'processing')
                <!-- Estado de procesamiento -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="text-center py-12">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                            <svg class="animate-spin h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Procesando Comparativa</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Tu comparativa se está generando. Esto puede tomar unos minutos.
                        </p>
                        <div class="mt-6">
                            <button onclick="window.location.reload()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                Actualizar Estado
                            </button>
                        </div>
                    </div>
                </div>

            @elseif($comparison->status === 'error')
                <!-- Estado de error -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="text-center py-12">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Error al Procesar</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Hubo un error al generar la comparativa. Por favor, intenta nuevamente.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('comparisons.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Crear Nueva Comparativa
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($comparison->status === 'ready')
                <!-- Resumen de la comparativa -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Resumen de la Comparativa
                        </h3>
                    </div>
                    <div class="p-6 lg:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-indigo-600">
                                    {{ $comparison->files_count }}
                                </div>
                                <div class="text-sm text-gray-500">Archivos Analizados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">
                                    {{ number_format($comparison->total_institutions ?? 0) }}
                                </div>
                                <div class="text-sm text-gray-500">Instituciones</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">
                                    {{ number_format($comparison->total_students ?? 0) }}
                                </div>
                                <div class="text-sm text-gray-500">Estudiantes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">
                                    {{ count($comparison->charts_data ?? []) }}
                                </div>
                                <div class="text-sm text-gray-500">Gráficos Generados</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archivos incluidos -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Archivos Incluidos en la Comparativa
                        </h3>
                    </div>
                    <div class="p-6 lg:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($comparison->files as $file)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $file->document_type === 'inicial' ? 'blue' : ($file->document_type === 'primaria' ? 'green' : 'purple') }}-100 text-{{ $file->document_type === 'inicial' ? 'blue' : ($file->document_type === 'primaria' ? 'green' : 'purple') }}-800">
                                            {{ ucfirst($file->document_type) }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 truncate mb-1">
                                        {{ $file->original_name }}
                                    </h4>
                                    <div class="text-xs text-gray-500">
                                        {{ $file->uploaded_at->format('d/m/Y') }} • 
                                        {{ number_format($file->total_institutions) }} IE • 
                                        {{ number_format($file->total_students) }} estudiantes
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Gráficos generados -->
                @if($comparison->charts_data && count($comparison->charts_data) > 0)
                    <div class="space-y-8">
                        @foreach($comparison->charts_data as $index => $chartData)
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $chartData['config']['title'] ?? 'Gráfico ' . ($index + 1) }}
                                        </h3>
                                        <button onclick="downloadChart({{ $index }})" 
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Descargar
                                        </button>
                                    </div>
                                    @if(isset($chartData['config']['description']))
                                        <p class="text-sm text-gray-600 mt-2">
                                            {{ $chartData['config']['description'] }}
                                        </p>
                                    @endif
                                </div>
                                <div class="p-6 lg:p-8">
                                    @if($chartData['type'] === 'table')
                                        <!-- Renderizar tabla -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        @if(isset($chartData['data']['headers']))
                                                            @foreach($chartData['data']['headers'] as $header)
                                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                    {{ $header }}
                                                                </th>
                                                            @endforeach
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @if(isset($chartData['data']['rows']))
                                                        @foreach($chartData['data']['rows'] as $row)
                                                            <tr>
                                                                @foreach($row as $cell)
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                        {{ $cell }}
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <!-- Canvas para gráfico -->
                                        <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                                            <canvas id="chart_{{ $index }}" 
                                                    data-chart="{{ json_encode($chartData['data']) }}"
                                                    data-type="{{ $chartData['type'] }}">
                                            </canvas>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            @endif
        </div>
    </div>

    <!-- Scripts para gráficos -->
    @if($comparison->status === 'ready' && $comparison->charts_data)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Renderizar todos los gráficos
                const canvases = document.querySelectorAll('canvas[id^="chart_"]');
                
                canvases.forEach(canvas => {
                    const chartData = JSON.parse(canvas.dataset.chart);
                    const chartType = canvas.dataset.type;
                    
                    new Chart(canvas, {
                        type: chartType,
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: chartType !== 'pie' ? {
                                y: {
                                    beginAtZero: true
                                }
                            } : {}
                        }
                    });
                });
            });

            // Descargar gráfico individual
            function downloadChart(index) {
                const canvas = document.getElementById(`chart_${index}`);
                if (canvas) {
                    const link = document.createElement('a');
                    link.download = `grafico_${index + 1}.png`;
                    link.href = canvas.toDataURL();
                    link.click();
                }
            }

            // Exportar todo a PDF
            function exportToPDF() {
                window.print();
            }
        </script>

        <style>
            @media print {
                .chart-container {
                    page-break-inside: avoid;
                }
                
                nav, header, .no-print {
                    display: none !important;
                }
            }
        </style>
    @endif
</x-app-layout>