<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Plantilla de Gráficos') }}
            </h2>
            <a href="{{ route('chart-templates.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Formulario principal -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Configurar Nueva Plantilla
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Crea una plantilla con múltiples gráficos que podrás reutilizar para diferentes archivos. Usa las primeras 23 columnas de datos educativos.
                    </p>
                </div>

                <form id="templateForm" class="p-6 lg:p-8">
                    @csrf
                    
                    <!-- Información básica de la plantilla -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="template_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Plantilla *
                            </label>
                            <input type="text" 
                                   id="template_name" 
                                   name="name" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Ej: Análisis Completo Matrícula 2024"
                                   required>
                        </div>

                        <div>
                            <label for="education_levels" class="block text-sm font-medium text-gray-700 mb-2">
                                Niveles Educativos *
                            </label>
                            <div class="space-y-2">
                                @foreach($educationLevels as $level => $label)
                                    <label class="inline-flex items-center mr-6">
                                        <input type="checkbox" 
                                               name="education_levels[]" 
                                               value="{{ $level }}"
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                               checked>
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Opcional)
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                  placeholder="Describe el propósito de esta plantilla..."></textarea>
                    </div>

                    <!-- Configurador de gráficos -->
                    <div class="border-t border-gray-200 pt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-medium text-gray-900">Gráficos de la Plantilla</h4>
                            <button type="button" 
                                    onclick="addNewChart()" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agregar Gráfico
                            </button>
                        </div>

                        <!-- Contenedor de gráficos -->
                        <div id="chartsContainer" class="space-y-6">
                            <!-- Los gráficos se agregarán dinámicamente aquí -->
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center justify-between border-t border-gray-200 pt-6 mt-8">
                        <div class="text-sm text-gray-500">
                            <span id="chartCount">0</span> gráficos configurados
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="previewTemplate()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Vista Previa
                            </button>
                            <button type="submit" 
                                    id="saveTemplateBtn"
                                    disabled
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Guardar Plantilla
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de vista previa -->
    <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Vista Previa de la Plantilla</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="previewContent" class="max-h-96 overflow-y-auto">
                <!-- Contenido de vista previa -->
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let chartCounter = 0;
        let availableColumns = @json($availableColumns);
        let chartTypes = @json($chartTypes);
        let educationLevels = @json($educationLevels);

        // Inicializar con un gráfico por defecto
        document.addEventListener('DOMContentLoaded', function() {
            addNewChart();
            updateChartCount();
        });

        // Agregar nuevo gráfico
        function addNewChart() {
            chartCounter++;
            const chartHtml = createChartConfigHTML(chartCounter);
            document.getElementById('chartsContainer').insertAdjacentHTML('beforeend', chartHtml);
            updateChartCount();
            updateSaveButton();
        }

        // Crear HTML para configuración de gráfico
        function createChartConfigHTML(chartId) {
            return `
                <div class="chart-config bg-gray-50 border border-gray-200 rounded-lg p-6" data-chart-id="${chartId}">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-md font-medium text-gray-900">Gráfico ${chartId}</h5>
                        <button type="button" 
                                onclick="removeChart(${chartId})" 
                                class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Nombre del gráfico -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Gráfico *
                            </label>
                            <input type="text" 
                                   name="charts[${chartId}][chart_name]" 
                                   class="chart-input focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Ej: Matrícula por Departamento"
                                   required>
                        </div>

                        <!-- Tipo de gráfico -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Gráfico *
                            </label>
                            <select name="charts[${chartId}][chart_type]" 
                                    class="chart-input focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    onchange="updateChartOptions(${chartId})"
                                    required>
                                <option value="">Seleccionar tipo...</option>
                                ${Object.entries(chartTypes).map(([key, label]) => 
                                    `<option value="${key}">${label}</option>`
                                ).join('')}
                            </select>
                        </div>

                        <!-- Nivel educativo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nivel Educativo *
                            </label>
                            <select name="charts[${chartId}][education_level]" 
                                    class="chart-input focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                                <option value="">Seleccionar nivel...</option>
                                ${Object.entries(educationLevels).map(([key, label]) => 
                                    `<option value="${key}">${label}</option>`
                                ).join('')}
                                <option value="mixed">Comparativo (Todos los niveles)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Selección de columnas -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Columnas a Usar (Máximo 23) *
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                            ${Object.entries(availableColumns).map(([index, columnName]) => `
                                <label class="inline-flex items-start text-xs">
                                    <input type="checkbox" 
                                           name="charts[${chartId}][data_columns][]" 
                                           value="${index}"
                                           class="chart-column-checkbox mt-0.5 mr-2 focus:ring-indigo-500 h-3 w-3 text-indigo-600 border-gray-300 rounded"
                                           onchange="updateSelectedColumns(${chartId})">
                                    <span class="text-gray-700 leading-tight">${index}. ${columnName}</span>
                                </label>
                            `).join('')}
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <span id="selectedColumnsCount${chartId}">0</span> columnas seleccionadas
                        </div>
                    </div>

                    <!-- Opciones específicas del gráfico -->
                    <div id="chartSpecificOptions${chartId}" class="mt-6">
                        <!-- Se llenará dinámicamente según el tipo de gráfico -->
                    </div>
                </div>
            `;
        }

        // Eliminar gráfico
        function removeChart(chartId) {
            if (document.querySelectorAll('.chart-config').length <= 1) {
                alert('Debe haber al menos un gráfico en la plantilla.');
                return;
            }
            
            const chartElement = document.querySelector(`[data-chart-id="${chartId}"]`);
            if (chartElement) {
                chartElement.remove();
                updateChartCount();
                updateSaveButton();
            }
        }

        // Actualizar contador de gráficos
        function updateChartCount() {
            const count = document.querySelectorAll('.chart-config').length;
            document.getElementById('chartCount').textContent = count;
        }

        // Actualizar contador de columnas seleccionadas
        function updateSelectedColumns(chartId) {
            const checkboxes = document.querySelectorAll(`input[name="charts[${chartId}][data_columns][]"]:checked`);
            document.getElementById(`selectedColumnsCount${chartId}`).textContent = checkboxes.length;
            updateSaveButton();
        }

        // Actualizar opciones específicas según tipo de gráfico
        function updateChartOptions(chartId) {
            const chartType = document.querySelector(`select[name="charts[${chartId}][chart_type]"]`).value;
            const optionsContainer = document.getElementById(`chartSpecificOptions${chartId}`);
            
            let optionsHTML = '';
            
            switch(chartType) {
                case 'pie':
                    optionsHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campo para Agrupar</label>
                                <select name="charts[${chartId}][chart_options][group_by]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="3">Departamento</option>
                                    <option value="2">UGEL</option>
                                    <option value="12">Tipo IE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campo para Sumar</label>
                                <select name="charts[${chartId}][chart_options][sum_field]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="13">Total Matriculados</option>
                                    <option value="14">Matrícula Definitiva</option>
                                </select>
                            </div>
                        </div>
                    `;
                    break;
                case 'bar':
                    optionsHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Eje X (Categorías)</label>
                                <select name="charts[${chartId}][chart_options][x_axis]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="3">Departamento</option>
                                    <option value="2">UGEL</option>
                                    <option value="9">Nombre IE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Eje Y (Valores)</label>
                                <select name="charts[${chartId}][chart_options][y_axis]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="13">Total Matriculados</option>
                                    <option value="14">Matrícula Definitiva</option>
                                    <option value="19">Total Grados</option>
                                    <option value="20">Total Secciones</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="charts[${chartId}][chart_options][horizontal]" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Gráfico horizontal</span>
                            </label>
                        </div>
                    `;
                    break;
                case 'line':
                    optionsHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campo Temporal/Secuencial</label>
                                <select name="charts[${chartId}][chart_options][time_field]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="auto">Automático (por archivo)</option>
                                    <option value="3">Departamento</option>
                                    <option value="2">UGEL</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campo de Valores</label>
                                <select name="charts[${chartId}][chart_options][value_field]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="13">Total Matriculados</option>
                                    <option value="14">Matrícula Definitiva</option>
                                </select>
                            </div>
                        </div>
                    `;
                    break;
                case 'table':
                    optionsHTML = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mostrar Totales</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center mr-4">
                                    <input type="checkbox" name="charts[${chartId}][chart_options][show_totals]" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                                    <span class="ml-2 text-sm text-gray-700">Mostrar fila de totales</span>
                                </label>
                                <label class="inline-flex items-center mr-4">
                                    <input type="checkbox" name="charts[${chartId}][chart_options][show_pagination]" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                                    <span class="ml-2 text-sm text-gray-700">Habilitar paginación</span>
                                </label>
                            </div>
                        </div>
                    `;
                    break;
                case 'comparison':
                    optionsHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campo de Comparación</label>
                                <select name="charts[${chartId}][chart_options][compare_field]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="13">Total Matriculados</option>
                                    <option value="14">Matrícula Definitiva</option>
                                    <option value="15">Matrícula en Proceso</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Agrupar por</label>
                                <select name="charts[${chartId}][chart_options][group_comparison]" class="block w-full text-sm border-gray-300 rounded-md">
                                    <option value="3">Departamento</option>
                                    <option value="2">UGEL</option>
                                    <option value="level">Nivel Educativo</option>
                                </select>
                            </div>
                        </div>
                    `;
                    break;
            }
            
            optionsContainer.innerHTML = optionsHTML;
        }

        // Actualizar estado del botón guardar
        function updateSaveButton() {
            const charts = document.querySelectorAll('.chart-config');
            const saveBtn = document.getElementById('saveTemplateBtn');
            const templateName = document.getElementById('template_name').value.trim();
            
            let isValid = templateName.length > 0 && charts.length > 0;
            
            // Verificar que cada gráfico tenga configuración mínima
            charts.forEach(chart => {
                const inputs = chart.querySelectorAll('.chart-input[required]');
                const columns = chart.querySelectorAll('.chart-column-checkbox:checked');
                
                let chartValid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) chartValid = false;
                });
                
                if (columns.length === 0) chartValid = false;
                
                if (!chartValid) isValid = false;
            });
            
            saveBtn.disabled = !isValid;
            if (isValid) {
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Vista previa de la plantilla
        function previewTemplate() {
            const charts = document.querySelectorAll('.chart-config');
            let previewHTML = '<div class="space-y-4">';
            
            charts.forEach((chart, index) => {
                const chartId = chart.dataset.chartId;
                const name = chart.querySelector(`input[name="charts[${chartId}][chart_name]"]`).value;
                const type = chart.querySelector(`select[name="charts[${chartId}][chart_type]"]`).value;
                const level = chart.querySelector(`select[name="charts[${chartId}][education_level]"]`).value;
                const columns = chart.querySelectorAll('.chart-column-checkbox:checked');
                
                previewHTML += `
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">${name || 'Gráfico ' + (index + 1)}</h4>
                        <p class="text-sm text-gray-600">Tipo: ${chartTypes[type] || 'No seleccionado'}</p>
                        <p class="text-sm text-gray-600">Nivel: ${educationLevels[level] || (level === 'mixed' ? 'Comparativo' : 'No seleccionado')}</p>
                        <p class="text-sm text-gray-600">Columnas: ${columns.length} seleccionadas</p>
                    </div>
                `;
            });
            
            previewHTML += '</div>';
            
            document.getElementById('previewContent').innerHTML = previewHTML;
            document.getElementById('previewModal').classList.remove('hidden');
        }

        // Cerrar vista previa
        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        // Manejar envío del formulario
        document.getElementById('templateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            const formData = new FormData(this);
            
            // Validar que hay al menos un gráfico
            const charts = document.querySelectorAll('.chart-config');
            if (charts.length === 0) {
                alert('Debes agregar al menos un gráfico a la plantilla.');
                return;
            }
            
            // Deshabilitar botón mientras se procesa
            const saveBtn = document.getElementById('saveTemplateBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Guardando...';
            
            // Enviar datos
            fetch('{{ route("chart-templates.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir a la vista de la plantilla
                    window.location.href = data.redirect_url;
                } else {
                    alert('Error: ' + data.message);
                    // Rehabilitar botón
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path></svg>Guardar Plantilla';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la plantilla. Por favor, intenta nuevamente.');
                // Rehabilitar botón
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path></svg>Guardar Plantilla';
            });
        });

        // Listeners para validación en tiempo real
        document.addEventListener('input', function(e) {
            if (e.target.matches('#template_name') || e.target.matches('.chart-input')) {
                updateSaveButton();
            }
        });
    </script>
</x-app-layout>