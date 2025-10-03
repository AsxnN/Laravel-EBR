<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\reports\show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $report->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Creado por {{ $report->creator->name ?? 'Usuario desconocido' }} el {{ $report->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($report->status === 'published') bg-green-100 text-green-800
                    @elseif($report->status === 'sent') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($report->status) }}
                </span>
                
                @if($report->status === 'draft')
                    <a href="{{ route('reports.edit', $report->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Agregar Gráficos
                    </a>
                @endif
                
                <a href="{{ route('reports.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Información del reporte -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Descripción</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $report->description ?: 'Sin descripción' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total de Gráficos</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $report->charts->count() }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Última Actualización</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $report->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos del reporte -->
            @if($report->charts->count() > 0)
                <div class="space-y-6">
                    @foreach($report->charts as $chart)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                            <div class="border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $chart->chart_title }}</h3>
                                        <p class="text-sm text-gray-600">
                                            Tipo: {{ ucfirst($chart->chart_config['chart_type'] ?? 'No definido') }}
                                            @if($chart->template_id)
                                                • Plantilla ID: {{ $chart->template_id }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $chart->file_count }} archivo(s)
                                        </span>
                                        <button onclick="exportChart({{ $chart->id }}, 'png')" 
                                                class="text-gray-600 hover:text-gray-900" title="Exportar PNG">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <!-- Contenedor del gráfico -->
                                <div id="chart_{{ $chart->id }}" class="w-full h-96"></div>
                                
                                <!-- Información de archivos usados -->
                                @if($chart->file_count > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Archivos utilizados:</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                            @foreach($chart->files as $file)
                                                <div class="flex items-center space-x-2 text-xs bg-gray-50 rounded px-2 py-1">
                                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                                    <span class="truncate">{{ $file->original_name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay gráficos en este reporte</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza agregando gráficos a tu reporte.</p>
                        @if($report->status === 'draft')
                            <div class="mt-6">
                                <a href="{{ route('reports.edit', $report->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar Gráfico
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Acciones del reporte -->
            @if($report->charts->count() > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones del Reporte</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($report->status === 'draft')
                                <button onclick="publishReport({{ $report->id }})" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Publicar Reporte
                                </button>
                            @endif
                            
                            <button onclick="exportReport({{ $report->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Exportar JSON
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Renderizar todos los gráficos
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($report->charts as $chart)
                @if($chart->chart_data && $chart->chart_config)
                    renderChart({{ $chart->id }}, {!! json_encode($chart->chart_data) !!}, {!! json_encode($chart->chart_config) !!});
                @else
                    console.warn('Chart {{ $chart->id }} missing data or config');
                    document.querySelector("#chart_{{ $chart->id }}").innerHTML = 
                        '<div class="flex items-center justify-center h-full text-gray-500">' +
                        '<p>No hay datos disponibles para este gráfico</p></div>';
                @endif
            @endforeach
        });

        function renderChart(chartId, chartData, config) {
            try {
                console.log('Rendering chart:', chartId, chartData, config);
                
                if (!chartData || !chartData.series || chartData.series.length === 0) {
                    document.querySelector("#chart_" + chartId).innerHTML = 
                        '<div class="flex items-center justify-center h-full text-gray-500">' +
                        '<p>No hay datos para mostrar en este gráfico</p></div>';
                    return;
                }
                
                const options = createChartOptions(chartData, config);
                const chart = new ApexCharts(document.querySelector("#chart_" + chartId), options);
                chart.render();
            } catch (error) {
                console.error('Error rendering chart ' + chartId + ':', error);
                document.querySelector("#chart_" + chartId).innerHTML = 
                    '<div class="flex items-center justify-center h-full text-red-500">' +
                    '<p>Error al cargar el gráfico</p></div>';
            }
        }

        function createChartOptions(chartData, config) {
            if (!chartData || !config) {
                console.error('Missing chart data or config');
                return getDefaultOptions();
            }

            const chartType = config.chart_type || 'column';
            
            switch(chartType) {
                case 'pie':
                    return createPieChart(chartData, config);
                case 'bar':
                    return createBarChart(chartData, config);
                case 'column':
                    return createColumnChart(chartData, config);
                case 'line':
                    return createLineChart(chartData, config);
                default:
                    return createColumnChart(chartData, config);
            }
        }

        function createPieChart(chartData, config) {
            // Para gráficos de pie, sumar todos los valores de cada serie
            const pieData = [];
            if (chartData.series && chartData.series.length > 0) {
                chartData.series.forEach(series => {
                    const total = Array.isArray(series.data) ? series.data.reduce((sum, val) => sum + val, 0) : 0;
                    if (total > 0) {
                        pieData.push(total);
                    }
                });
            }

            return {
                series: pieData,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: chartData.series ? chartData.series.map(s => s.name) : [],
                title: {
                    text: config.title || chartData.title || '',
                    align: 'center'
                },
                legend: {
                    position: 'bottom'
                }
            };
        }

        function createBarChart(chartData, config) {
            return {
                series: chartData.series || [],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: true
                    }
                },
                xaxis: {
                    categories: chartData.categories || []
                },
                title: {
                    text: config.title || chartData.title || '',
                    align: 'center'
                }
            };
        }

        function createColumnChart(chartData, config) {
            return {
                series: chartData.series || [],
                chart: {
                    type: 'bar',
                    height: 350
                },
                xaxis: {
                    categories: chartData.categories || []
                },
                title: {
                    text: config.title || chartData.title || '',
                    align: 'center'
                }
            };
        }

        function createLineChart(chartData, config) {
            return {
                series: chartData.series || [],
                chart: {
                    type: 'line',
                    height: 350
                },
                xaxis: {
                    categories: chartData.categories || []
                },
                title: {
                    text: config.title || chartData.title || '',
                    align: 'center'
                }
            };
        }

        function getDefaultOptions() {
            return {
                series: [],
                chart: {
                    type: 'bar',
                    height: 350
                },
                xaxis: {
                    categories: []
                },
                title: {
                    text: 'Gráfico sin datos',
                    align: 'center'
                }
            };
        }

        function publishReport(reportId) {
            if (confirm('¿Estás seguro de que quieres publicar este reporte?')) {
                fetch(`/reports/${reportId}/publish`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al publicar reporte');
                });
            }
        }

        function exportReport(reportId) {
            window.location.href = `/reports/${reportId}/export/json`;
        }

        function exportChart(chartId, format) {
            console.log(`Exportar gráfico ${chartId} en formato ${format}`);
        }
    </script>
</x-app-layout>