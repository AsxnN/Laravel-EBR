<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\charts\use-template.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Generar Gráfico: ') . $template->name }}
            </h2>
            <a href="{{ route('charts.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Plantillas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información de la plantilla -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
                                {{ $template->name }}
                            </h1>
                            <p class="mt-2 text-gray-500 leading-relaxed">
                                {{ $template->description }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="h-12 w-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                                @if($template->chart_type == 'bar' || $template->chart_type == 'column')
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @elseif($template->chart_type == 'line')
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                    </svg>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $template->chart_type_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Configuración de la plantilla -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Eje X (Categorías)</h3>
                            <p class="text-lg font-semibold text-indigo-600">{{ $template->x_axis_label }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Eje Y (Valores)</h3>
                            <p class="text-lg font-semibold text-indigo-600">{{ $template->y_axis_label }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Segmentación</h3>
                            <p class="text-lg font-semibold text-indigo-600">Por Nivel Educativo</p>
                        </div>
                    </div>

                    <!-- Propósito -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">¿Para qué sirve este gráfico?</h3>
                        <p class="text-sm text-blue-800">{{ $template->purpose }}</p>
                    </div>
                </div>
            </div>

            <!-- Selección de archivos -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-medium text-gray-900">
                        Seleccionar Archivos por Nivel Educativo
                    </h2>
                    <p class="mt-2 text-gray-500 leading-relaxed">
                        Elige los archivos de cada nivel educativo que quieres incluir en el gráfico.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <form id="chartGenerateForm">
                        @csrf
                        <!-- Archivos por nivel educativo -->
                        @php
                            // En lugar de agrupar por document_type, vamos a mostrar todos los archivos
                            // y permitir que el usuario los seleccione manualmente por nivel
                            $allFiles = $files;
                            $levels = [
                                'inicial' => ['name' => 'Inicial', 'color' => 'blue', 'icon' => '🎨'],
                                'primaria' => ['name' => 'Primaria', 'color' => 'green', 'icon' => '📚'],
                                'secundaria' => ['name' => 'Secundaria', 'color' => 'purple', 'icon' => '🎓']
                            ];
                        @endphp

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            @foreach($levels as $levelKey => $levelInfo)
                                <div class="border border-gray-200 rounded-lg">
                                    <div class="bg-{{ $levelInfo['color'] }}-50 border-b border-{{ $levelInfo['color'] }}-200 px-4 py-3 rounded-t-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg">{{ $levelInfo['icon'] }}</span>
                                                <h3 class="text-lg font-medium text-{{ $levelInfo['color'] }}-900">
                                                    {{ $levelInfo['name'] }}
                                                </h3>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       id="selectAll{{ ucfirst($levelKey) }}" 
                                                       class="select-all-level h-4 w-4 text-{{ $levelInfo['color'] }}-600 focus:ring-{{ $levelInfo['color'] }}-500 border-gray-300 rounded"
                                                       data-level="{{ $levelKey }}">
                                                <label for="selectAll{{ ucfirst($levelKey) }}" class="ml-2 text-sm text-{{ $levelInfo['color'] }}-700">
                                                    Seleccionar todos
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        @php
                                            // Filtrar archivos que podrían pertenecer a este nivel
                                            $levelFiles = $allFiles->filter(function($file) use ($levelKey) {
                                                $name = strtolower($file->original_name);
                                                $docType = strtolower($file->document_type);
                                                
                                                if ($levelKey === 'inicial') {
                                                    return str_contains($name, 'inicial') || 
                                                           str_contains($name, 'jardin') || 
                                                           str_contains($name, 'jardín') ||
                                                           str_contains($name, 'cuna') ||
                                                           str_contains($name, '3 años') ||
                                                           str_contains($name, '4 años') ||
                                                           str_contains($name, '5 años') ||
                                                           str_contains($docType, 'inicial') ||
                                                           // También incluir archivos que no claramente pertenecen a otros niveles
                                                           (!str_contains($name, 'primaria') && !str_contains($name, 'secundaria') && 
                                                            !str_contains($docType, 'primaria') && !str_contains($docType, 'secundaria'));
                                                } elseif ($levelKey === 'primaria') {
                                                    return str_contains($name, 'primaria') || 
                                                           str_contains($docType, 'primaria') ||
                                                           str_contains($name, '1er grado') ||
                                                           str_contains($name, '2do grado') ||
                                                           str_contains($name, '3er grado') ||
                                                           str_contains($name, '4to grado') ||
                                                           str_contains($name, '5to grado') ||
                                                           str_contains($name, '6to grado');
                                                } else { // secundaria
                                                    return str_contains($name, 'secundaria') || 
                                                           str_contains($docType, 'secundaria') ||
                                                           str_contains($name, '1° sec') ||
                                                           str_contains($name, '2° sec') ||
                                                           str_contains($name, '3° sec') ||
                                                           str_contains($name, '4° sec') ||
                                                           str_contains($name, '5° sec');
                                                }
                                            });
                                        @endphp

                                        @if($levelFiles->count() > 0)
                                            <div class="space-y-3 max-h-60 overflow-y-auto">
                                                @foreach($levelFiles as $file)
                                                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                                        <input type="checkbox" 
                                                               id="file_{{ $file->id }}_{{ $levelKey }}" 
                                                               name="file_ids[]" 
                                                               value="{{ $file->id }}"
                                                               class="file-checkbox mt-1 h-4 w-4 text-{{ $levelInfo['color'] }}-600 focus:ring-{{ $levelInfo['color'] }}-500 border-gray-300 rounded"
                                                               data-level="{{ $levelKey }}"
                                                               data-file-id="{{ $file->id }}"
                                                               data-document-type="{{ $file->document_type }}"
                                                               data-original-name="{{ $file->original_name }}"
                                                               data-intended-level="{{ $levelKey }}">
                                                        <div class="flex-1 min-w-0">
                                                            <label for="file_{{ $file->id }}_{{ $levelKey }}" class="cursor-pointer">
                                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                                    {{ $file->original_name }}
                                                                </p>
                                                                <div class="mt-1 text-xs text-gray-500">
                                                                    <div class="grid grid-cols-2 gap-2">
                                                                        <span>📍 {{ $file->total_institutions ?? 0 }} instituciones</span>
                                                                        <span>👥 {{ $file->total_students ?? 0 }} estudiantes</span>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span>📅 {{ $file->uploaded_at->format('d/m/Y H:i') }}</span>
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span class="text-xs text-gray-400">Tipo: {{ $file->document_type }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-1 flex gap-1">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $levelInfo['color'] }}-100 text-{{ $levelInfo['color'] }}-800">
                                                                        Asignar a {{ $levelInfo['name'] }}
                                                                    </span>
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                        {{ ucfirst($file->document_type) }}
                                                                    </span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Mostrar resumen de archivos en esta sección -->
                                            <div class="mt-3 p-2 bg-{{ $levelInfo['color'] }}-50 rounded text-xs text-{{ $levelInfo['color'] }}-700">
                                                {{ $levelFiles->count() }} archivo(s) disponible(s) para {{ $levelInfo['name'] }}
                                            </div>
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500">
                                                    No hay archivos detectados para {{ $levelInfo['name'] }}
                                                </p>
                                                <a href="{{ route('files.create') }}" 
                                                   class="mt-2 text-xs text-{{ $levelInfo['color'] }}-600 hover:text-{{ $levelInfo['color'] }}-500">
                                                    Subir archivo de {{ $levelInfo['name'] }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumen de selección -->
                        <div id="selectionSummary" class="mt-6 p-4 bg-gray-50 rounded-lg hidden">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Resumen de Selección</h3>
                            <div id="summaryContent" class="text-sm text-gray-600">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Vista previa de datos -->
                        <div id="previewSection" class="mt-6 hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Vista Previa de Datos</h3>
                                <button type="button" 
                                        id="refreshPreviewBtn"
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
                        <div class="mt-8 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <span id="fileCount">0</span> archivo(s) seleccionado(s)
                            </div>
                            <div class="flex space-x-4">
                                <button type="button" 
                                        id="previewBtn"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Vista Previa
                                </button>
                                <button type="submit" 
                                        id="generateBtn"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Generar Gráfico
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Área del gráfico generado -->
            <div id="chartSection" class="bg-white overflow-hidden shadow-xl sm:rounded-lg hidden">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 id="chartTitle" class="text-xl font-medium text-gray-900">Gráfico Generado</h2>
                            <p id="chartSubtitle" class="text-sm text-gray-500 mt-1"></p>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" 
                                    id="exportPngBtn" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Descargar PNG
                            </button>
                            <button type="button" 
                                    id="exportSvgBtn" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Descargar SVG
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6 lg:p-8">
                    <div id="chartContainer" style="height: 500px;">
                        <!-- El gráfico se renderiza aquí -->
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
        const templateId = {{ $template->id }};

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('chartGenerateForm');
            const previewBtn = document.getElementById('previewBtn');
            const generateBtn = document.getElementById('generateBtn');
            const refreshPreviewBtn = document.getElementById('refreshPreviewBtn');

            // Manejar selección "Seleccionar todos"
            document.querySelectorAll('.select-all-level').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const level = this.dataset.level;
                    const levelCheckboxes = document.querySelectorAll(`input[data-level="${level}"].file-checkbox`);
                    
                    levelCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    
                    updateSelectionSummary();
                });
            });

            // Manejar selección individual de archivos
            document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectionSummary();
                    
                    // Actualizar el checkbox "Seleccionar todos" correspondiente
                    const level = this.dataset.level;
                    const levelCheckboxes = document.querySelectorAll(`input[data-level="${level}"].file-checkbox`);
                    const selectAllCheckbox = document.querySelector(`input[data-level="${level}"].select-all-level`);
                    
                    const checkedCount = document.querySelectorAll(`input[data-level="${level}"].file-checkbox:checked`).length;
                    selectAllCheckbox.checked = checkedCount === levelCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < levelCheckboxes.length;
                });
            });

            // Botones de acción
            previewBtn.addEventListener('click', showPreview);
            generateBtn.addEventListener('click', generateChart);
            refreshPreviewBtn?.addEventListener('click', showPreview);

            // Inicializar estado
            updateSelectionSummary();
        });

        function updateSelectionSummary() {
            const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
            const fileCount = selectedFiles.length;
            
            document.getElementById('fileCount').textContent = fileCount;
            
            // Habilitar/deshabilitar botones
            const previewBtn = document.getElementById('previewBtn');
            const generateBtn = document.getElementById('generateBtn');
            
            previewBtn.disabled = fileCount === 0;
            generateBtn.disabled = fileCount === 0;
            
            if (fileCount > 0) {
                // Agrupar por nivel
                const byLevel = {};
                selectedFiles.forEach(input => {
                    const level = input.dataset.level;
                    byLevel[level] = (byLevel[level] || 0) + 1;
                });
                
                let summaryHtml = '<div class="flex flex-wrap gap-2">';
                for (const [level, count] of Object.entries(byLevel)) {
                    const levelName = level.charAt(0).toUpperCase() + level.slice(1);
                    const colorClass = level === 'inicial' ? 'blue' : level === 'primaria' ? 'green' : 'purple';
                    summaryHtml += `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${colorClass}-100 text-${colorClass}-800">
                            ${levelName}: ${count} archivo(s)
                        </span>
                    `;
                }
                summaryHtml += '</div>';
                
                document.getElementById('summaryContent').innerHTML = summaryHtml;
                document.getElementById('selectionSummary').classList.remove('hidden');
            } else {
                document.getElementById('selectionSummary').classList.add('hidden');
                document.getElementById('previewSection').classList.add('hidden');
            }
        }

        function showPreview() {
            const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
            if (selectedFiles.length === 0) {
                alert('Selecciona al menos un archivo');
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            selectedFiles.forEach(input => {
                formData.append('file_ids[]', input.value);
            });

            previewBtn.disabled = true;
            previewBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Cargando...';

            fetch(`{{ route('charts.generate-from-template', $template->id) }}`, {
                method: 'POST',
                body: formData
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
                previewBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>Vista Previa';
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
                        Mostrando primeros ${limitedCategories.length} de ${data.data.categories.length} elementos
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

        function debugFileSelection() {
            const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
            console.log('=== DEBUG FILE SELECTION ===');
            
            selectedFiles.forEach((checkbox, index) => {
                console.log(`Archivo ${index + 1}:`, {
                    fileId: checkbox.dataset.fileId,
                    documentType: checkbox.dataset.documentType,
                    originalName: checkbox.dataset.originalName,
                    level: checkbox.dataset.level
                });
            });
            
            console.log('============================');
        }

        // Modificar la función generateChart para incluir el debug:
        function generateChart() {
            const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
            if (selectedFiles.length === 0) {
                alert('Selecciona al menos un archivo');
                return;
            }

            debugFileSelection();

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            
            // Enviar información sobre los niveles asignados
            const assignedLevels = {};
            selectedFiles.forEach(input => {
                formData.append('file_ids[]', input.value);
                const intendedLevel = input.dataset.intendedLevel;
                assignedLevels[input.value] = intendedLevel.charAt(0).toUpperCase() + intendedLevel.slice(1);
            });
            
            formData.append('assigned_levels', JSON.stringify(assignedLevels));

            console.log('Niveles asignados:', assignedLevels);

            // Resto de la función igual...
            const generateBtn = document.getElementById('generateBtn');
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generando...';

            fetch(`{{ route('charts.generate-from-template', $template->id) }}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderChart(data.data, data.config, data.template);
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
        function debugChartData(chartData, config) {
            console.log('=== DEBUG CHART DATA ===');
            console.log('Categories:', chartData.categories);
            console.log('Series count:', chartData.series.length);
            
            chartData.series.forEach((series, index) => {
                console.log(`Serie ${index + 1}:`, {
                    name: series.name,
                    dataPoints: series.data.length,
                    total: series.data.reduce((sum, val) => sum + val, 0),
                    sampleData: series.data.slice(0, 5)
                });
            });
            
            console.log('Config:', config);
            console.log('========================');
        }

        

        // Modificar la función renderChart para incluir el debug:
        function renderChart(chartData, config, template) {
            debugChartData(chartData, config);
            
            console.log('Renderizando gráfico con datos:', chartData, config);
            
            currentConfig = config;
            
            // Verificar que tenemos datos
            if (!chartData.series || chartData.series.length === 0) {
                alert('No se encontraron datos para generar el gráfico');
                return;
            }

            if (!chartData.categories || chartData.categories.length === 0) {
                alert('No se encontraron categorías para el eje X');
                return;
            }
            
            // Configurar opciones según el tipo de gráfico
            let options = {};
            
            switch(config.chart_type) {
                case 'pie':
                    options = createPieChart(chartData, config, template);
                    break;
                case 'bar':
                    options = createBarChart(chartData, config, template);
                    break;
                case 'column':
                    options = createColumnChart(chartData, config, template);
                    break;
                case 'line':
                    options = createLineChart(chartData, config, template);
                    break;
                default:
                    options = createColumnChart(chartData, config, template);
            }

            console.log('Opciones del gráfico:', options);

            // Destruir gráfico anterior si existe
            if (currentChart) {
                currentChart.destroy();
            }

            try {
                currentChart = new ApexCharts(document.querySelector("#chartContainer"), options);
                currentChart.render().then(function() {
                    console.log('Gráfico renderizado exitosamente');
                    
                    // Mostrar sección del gráfico
                    document.getElementById('chartSection').classList.remove('hidden');
                    document.getElementById('chartTitle').textContent = template.name;
                    document.getElementById('chartSubtitle').textContent = `${config.y_label} por ${config.x_label} - ${chartData.categories.length} categorías`;

                    // Configurar botones de exportación
                    setupExportButtons();

                    // Scroll hacia el gráfico
                    document.getElementById('chartSection').scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start' 
                    });
                }).catch(function(error) {
                    console.error('Error renderizando gráfico:', error);
                    alert('Error al renderizar el gráfico: ' + error.message);
                });
            } catch (error) {
                console.error('Error creando gráfico:', error);
                alert('Error al crear el gráfico: ' + error.message);
            }
        }

        // 1. GRÁFICO DE COLUMNAS (Vertical)
        function createColumnChart(chartData, config, template) {
            return {
                series: chartData.series.map(series => ({
                    name: series.name,
                    data: series.data,
                    color: series.color
                })),
                chart: {
                    type: 'bar',
                    height: 500,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end',
                        dataLabels: {
                            position: 'top'
                        }
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
                        text: config.x_label,
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    },
                    labels: {
                        rotate: chartData.categories.length > 8 ? -45 : 0,
                        style: {
                            fontSize: '12px',
                            colors: '#6B7280'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: config.y_label,
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString();
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString() + " " + config.y_label.toLowerCase();
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontWeight: 600
                },
                title: {
                    text: template.name,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold',
                        color: '#111827'
                    }
                },
                subtitle: {
                    text: `${config.y_label} por ${config.x_label} - Segmentado por Nivel Educativo`,
                    align: 'center',
                    style: {
                        fontSize: '14px',
                        color: '#6B7280'
                    }
                },
                colors: chartData.series.map(series => series.color),
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        }

        // 2. GRÁFICO DE BARRAS (Horizontal)
        function createBarChart(chartData, config, template) {
            return {
                series: chartData.series.map(series => ({
                    name: series.name,
                    data: series.data,
                    color: series.color
                })),
                chart: {
                    type: 'bar',
                    height: 500,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '70%',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: -6,
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    title: {
                        text: config.y_label, // En barras horizontales, el eje X muestra los valores
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString();
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: config.x_label, // En barras horizontales, el eje Y muestra las categorías
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString() + " " + config.y_label.toLowerCase();
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontWeight: 600
                },
                title: {
                    text: template.name,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold',
                        color: '#111827'
                    }
                },
                subtitle: {
                    text: `${config.y_label} por ${config.x_label} - Segmentado por Nivel Educativo`,
                    align: 'center',
                    style: {
                        fontSize: '14px',
                        color: '#6B7280'
                    }
                },
                // Para barras horizontales, las categorías van en el eje Y
                yaxis: {
                    ...{
                        title: {
                            text: config.x_label,
                            style: {
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#374151'
                            }
                        }
                    },
                    categories: chartData.categories
                },
                colors: chartData.series.map(series => series.color),
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        }

        // 3. GRÁFICO DE LÍNEAS
        function createLineChart(chartData, config, template) {
            return {
                series: chartData.series.map(series => ({
                    name: series.name,
                    data: series.data,
                    color: series.color
                })),
                chart: {
                    height: 500,
                    type: 'line',
                    zoom: {
                        enabled: true
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: template.name,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold',
                        color: '#111827'
                    }
                },
                subtitle: {
                    text: `${config.y_label} por ${config.x_label} - Segmentado por Nivel Educativo`,
                    align: 'center',
                    style: {
                        fontSize: '14px',
                        color: '#6B7280'
                    }
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: chartData.categories,
                    title: {
                        text: config.x_label,
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    },
                    labels: {
                        rotate: chartData.categories.length > 8 ? -45 : 0,
                        style: {
                            fontSize: '12px',
                            colors: '#6B7280'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: config.y_label,
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#374151'
                        }
                    },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString();
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return Math.round(val).toLocaleString() + " " + config.y_label.toLowerCase();
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontWeight: 600
                },
                markers: {
                    size: 5,
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                colors: chartData.series.map(series => series.color),
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        }

        // 4. GRÁFICO CIRCULAR (PIE/DONA)
        function createPieChart(chartData, config, template) {
            // Para gráfico de pie, necesitamos sumar todos los valores por nivel
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
                    width: 500,
                    type: 'pie',
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
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                labels: pieLabels,
                colors: pieColors,
                title: {
                    text: template.name,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold',
                        color: '#111827'
                    }
                },
                subtitle: {
                    text: `Distribución de ${config.y_label} por Nivel Educativo`,
                    align: 'center',
                    style: {
                        fontSize: '14px',
                        color: '#6B7280'
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontWeight: 600
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.labels[opts.seriesIndex] + ": " + val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val, opts) {
                            const percentage = ((val / pieData.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                            return Math.round(val).toLocaleString() + " " + config.y_label.toLowerCase() + " (" + percentage + "%)";
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        donut: {
                            size: '0%' // 0% para pie completo, puedes cambiarlo a 40% para donut
                        }
                    }
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            width: 350
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }, {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        }

        // FUNCIÓN PARA CONFIGURAR BOTONES DE EXPORTACIÓN
        function setupExportButtons() {
            const exportPngBtn = document.getElementById('exportPngBtn');
            const exportSvgBtn = document.getElementById('exportSvgBtn');

            if (exportPngBtn) {
                exportPngBtn.onclick = function() {
                    if (currentChart) {
                        currentChart.dataURI().then(({imgURI}) => {
                            const link = document.createElement('a');
                            link.href = imgURI;
                            link.download = `grafico_${currentConfig?.x_axis || 'chart'}_${new Date().getTime()}.png`;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }).catch(function(error) {
                            console.error('Error exportando PNG:', error);
                            alert('Error al exportar el gráfico como PNG');
                        });
                    } else {
                        alert('No hay gráfico para exportar');
                    }
                };
            }

            if (exportSvgBtn) {
                exportSvgBtn.onclick = function() {
                    if (currentChart) {
                        currentChart.dataURI({type: 'svg'}).then(({imgURI}) => {
                            const link = document.createElement('a');
                            link.href = imgURI;
                            link.download = `grafico_${currentConfig?.x_axis || 'chart'}_${new Date().getTime()}.svg`;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }).catch(function(error) {
                            console.error('Error exportando SVG:', error);
                            alert('Error al exportar el gráfico como SVG');
                        });
                    } else {
                        alert('No hay gráfico para exportar');
                    }
                };
            }
        }
    </script>
</x-app-layout>