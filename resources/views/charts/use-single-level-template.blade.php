<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $template->name }} - Un Solo Nivel
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Compara múltiples archivos del mismo nivel educativo
                </p>
            </div>
            <a href="{{ route('charts.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Plantillas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <!-- Información de la plantilla -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                            @if($template->chart_type == 'bar' || $template->chart_type == 'column')
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            @elseif($template->chart_type == 'line')
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                </svg>
                            @elseif($template->chart_type == 'table')
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H5a1 1 0 01-1-1z"></path>
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-blue-900 mb-2">{{ $template->name }}</h3>
                        <p class="text-blue-700 mb-4">{{ $template->description }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-blue-900">Tipo:</span>
                                <span class="text-blue-700">{{ $template->chart_type_label }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-900">Eje X:</span>
                                <span class="text-blue-700">{{ $template->x_axis_label }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-900">Eje Y:</span>
                                <span class="text-blue-700">{{ $template->y_axis_label }}</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-blue-100 rounded">
                            <p class="text-sm text-blue-800"><strong>Propósito:</strong> {{ $template->purpose }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de nivel educativo -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    1. Selecciona el Nivel Educativo
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Elige el nivel educativo para filtrar los archivos disponibles. Solo se mostrarán archivos de este nivel.
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="relative">
                        <input type="radio" name="level_selection" value="Inicial" class="sr-only peer" onchange="updateFilesByLevel()">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">🎨</div>
                                <div class="font-medium text-gray-900">Inicial</div>
                                <div class="text-xs text-gray-500 mt-1" id="inicial-count">0 archivos</div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="level_selection" value="Primaria" class="sr-only peer" onchange="updateFilesByLevel()">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">📚</div>
                                <div class="font-medium text-gray-900">Primaria</div>
                                <div class="text-xs text-gray-500 mt-1" id="primaria-count">0 archivos</div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="level_selection" value="Secundaria" class="sr-only peer" onchange="updateFilesByLevel()">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">🎓</div>
                                <div class="font-medium text-gray-900">Secundaria</div>
                                <div class="text-xs text-gray-500 mt-1" id="secundaria-count">0 archivos</div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="level_selection" value="Global" class="sr-only peer" onchange="updateFilesByLevel()">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">🌍</div>
                                <div class="font-medium text-gray-900">Global</div>
                                <div class="text-xs text-gray-500 mt-1" id="global-count">0 archivos</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Selección de archivos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    2. Selecciona los Archivos a Comparar
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Elige múltiples archivos del mismo nivel para crear la comparación.
                </p>
                
                <!-- Mensaje cuando no hay nivel seleccionado -->
                <div id="no-level-message" class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <p>Selecciona un nivel educativo para ver los archivos disponibles</p>
                </div>

                <!-- Lista de archivos filtrados -->
                <div id="files-container" class="hidden">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <button type="button" onclick="selectAllFiles()" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Seleccionar todos
                            </button>
                            <span class="text-gray-300 mx-2">|</span>
                            <button type="button" onclick="clearAllFiles()" 
                                    class="text-sm text-gray-600 hover:text-gray-800">
                                Deseleccionar todos
                            </button>
                        </div>
                        <div id="selection-summary" class="text-sm text-gray-600">
                            0 archivos seleccionados
                        </div>
                    </div>
                    <div id="files-list" class="space-y-3">
                        <!-- Los archivos se cargarán dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- Vista previa de datos -->
            <div id="previewSection" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8 hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Vista Previa de Datos</h3>
                    <button type="button" 
                            id="refreshPreviewBtn"
                            onclick="showPreview()"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Actualizar
                    </button>
                </div>
                <div id="previewContent" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <!-- Contenido de vista previa -->
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">3. Generar Gráfico</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Crea la visualización con los archivos seleccionados
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="previewBtn" onclick="showPreview()" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50" 
                                disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Vista Previa
                        </button>
                        <button id="generateBtn" onclick="generateChart()" 
                                class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50" 
                                disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Generar Gráfico
                        </button>
                    </div>
                </div>
            </div>

            <!-- Área de resultado -->
            <div id="chartResult" class="hidden mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 id="chartTitle" class="text-lg font-medium text-gray-900">Resultado</h3>
                            <p id="chartSubtitle" class="text-sm text-gray-500 mt-1"></p>
                        </div>
                        <div class="flex space-x-2">
                            <button id="exportPngBtn" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>PNG</span>
                            </button>
                            <button id="exportSvgBtn" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>CSV</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div id="chartContainer" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let currentChart = null;
        let currentConfig = null;
        const templateId = {{ $template->id }};
        const allFiles = @json($files);

        document.addEventListener('DOMContentLoaded', function() {
            updateFileCounts();
        });

        function updateFileCounts() {
            const levels = ['Inicial', 'Primaria', 'Secundaria', 'Global'];
            
            levels.forEach(level => {
                const count = allFiles.filter(file => 
                    detectFileLevel(file).toLowerCase() === level.toLowerCase()
                ).length;
                
                const element = document.getElementById(level.toLowerCase() + '-count');
                if (element) {
                    element.textContent = `${count} archivo${count !== 1 ? 's' : ''}`;
                }
            });
        }

        function detectFileLevel(file) {
            const name = file.original_name.toLowerCase();
            const type = file.document_type.toLowerCase();
            
            if (name.includes('inicial') || type.includes('inicial')) return 'Inicial';
            if (name.includes('primaria') || type.includes('primaria')) return 'Primaria';
            if (name.includes('secundaria') || type.includes('secundaria')) return 'Secundaria';
            if (name.includes('global') || type.includes('global')) return 'Global';
            
            return file.document_type;
        }

        function updateFilesByLevel() {
            const selectedLevel = document.querySelector('input[name="level_selection"]:checked')?.value;
            
            if (!selectedLevel) {
                document.getElementById('no-level-message').classList.remove('hidden');
                document.getElementById('files-container').classList.add('hidden');
                updateActionButtons();
                return;
            }

            document.getElementById('no-level-message').classList.add('hidden');
            document.getElementById('files-container').classList.remove('hidden');

            const filteredFiles = allFiles.filter(file => 
                detectFileLevel(file).toLowerCase() === selectedLevel.toLowerCase()
            );

            renderFilesList(filteredFiles);
            updateActionButtons();
        }

        function renderFilesList(files) {
            const container = document.getElementById('files-list');
            
            if (files.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No hay archivos disponibles para este nivel</p>
                        <p class="text-sm mt-2">Sube archivos de este nivel para poder crear comparaciones</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = files.map(file => `
                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                    <input type="checkbox" 
                           id="file_${file.id}" 
                           value="${file.id}" 
                           onchange="updateSelectionSummary()"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="file_${file.id}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    ${file.original_name}
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span>📍 ${file.total_institutions || 0} instituciones</span>
                                    <span class="mx-2">•</span>
                                    <span>👥 ${file.total_students || 0} estudiantes</span>
                                    <span class="mx-2">•</span>
                                    <span>📅 ${new Date(file.uploaded_at).toLocaleDateString()}</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    ${file.document_type} • Por ${file.user.name}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Procesado
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function selectAllFiles() {
            const checkboxes = document.querySelectorAll('#files-list input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = true);
            updateSelectionSummary();
        }

        function clearAllFiles() {
            const checkboxes = document.querySelectorAll('#files-list input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            updateSelectionSummary();
        }

        function updateSelectionSummary() {
            const selectedCount = document.querySelectorAll('#files-list input[type="checkbox"]:checked').length;
            document.getElementById('selection-summary').textContent = 
                `${selectedCount} archivo${selectedCount !== 1 ? 's' : ''} seleccionado${selectedCount !== 1 ? 's' : ''}`;
            
            updateActionButtons();
        }

        function updateActionButtons() {
            const selectedLevel = document.querySelector('input[name="level_selection"]:checked')?.value;
            const selectedFiles = document.querySelectorAll('#files-list input[type="checkbox"]:checked').length;
            
            const canGenerate = selectedLevel && selectedFiles >= 1;
            
            document.getElementById('previewBtn').disabled = !canGenerate;
            document.getElementById('generateBtn').disabled = !canGenerate;
        }

        function showPreview() {
            const selectedLevel = document.querySelector('input[name="level_selection"]:checked')?.value;
            const selectedFileIds = Array.from(document.querySelectorAll('#files-list input[type="checkbox"]:checked'))
                .map(cb => parseInt(cb.value));

            if (!selectedLevel || selectedFileIds.length === 0) {
                alert('Selecciona un nivel y al menos un archivo');
                return;
            }

            // Mostrar loading
            const previewBtn = document.getElementById('previewBtn');
            previewBtn.disabled = true;
            previewBtn.innerHTML = `
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cargando...
            `;

            fetch(`/charts/generate/single-level/${templateId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    file_ids: selectedFileIds,
                    selected_level: selectedLevel
                })
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
            })
            .finally(() => {
                previewBtn.disabled = false;
                previewBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Vista Previa
                `;
                updateActionButtons();
            });
        }

        function displayPreview(data) {
            const previewContent = document.getElementById('previewContent');
            
            // Limitar a los primeros 10 elementos para la vista previa
            const limitedCategories = data.data.categories.slice(0, 10);
            const limitedSeries = data.data.series.map(series => ({
                ...series,
                data: series.data.slice(0, 10)
            }));

            let html = `
                <div class="p-4">
                    <div class="mb-4 text-sm text-gray-600">
                        Mostrando primeros ${limitedCategories.length} de ${data.data.categories.length} elementos para ${data.config.selected_level}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${data.config.x_label}</th>
                                    ${limitedSeries.map(series => 
                                        `<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${series.name}</th>`
                                    ).join('')}
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
            `;

            limitedCategories.forEach((category, index) => {
                const rowTotal = limitedSeries.reduce((sum, series) => sum + (series.data[index] || 0), 0);
                html += `<tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">`;
                html += `<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${category}</td>`;
                
                limitedSeries.forEach(series => {
                    const value = series.data[index] || 0;
                    html += `<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${value.toLocaleString()}</td>`;
                });
                
                html += `<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${rowTotal.toLocaleString()}</td>`;
                html += '</tr>';
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            previewContent.innerHTML = html;
            document.getElementById('previewSection').classList.remove('hidden');
        }

        function generateChart() {
            const selectedLevel = document.querySelector('input[name="level_selection"]:checked')?.value;
            const selectedFileIds = Array.from(document.querySelectorAll('#files-list input[type="checkbox"]:checked'))
                .map(cb => parseInt(cb.value));

            if (!selectedLevel || selectedFileIds.length === 0) {
                alert('Selecciona un nivel y al menos un archivo');
                return;
            }

            // Mostrar loading
            const generateBtn = document.getElementById('generateBtn');
            generateBtn.disabled = true;
            generateBtn.innerHTML = `
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generando...
            `;

            fetch(`/charts/generate/single-level/${templateId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    file_ids: selectedFileIds,
                    selected_level: selectedLevel
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayChart(data.data, data.config, data.template);
                } else {
                    alert('Error al generar gráfico: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar gráfico');
            })
            .finally(() => {
                // Restaurar botón
                generateBtn.disabled = false;
                generateBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Generar Gráfico
                `;
                updateActionButtons();
            });
        }

        function debugChartData(chartData, config) {
            console.log('=== DEBUG CHART DATA (Single Level) ===');
            console.log('Categories:', chartData.categories);
            console.log('Series count:', chartData.series.length);
            console.log('Selected level:', config.selected_level);
            
            chartData.series.forEach((series, index) => {
                console.log(`Serie ${index + 1}:`, {
                    name: series.name,
                    dataPoints: series.data.length,
                    total: series.data.reduce((sum, val) => sum + val, 0),
                    sampleData: series.data.slice(0, 5)
                });
            });
            
            console.log('Config:', config);
            console.log('==========================================');
        }

        function displayChart(chartData, config, template) {
            console.log('Datos del gráfico:', chartData);
            console.log('Configuración:', config);

            debugChartData(chartData, config);

            const chartContainer = document.getElementById('chartContainer');
            const resultSection = document.getElementById('chartResult');

            // Actualizar títulos
            document.getElementById('chartTitle').textContent = template.name;
            
            if (config.chart_type === 'table') {
                document.getElementById('chartSubtitle').textContent = `Tabla de ${config.y_label} por ${config.x_label} - Nivel: ${config.selected_level} - ${chartData.categories.length} categorías`;
                displayTable(chartData, config, template);
            } else {
                document.getElementById('chartSubtitle').textContent = `${config.y_label} por ${config.x_label} - Nivel: ${config.selected_level} - ${chartData.categories.length} categorías`;
                displayApexChart(chartData, config, template);
            }

            resultSection.classList.remove('hidden');
            resultSection.scrollIntoView({ behavior: 'smooth' });
        }

        function displayApexChart(chartData, config, template) {
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '<div id="apexChart" style="height: 500px;"></div>';

            let chartOptions;

            switch (config.chart_type) {
                case 'column':
                    chartOptions = createColumnChart(chartData, config, template);
                    break;
                case 'bar':
                    chartOptions = createBarChart(chartData, config, template);
                    break;
                case 'line':
                    chartOptions = createLineChart(chartData, config, template);
                    break;
                case 'pie':
                    chartOptions = createPieChart(chartData, config, template);
                    break;
                default:
                    chartOptions = createColumnChart(chartData, config, template);
            }

            if (currentChart) {
                currentChart.destroy();
            }

            currentChart = new ApexCharts(document.querySelector("#apexChart"), chartOptions);
            currentChart.render().then(function() {
                console.log('Gráfico single-level renderizado exitosamente');
                setupExportButtons();
            }).catch(function(error) {
                console.error('Error renderizando gráfico single-level:', error);
                alert('Error al renderizar el gráfico: ' + error.message);
            });
            
            currentConfig = config;
        }

        function displayTable(chartData, config, template) {
            const chartContainer = document.getElementById('chartContainer');
            
            // Crear HTML de la tabla igual que en use-template
            let tableHtml = `
                <div class="table-container">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                                        ${config.x_label}
                                    </th>
            `;

            // Headers para cada archivo
            chartData.series.forEach(series => {
                tableHtml += `
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                        ${series.name}
                    </th>
                `;
            });

            tableHtml += `
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
            `;

            // Filas de datos
            chartData.categories.forEach((category, index) => {
                const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                let rowTotal = 0;
                tableHtml += `<tr class="${rowClass}">`;
                
                // Categoría
                tableHtml += `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                        ${category}
                    </td>
                `;

                chartData.series.forEach(series => {
                    const value = series.data[index] || 0;
                    rowTotal += value;
                    tableHtml += `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-300">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${value.toLocaleString()}
                            </span>
                        </td>
                    `;
                });

                // Total de la fila
                tableHtml += `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 bg-gray-100">
                        ${rowTotal.toLocaleString()}
                    </td>
                `;
                
                tableHtml += '</tr>';
            });

            // Fila de totales
            tableHtml += `
                <tr class="bg-gray-100 font-bold">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r border-gray-300">
                        TOTAL
                    </td>
            `;
            
            let grandTotal = 0;
            chartData.series.forEach(series => {
                const levelTotal = series.data.reduce((sum, val) => sum + val, 0);
                grandTotal += levelTotal;
                tableHtml += `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r border-gray-300">
                        ${levelTotal.toLocaleString()}
                    </td>
                `;
            });
            
            tableHtml += `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 bg-gray-200">
                        ${grandTotal.toLocaleString()}
                    </td>
                </tr>
            `;

            tableHtml += `
                        </tbody>
                    </table>
                </div>
            </div>
            `;

            chartContainer.innerHTML = tableHtml;
            setupTableExportButtons(chartData, config, template);
        }

        // FUNCIONES DE CREACIÓN DE GRÁFICOS (COPIADAS DE use-template)
        function createColumnChart(chartData, config, template) {
            return {
                series: chartData.series,
                chart: {
                    type: 'bar',
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
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
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
                            return val.toLocaleString()
                        }
                    }
                },
                title: {
                    text: `${template.name} - Nivel: ${config.selected_level}`,
                    align: 'center'
                }
            };
        }

        function createBarChart(chartData, config, template) {
            const columnOptions = createColumnChart(chartData, config, template);
            columnOptions.plotOptions.bar.horizontal = true;
            columnOptions.plotOptions.bar.barHeight = '55%';
            return columnOptions;
        }

        function createLineChart(chartData, config, template) {
            return {
                series: chartData.series,
                chart: {
                    type: 'line',
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
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
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
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString()
                        }
                    }
                },
                markers: {
                    size: 4
                },
                title: {
                    text: `${template.name} - Nivel: ${config.selected_level}`,
                    align: 'center'
                }
            };
        }

        function createPieChart(chartData, config, template) {
            // Para gráficos de pie, sumar los totales por archivo
            const pieData = [];
            const pieLabels = [];
            const pieColors = [];
            
            chartData.series.forEach(series => {
                const total = series.data.reduce((sum, val) => sum + val, 0);
                if (total > 0) {
                    pieData.push(total);
                    pieLabels.push(series.name);
                    pieColors.push(series.color);
                }
            });
            
            return {
                series: pieData,
                chart: {
                    type: 'pie',
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
                labels: pieLabels,
                colors: pieColors,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString()
                        }
                    }
                },
                title: {
                    text: `${template.name} - Nivel: ${config.selected_level}`,
                    align: 'center'
                }
            };
        }

        function setupExportButtons() {
            const exportPngBtn = document.getElementById('exportPngBtn');
            const exportSvgBtn = document.getElementById('exportSvgBtn');

            if (exportPngBtn && currentChart) {
                exportPngBtn.onclick = function() {
                    currentChart.dataURI().then(function(data) {
                        const link = document.createElement('a');
                        link.href = data.imgURI;
                        link.download = `grafico_single_level_${new Date().getTime()}.png`;
                        link.click();
                    });
                };
            }

            if (exportSvgBtn && currentChart) {
                exportSvgBtn.onclick = function() {
                    currentChart.dataURI({type: 'svg'}).then(function(data) {
                        const link = document.createElement('a');
                        link.href = data.imgURI;
                        link.download = `grafico_single_level_${new Date().getTime()}.svg`;
                        link.click();
                    });
                };
            }
        }

        function setupTableExportButtons(chartData, config, template) {
            const exportPngBtn = document.getElementById('exportPngBtn');
            const exportSvgBtn = document.getElementById('exportSvgBtn');

            if (exportPngBtn) {
                exportPngBtn.onclick = function() {
                    exportTableAsImage('png', chartData, config, template);
                };
                const pngSpan = exportPngBtn.querySelector('span');
                if (pngSpan) {
                    pngSpan.textContent = 'PNG';
                }
            }

            if (exportSvgBtn) {
                exportSvgBtn.onclick = function() {
                    exportTableAsCSV(chartData, config, template);
                };
                const svgSpan = exportSvgBtn.querySelector('span');
                if (svgSpan) {
                    svgSpan.textContent = 'CSV';
                }
            }
        }

        function exportTableAsCSV(chartData, config, template) {
            let csvContent = '';
            
            // Header
            let headerRow = [config.x_label];
            chartData.series.forEach(series => {
                headerRow.push(series.name);
            });
            headerRow.push('Total');
            csvContent += headerRow.join(',') + '\n';
            
            // Data rows
            chartData.categories.forEach((category, index) => {
                let row = [category];
                let rowTotal = 0;
                
                chartData.series.forEach(series => {
                    const value = series.data[index] || 0;
                    rowTotal += value;
                    row.push(value);
                });
                
                row.push(rowTotal);
                csvContent += row.join(',') + '\n';
            });
            
            // Total row
            let totalRow = ['TOTAL'];
            let grandTotal = 0;
            chartData.series.forEach(series => {
                const levelTotal = series.data.reduce((sum, val) => sum + val, 0);
                grandTotal += levelTotal;
                totalRow.push(levelTotal);
            });
            totalRow.push(grandTotal);
            csvContent += totalRow.join(',') + '\n';
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `tabla_single_level_${config.selected_level}_${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportTableAsImage(format, chartData, config, template) {
            // Esta función podría implementarse usando html2canvas o similar
            alert('La exportación de tablas como imagen requiere una librería adicional. Por ahora, usa la exportación CSV.');
        }
    </script>
</x-app-layout>