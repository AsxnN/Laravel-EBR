<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\charts\show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generador de Gráficos Personalizados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Panel de configuración -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Configurador de Gráficos
                    </h1>
                    <p class="mt-2 text-gray-500 leading-relaxed">
                        Selecciona los archivos y configura los ejes para generar gráficos personalizados.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <form id="chartConfigForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Selección de archivos -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivos a incluir
                                </label>
                                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                                    @foreach($files as $file)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="file_{{ $file->id }}" 
                                               name="file_ids[]" 
                                               value="{{ $file->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               {{ $files->count() == 1 ? 'checked' : '' }}>
                                        <label for="file_{{ $file->id }}" class="ml-3 text-sm">
                                            <span class="font-medium">{{ $file->original_name }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-xs font-medium
                                                @if($file->document_type == 'inicial') bg-blue-100 text-blue-800 
                                                @elseif($file->document_type == 'primaria') bg-green-100 text-green-800 
                                                @else bg-purple-100 text-purple-800 @endif">
                                                {{ ucfirst($file->document_type) }}
                                            </span>
                                            <div class="text-xs text-gray-500">
                                                {{ $file->total_institutions ?? 0 }} inst. | {{ $file->total_students ?? 0 }} est.
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Eje X -->
                            <div>
                                <label for="x_axis" class="block text-sm font-medium text-gray-700 mb-2">
                                    Eje X (Categorías)
                                </label>
                                <select id="x_axis" name="x_axis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($axisOptions['x_axis'] as $key => $label)
                                        <option value="{{ $key }}" {{ $key == 'ugel' ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Eje Y -->
                            <div>
                                <label for="y_axis" class="block text-sm font-medium text-gray-700 mb-2">
                                    Eje Y (Valores)
                                </label>
                                <select id="y_axis" name="y_axis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($axisOptions['y_axis'] as $key => $label)
                                        <option value="{{ $key }}" {{ $key == 'total_matriculados' ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tipo de gráfico -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Gráfico
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="chart_type" value="column" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Columnas</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="chart_type" value="bar" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Barras</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="chart_type" value="line" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Líneas</span>
                                </label>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-6 flex space-x-4">
                            <button type="button" id="previewBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Vista Previa
                            </button>
                            <button type="button" id="generateBtn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Generar Gráfico
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Área de vista previa -->
            <div id="previewSection" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 hidden">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-medium text-gray-900">Vista Previa de Datos</h2>
                </div>
                <div class="p-6 lg:p-8">
                    <div id="previewContent">
                        <!-- Contenido de vista previa se carga aquí -->
                    </div>
                </div>
            </div>

            <!-- Área del gráfico -->
            <div id="chartSection" class="bg-white overflow-hidden shadow-xl sm:rounded-lg hidden">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 id="chartTitle" class="text-xl font-medium text-gray-900">Gráfico Generado</h2>
                        <div class="flex space-x-2">
                            <button type="button" id="exportPngBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PNG
                            </button>
                            <button type="button" id="exportSvgBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                SVG
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6 lg:p-8">
                    <div id="chartContainer" style="height: 500px;">
                        <!-- Gráfico se renderiza aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let currentChart = null;
        let currentConfig = null;

        document.addEventListener('DOMContentLoaded', function() {
            const previewBtn = document.getElementById('previewBtn');
            const generateBtn = document.getElementById('generateBtn');
            const form = document.getElementById('chartConfigForm');

            previewBtn.addEventListener('click', function() {
                showPreview();
            });

            generateBtn.addEventListener('click', function() {
                generateChart();
            });

            // Auto-preview cuando cambian los selects
            document.getElementById('x_axis').addEventListener('change', function() {
                if (this.value && document.getElementById('y_axis').value) {
                    showPreview();
                }
            });

            document.getElementById('y_axis').addEventListener('change', function() {
                if (this.value && document.getElementById('x_axis').value) {
                    showPreview();
                }
            });
        });

        function showPreview() {
            const formData = new FormData(document.getElementById('chartConfigForm'));
            const fileIds = formData.getAll('file_ids[]');
            
            if (fileIds.length === 0) {
                alert('Selecciona al menos un archivo');
                return;
            }

            if (!formData.get('x_axis') || !formData.get('y_axis')) {
                alert('Selecciona tanto el eje X como el eje Y');
                return;
            }

            fetch('{{ route("charts.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPreview(data);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener vista previa');
            });
        }

        function displayPreview(data) {
            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');
            
            let html = `
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Configuración</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Eje X:</span>
                            <span class="text-gray-900">${data.x_label}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Eje Y:</span>
                            <span class="text-gray-900">${data.y_label}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Archivos:</span>
                            <span class="text-gray-900">${data.total_files}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Vista Previa de Datos (primeros 10)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${data.x_label}</th>
                                    ${data.preview.levels.map(level => 
                                        `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${level}</th>`
                                    ).join('')}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
            `;

            data.preview.categories.forEach((category, index) => {
                html += `<tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">`;
                html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${category}</td>`;
                
                data.preview.series.forEach(series => {
                    html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${series.data[index] || 0}</td>`;
                });
                
                html += '</tr>';
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            previewContent.innerHTML = html;
            previewSection.classList.remove('hidden');
        }

        function generateChart() {
            const formData = new FormData(document.getElementById('chartConfigForm'));
            const fileIds = formData.getAll('file_ids[]');
            
            if (fileIds.length === 0) {
                alert('Selecciona al menos un archivo');
                return;
            }

            if (!formData.get('x_axis') || !formData.get('y_axis')) {
                alert('Selecciona tanto el eje X como el eje Y');
                return;
            }

            generateBtn.disabled = true;
            generateBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generando...';

            fetch('{{ route("charts.generate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderChart(data.data, data.config);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar gráfico');
            })
            .finally(() => {
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>Generar Gráfico';
            });
        }

        function renderChart(chartData, config) {
            currentConfig = config;
            
            const options = {
                series: chartData.series,
                chart: {
                    type: config.chart_type,
                    height: 500,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: config.chart_type === 'bar',
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: config.chart_type === 'line' ? 2 : 0,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: chartData.categories,
                    title: {
                        text: config.x_label
                    }
                },
                yaxis: {
                    title: {
                        text: config.y_label
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " " + config.y_label.toLowerCase();
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                },
                title: {
                    text: `${config.y_label} por ${config.x_label}`,
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold'
                    }
                }
            };

            if (currentChart) {
                currentChart.destroy();
            }

            currentChart = new ApexCharts(document.querySelector("#chartContainer"), options);
            currentChart.render();

            // Mostrar sección del gráfico
            document.getElementById('chartSection').classList.remove('hidden');
            document.getElementById('chartTitle').textContent = `${config.y_label} por ${config.x_label}`;

            // Configurar botones de exportación
            setupExportButtons();
        }

        function setupExportButtons() {
            document.getElementById('exportPngBtn').addEventListener('click', function() {
                if (currentChart) {
                    currentChart.dataURI().then(({imgURI, blob}) => {
                        const link = document.createElement('a');
                        link.href = imgURI;
                        link.download = `grafico_${currentConfig.x_axis}_${currentConfig.y_axis}.png`;
                        link.click();
                    });
                }
            });

            document.getElementById('exportSvgBtn').addEventListener('click', function() {
                if (currentChart) {
                    currentChart.dataURI({type: 'svg'}).then(({imgURI, blob}) => {
                        const link = document.createElement('a');
                        link.href = imgURI;
                        link.download = `grafico_${currentConfig.x_axis}_${currentConfig.y_axis}.svg`;
                        link.click();
                    });
                }
            });
        }
    </script>
</x-app-layout>