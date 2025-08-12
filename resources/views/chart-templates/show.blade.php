<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $template->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Plantilla con {{ $template->charts->count() }} gráficos • Creada el {{ $template->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('chart-templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
                <button id="downloadReportBtn" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 disabled:opacity-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar Reporte
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Información de la plantilla -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Descripción</h3>
                            <p class="text-sm text-gray-600">
                                {{ $template->description ?: 'Sin descripción disponible.' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Niveles Educativos</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($template->education_levels as $level)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @switch($level)
                                            @case('inicial') bg-blue-100 text-blue-800 @break
                                            @case('primaria') bg-green-100 text-green-800 @break
                                            @case('secundaria') bg-purple-100 text-purple-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst($level) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Estadísticas</h3>
                            <div class="text-sm text-gray-600">
                                <p>• {{ $template->charts->count() }} gráficos configurados</p>
                                <p>• Creado por {{ $template->creator->name ?? 'Usuario' }}</p>
                                <p>• Estado: <span class="font-medium {{ $template->status === 'active' ? 'text-green-600' : 'text-red-600' }}">{{ $template->status === 'active' ? 'Activa' : 'Inactiva' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de archivos por gráfico -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Configurar Archivos para cada Gráfico
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Selecciona los archivos que deseas usar para cada gráfico. Puedes usar diferentes archivos para diferentes gráficos según tu análisis.
                    </p>
                </div>

                <form id="chartsConfigForm" class="p-6 lg:p-8">
                    @csrf
                    
                    @foreach($template->charts as $index => $chart)
                        <div class="chart-files-config mb-8 pb-8 {{ !$loop->last ? 'border-b border-gray-200' : '' }}" data-chart-id="{{ $chart->id }}">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">
                                        {{ $chart->chart_name }}
                                    </h4>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            {{ $chart->chart_type_text }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            {{ $chart->education_level_text }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            {{ count($chart->data_columns) }} columnas
                                        </span>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="selectAllFiles({{ $chart->id }})" 
                                        class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Seleccionar todos
                                </button>
                            </div>

                            <!-- Selección de archivos según el nivel educativo del gráfico -->
                            @php
                                $relevantFiles = collect([]);
                                if ($chart->education_level === 'mixed') {
                                    $relevantFiles = $availableFiles['inicial']
                                        ->merge($availableFiles['primaria'])
                                        ->merge($availableFiles['secundaria']);
                                } else {
                                    $relevantFiles = $availableFiles[$chart->education_level] ?? collect([]);
                                }
                            @endphp

                            @if($relevantFiles->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($relevantFiles as $file)
                                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" 
                                                name="chart_files[{{ $chart->id }}][]" 
                                                value="{{ $file->id }}"
                                                class="chart-file-checkbox mt-1 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                onchange="updateSelectedFiles({{ $chart->id }})">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $file->original_name }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                        @switch($file->document_type)
                                                            @case('inicial') bg-blue-100 text-blue-800 @break
                                                            @case('primaria') bg-green-100 text-green-800 @break
                                                            @case('secundaria') bg-purple-100 text-purple-800 @break
                                                            @default bg-gray-100 text-gray-800
                                                        @endswitch">
                                                        {{ ucfirst($file->document_type ?? 'N/A') }}
                                                    </span>
                                                    <span class="ml-2">{{ $file->uploaded_at->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ number_format($file->total_institutions ?? 0) }} IE • 
                                                    {{ number_format($file->total_students ?? 0) }} estudiantes
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3 text-sm text-gray-500">
                                    <span id="selectedFilesCount{{ $chart->id }}">0</span> archivos seleccionados
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">No hay archivos disponibles para este tipo de gráfico.</p>
                                    <p class="text-xs">Sube archivos de {{ $chart->education_level_text }} primero.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Botón para generar gráficos -->
                    <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                        <div class="text-sm text-gray-500">
                            <span id="totalSelectedFiles">0</span> archivos seleccionados en total
                        </div>
                        <button type="submit" 
                                id="generateChartsBtn"
                                disabled
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span id="generateBtnText">Generar Gráficos</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Área de resultados de gráficos -->
            <div id="chartsResultsArea" class="space-y-8" style="display: none;">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Gráficos Generados
                            </h3>
                            <div class="flex space-x-2">
                                <button onclick="exportAllChartsAsPDF()" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </button>
                                <button onclick="exportAllChartsAsImages()" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Imágenes
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="chartsContainer" class="p-6 lg:p-8">
                        <!-- Los gráficos se mostrarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de carga -->
    <div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Generando Gráficos</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="loadingMessage">
                        Procesando archivos y creando visualizaciones...
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let generatedCharts = [];
        let chartInstances = {};

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            updateTotalSelectedFiles();
            updateGenerateButton();
        });

        // Seleccionar todos los archivos para un gráfico
        function selectAllFiles(chartId) {
            const checkboxes = document.querySelectorAll(`input[name="chart_files[${chartId}][]"]`);
            const allSelected = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allSelected;
            });
            
            updateSelectedFiles(chartId);
        }

        // Actualizar contador de archivos seleccionados para un gráfico
        function updateSelectedFiles(chartId) {
            const checkboxes = document.querySelectorAll(`input[name="chart_files[${chartId}][]"]:checked`);
            document.getElementById(`selectedFilesCount${chartId}`).textContent = checkboxes.length;
            
            updateTotalSelectedFiles();
            updateGenerateButton();
        }

        // Actualizar total de archivos seleccionados
        function updateTotalSelectedFiles() {
            const allCheckboxes = document.querySelectorAll('.chart-file-checkbox:checked');
            document.getElementById('totalSelectedFiles').textContent = allCheckboxes.length;
        }

        // Actualizar estado del botón generar
        function updateGenerateButton() {
            const chartConfigs = document.querySelectorAll('.chart-files-config');
            const generateBtn = document.getElementById('generateChartsBtn');
            
            let hasSelection = false;
            chartConfigs.forEach(config => {
                const checkboxes = config.querySelectorAll('.chart-file-checkbox:checked');
                if (checkboxes.length > 0) {
                    hasSelection = true;
                }
            });
            
            generateBtn.disabled = !hasSelection;
            if (hasSelection) {
                generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Manejar generación de gráficos
        document.getElementById('chartsConfigForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar modal de carga
            document.getElementById('loadingModal').classList.remove('hidden');
            
            // Deshabilitar botón
            const generateBtn = document.getElementById('generateChartsBtn');
            const generateBtnText = document.getElementById('generateBtnText');
            generateBtn.disabled = true;
            generateBtnText.textContent = 'Generando...';
            
            // Recopilar datos del formulario
            const formData = new FormData(this);
            
            // Enviar petición
            fetch(`{{ route('chart-templates.generate-charts', $template->id) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingModal').classList.add('hidden');
                
                if (data.success) {
                    generatedCharts = data.charts;
                    displayCharts(data.charts);
                    
                    // Mostrar área de resultados
                    document.getElementById('chartsResultsArea').style.display = 'block';
                    
                    // Scroll hacia los resultados
                    document.getElementById('chartsResultsArea').scrollIntoView({ 
                        behavior: 'smooth' 
                    });
                } else {
                    alert('Error: ' + data.message);
                }
                
                // Rehabilitar botón
                generateBtn.disabled = false;
                generateBtnText.textContent = 'Generar Gráficos';
            })
            .catch(error => {
                document.getElementById('loadingModal').classList.add('hidden');
                console.error('Error:', error);
                alert('Error al generar gráficos. Por favor, intenta nuevamente.');
                
                // Rehabilitar botón
                generateBtn.disabled = false;
                generateBtnText.textContent = 'Generar Gráficos';
            });
        });

        // Mostrar gráficos generados
        function displayCharts(charts) {
            const container = document.getElementById('chartsContainer');
            container.innerHTML = '';

            charts.forEach((chartData, index) => {
                const chartElement = createChartElement(chartData, index);
                container.appendChild(chartElement);
                
                // Renderizar el gráfico específico
                setTimeout(() => {
                    renderChart(chartData, index);
                }, 100 * index); // Pequeño delay entre gráficos
            });
        }

        // Crear elemento HTML para un gráfico
        function createChartElement(chartData, index) {
            const div = document.createElement('div');
            div.className = 'chart-item bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6';
            
            const config = chartData.config;
            const filesInfo = chartData.files.map(f => f.original_name).join(', ');
            
            div.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">${config.chart_name}</h4>
                        <p class="text-sm text-gray-600">${config.chart_type_text} • ${config.education_level_text}</p>
                        <p class="text-xs text-gray-500 mt-1">Archivos: ${filesInfo}</p>
                    </div>
                    <button onclick="downloadChart(${index})" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Descargar
                    </button>
                </div>
                <div class="chart-content">
                    ${config.chart_type === 'table' ? 
                        `<div id="chart-${index}" class="overflow-x-auto"></div>` : 
                        `<div class="relative" style="height: 400px;">
                            <canvas id="chart-${index}"></canvas>
                         </div>`
                    }
                </div>
            `;
            
            return div;
        }

        // Renderizar un gráfico específico
        function renderChart(chartData, index) {
            const config = chartData.config;
            const data = chartData.data;
            
            if (config.chart_type === 'table') {
                renderTable(data, index);
            } else {
                renderCanvasChart(data, index);
            }
        }

        // Renderizar tabla
        function renderTable(data, index) {
            const container = document.getElementById(`chart-${index}`);
            
            let tableHTML = `
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            ${data.headers.map(header => 
                                `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${header}</th>`
                            ).join('')}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${data.rows.map(row => 
                            `<tr>${row.map(cell => 
                                `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cell}</td>`
                            ).join('')}</tr>`
                        ).join('')}
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
        }

        // Renderizar gráfico en canvas
        function renderCanvasChart(data, index) {
            const ctx = document.getElementById(`chart-${index}`).getContext('2d');
            
            // Destruir gráfico anterior si existe
            if (chartInstances[index]) {
                chartInstances[index].destroy();
            }
            
            const chartConfig = {
                type: data.type,
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            };

            // Configuraciones específicas por tipo de gráfico
            if (data.type === 'pie') {
                chartConfig.options.plugins.legend.position = 'right';
            } else if (data.type === 'bar') {
                chartConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                };
            } else if (data.type === 'line') {
                chartConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                };
            }
            
            chartInstances[index] = new Chart(ctx, chartConfig);
        }

        // Descargar gráfico individual
        function downloadChart(index) {
            const config = generatedCharts[index].config;
            
            if (config.chart_type === 'table') {
                downloadTableAsCSV(index, config.chart_name);
            } else {
                downloadCanvasAsImage(index, config.chart_name);
            }
        }

        // Descargar tabla como CSV
        function downloadTableAsCSV(index, filename) {
            const data = generatedCharts[index].data;
            
            let csvContent = '';
            csvContent += data.headers.join(',') + '\n';
            data.rows.forEach(row => {
                csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
            });
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Descargar canvas como imagen
        function downloadCanvasAsImage(index, filename) {
            const canvas = document.getElementById(`chart-${index}`);
            const url = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}.png`;
            a.click();
        }

        // Exportar todos los gráficos como imágenes
        function exportAllChartsAsImages() {
            generatedCharts.forEach((chartData, index) => {
                setTimeout(() => {
                    downloadChart(index);
                }, 500 * index);
            });
        }

        // Exportar todos los gráficos como PDF (por implementar)
        function exportAllChartsAsPDF() {
            alert('Función de exportar PDF estará disponible próximamente.');
        }

        // Listeners para checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.matches('.chart-file-checkbox')) {
                const chartId = e.target.name.match(/\[(\d+)\]/)[1];
                updateSelectedFiles(parseInt(chartId));
            }
        });

        // Descargar reporte completo
        document.getElementById('downloadReportBtn').addEventListener('click', function() {
            fetch(`{{ route('chart-templates.download-report', $template->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_id: {{ $template->id }},
                    charts_data: generatedCharts
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reporte generado exitosamente.');
                } else {
                    alert('Error al generar reporte: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar reporte.');
            });
        });
    </script>
</x-app-layout>