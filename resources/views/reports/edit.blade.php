<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\reports\edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Reporte: {{ $report->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Modifica la información del reporte y agrega o elimina gráficos
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('reports.show', $report->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver Reporte
                </a>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Reporte</h3>
                    <form id="updateReportForm">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Título del Reporte *
                                </label>
                                <input type="text" id="title" name="title" required 
                                       value="{{ $report->title }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado
                                </label>
                                <select id="status" name="status" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="draft" {{ $report->status === 'draft' ? 'selected' : '' }}>Borrador</option>
                                    <option value="published" {{ $report->status === 'published' ? 'selected' : '' }}>Publicado</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <textarea id="description" name="description" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $report->description }}</textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Actualizar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Agregar nuevo gráfico usando plantillas -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-medium text-gray-900">
                        Agregar Nuevo Gráfico
                    </h2>
                    <p class="mt-2 text-gray-500 leading-relaxed">
                        Selecciona una plantilla y elige los archivos por nivel educativo para crear un nuevo gráfico.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <!-- Selección de plantilla -->
                    <div class="mb-6">
                        <label for="templateSelect" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Plantilla *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($templates as $template)
                                <div class="template-card border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer"
                                     data-template-id="{{ $template->id }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                @if($template->chart_type == 'bar' || $template->chart_type == 'column')
                                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                @elseif($template->chart_type == 'line')
                                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <input type="radio" 
                                                   name="selected_template" 
                                                   value="{{ $template->id }}" 
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $template->chart_type_label }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $template->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $template->description }}</p>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="bg-gray-50 rounded p-2">
                                            <span class="font-medium text-gray-700">Eje X:</span>
                                            <span class="text-gray-600">{{ $template->x_axis_label }}</span>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2">
                                            <span class="font-medium text-gray-700">Eje Y:</span>
                                            <span class="text-gray-600">{{ $template->y_axis_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Información de la plantilla seleccionada -->
                    <div id="templateInfo" class="hidden mb-6 p-4 bg-blue-50 rounded-lg">
                        <div id="templateDetails">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>

                    <!-- Selección de archivos por nivel educativo -->
                    <div id="fileSelectionSection" class="hidden">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Seleccionar Archivos por Nivel Educativo
                        </h3>
                        
                        @php
                            $allFiles = $files->flatten();
                            $levels = [
                                'inicial' => ['name' => 'Inicial', 'color' => 'blue', 'icon' => '🎨'],
                                'primaria' => ['name' => 'Primaria', 'color' => 'green', 'icon' => '📚'],
                                'secundaria' => ['name' => 'Secundaria', 'color' => 'purple', 'icon' => '🎓']
                            ];
                        @endphp

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                            @foreach($levels as $levelKey => $levelInfo)
                                <div class="border border-gray-200 rounded-lg">
                                    <div class="bg-{{ $levelInfo['color'] }}-50 px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="text-{{ $levelInfo['color'] }}-600 text-lg mr-2">{{ $levelInfo['icon'] }}</span>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900">
                                                        Nivel {{ $levelInfo['name'] }}
                                                    </h3>
                                                    <p class="text-xs text-gray-500">
                                                        Selecciona uno o más archivos
                                                    </p>
                                                </div>
                                            </div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                    class="form-checkbox h-4 w-4 text-{{ $levelInfo['color'] }}-600 select-all-level" 
                                                    data-level="{{ $levelKey }}">
                                                <span class="ml-2 text-xs text-gray-600">Todos</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 max-h-64 overflow-y-auto">
                                        @php $levelFiles = $allFiles->where('document_type', $levelKey) @endphp
                                        
                                        @if($levelFiles->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($levelFiles as $file)
                                                    <label class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                        <input type="checkbox" 
                                                            class="form-checkbox h-4 w-4 text-{{ $levelInfo['color'] }}-600 file-checkbox" 
                                                            value="{{ $file->id }}"
                                                            data-level="{{ $levelKey }}"
                                                            data-intended-level="{{ $levelKey }}"
                                                            data-file-id="{{ $file->id }}"
                                                            data-document-type="{{ $file->document_type }}"
                                                            data-original-name="{{ $file->original_name }}">
                                                        
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $file->original_name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                <div class="flex items-center space-x-2">
                                                                    <span>{{ number_format($file->total_students ?? 0) }} estudiantes</span>
                                                                    <span>•</span>
                                                                    <span>{{ $file->uploaded_at->format('d/m/Y') }}</span>
                                                                </div>
                                                                @if($file->processing_summary && isset($file->processing_summary['by_ugel']))
                                                                    <div class="text-xs text-gray-400 mt-1">
                                                                        {{ count($file->processing_summary['by_ugel']) }} UGEL(s)
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div class="text-gray-400 text-sm">
                                                    No hay archivos de {{ $levelInfo['name'] }}
                                                </div>
                                                <a href="{{ route('files.create') }}" class="text-xs text-{{ $levelInfo['color'] }}-600 hover:text-{{ $levelInfo['color'] }}-500">
                                                    Subir archivo
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Información mejorada del resumen de selección -->
                        <div id="selectionSummary" class="mt-6 p-4 bg-gray-50 rounded-lg hidden">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Resumen de Selección</h3>
                            <div id="summaryContent" class="text-sm text-gray-600">
                                <!-- Se llena dinámicamente -->
                            </div>
                            <div id="multipleFilesWarning" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700 hidden">
                                <strong>Nota:</strong> Tienes múltiples archivos del mismo nivel. Cada archivo aparecerá como una serie separada en el gráfico.
                            </div>
                        </div>

                        <!-- Título del gráfico y notas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="chart_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Título del Gráfico *
                                </label>
                                <input type="text" id="chart_title" name="chart_title" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Ej: Matrícula por UGEL 2025">
                            </div>
                            <div>
                                <label for="chart_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notas (Opcional)
                                </label>
                                <textarea id="chart_notes" name="chart_notes" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Notas adicionales sobre este gráfico..."></textarea>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <span id="fileCount">0</span> archivo(s) seleccionado(s)
                            </div>
                            <div class="flex space-x-4">
                                <button type="button" 
                                        id="previewChartBtn"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Vista Previa
                                </button>
                                <button type="button" 
                                        id="addChartBtn"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Agregar al Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista previa del gráfico -->
            <div id="chartPreviewSection" class="bg-white overflow-hidden shadow-xl sm:rounded-lg hidden">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Vista Previa del Gráfico</h3>
                </div>
                <div class="p-6 lg:p-8">
                    <div id="chartPreviewContainer" style="height: 400px;">
                        <!-- El gráfico de vista previa se renderiza aquí -->
                    </div>
                </div>
            </div>

            <!-- Gráficos existentes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Gráficos del Reporte ({{ $report->charts->count() }})</h3>
                        @if($report->charts->count() > 1)
                            <button onclick="enableReorder()" 
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                                Reordenar
                            </button>
                        @endif
                    </div>

                    @if($report->charts->count() > 0)
                        <div id="chartsContainer" class="space-y-4">
                            @foreach($report->charts as $chart)
                                <div class="chart-item border rounded-lg p-4" data-chart-id="{{ $chart->id }}" data-order="{{ $chart->order }}">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="reorder-handle cursor-move hidden">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $chart->chart_title }}</h4>
                                                <p class="text-sm text-gray-600">
                                                    Plantilla: {{ $chart->template->name }} | 
                                                    Tipo: {{ $chart->template->chart_type_label }} |
                                                    Archivos: {{ $chart->file_count }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">Orden: {{ $chart->order }}</span>
                                            <button onclick="removeChart({{ $chart->id }})" 
                                                    class="text-red-600 hover:text-red-900 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @if($chart->notes)
                                        <div class="text-sm text-gray-600 bg-gray-50 rounded p-2 mb-4">
                                            <strong>Notas:</strong> {{ $chart->notes }}
                                        </div>
                                    @endif

                                    <!-- Preview del gráfico -->
                                    <div id="chart_preview_{{ $chart->id }}" class="w-full h-64 bg-gray-50 rounded border"></div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="reorderControls" class="hidden mt-4 flex justify-end space-x-3">
                            <button onclick="cancelReorder()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button onclick="saveOrder()" 
                                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Guardar Orden
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay gráficos</h3>
                            <p class="mt-1 text-sm text-gray-500">Agrega tu primer gráfico usando una plantilla.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
    let currentPreviewChart = null;
    let selectedTemplate = null;
    const templates = @json($templates);

    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        updateFileSelectionState();
        
        // Renderizar gráficos existentes
        @foreach($report->charts as $chart)
            renderChartPreview({{ $chart->id }}, @json($chart->chart_data), @json($chart->chart_config));
        @endforeach
    });

    function initializeEventListeners() {
        // Actualizar información del reporte
        document.getElementById('updateReportForm').addEventListener('submit', updateReport);

        // Selección de plantillas
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                selectTemplate(radio.value);
            });
        });

        document.querySelectorAll('input[name="selected_template"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    selectTemplate(this.value);
                }
            });
        });

        // Manejar selección "Seleccionar todos"
        document.querySelectorAll('.select-all-level').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const level = this.dataset.level;
                const levelCheckboxes = document.querySelectorAll(`input[data-level="${level}"].file-checkbox`);
                
                levelCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                
                updateFileSelectionState();
            });
        });

        // Manejar selección individual de archivos
        document.querySelectorAll('.file-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateFileSelectionState();
                
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
        document.getElementById('previewChartBtn')?.addEventListener('click', previewChart);
        document.getElementById('addChartBtn')?.addEventListener('click', addChartToReport);
    }

    function selectTemplate(templateId) {
        selectedTemplate = templates.find(t => t.id == templateId);
        
        if (selectedTemplate) {
            showTemplateInfo(selectedTemplate);
            document.getElementById('fileSelectionSection').classList.remove('hidden');
            
            // Auto-generar título del gráfico si está vacío
            const titleInput = document.getElementById('chart_title');
            if (!titleInput.value.trim()) {
                titleInput.value = selectedTemplate.name;
            }
        }
    }

    function showTemplateInfo(template) {
        const templateDetails = document.getElementById('templateDetails');
        if (templateDetails) {
            templateDetails.innerHTML = `
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">${template.name}</h4>
                        <p class="text-sm text-gray-600 mt-1">${template.description || ''}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${template.chart_type_label || template.chart_type}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white rounded-lg p-3 border">
                        <h5 class="text-sm font-medium text-gray-900">Eje X</h5>
                        <p class="text-sm text-blue-600">${template.x_axis_label || template.x_axis}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 border">
                        <h5 class="text-sm font-medium text-gray-900">Eje Y</h5>
                        <p class="text-sm text-blue-600">${template.y_axis_label || template.y_axis}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 border">
                        <h5 class="text-sm font-medium text-gray-900">Segmentación</h5>
                        <p class="text-sm text-blue-600">Por Nivel Educativo</p>
                    </div>
                </div>
                ${template.purpose ? `
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <h5 class="text-sm font-medium text-blue-900">Propósito:</h5>
                    <p class="text-sm text-blue-800">${template.purpose}</p>
                </div>
                ` : ''}
            `;
            
            document.getElementById('templateInfo').classList.remove('hidden');
        }
    }

    function updateFileSelectionState() {
        const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
        const fileCount = selectedFiles.length;
        
        const fileCountElement = document.getElementById('fileCount');
        if (fileCountElement) {
            fileCountElement.textContent = fileCount;
        }
        
        const previewBtn = document.getElementById('previewChartBtn');
        const addBtn = document.getElementById('addChartBtn');
        
        if (previewBtn) {
            previewBtn.disabled = fileCount === 0 || !selectedTemplate;
        }
        
        if (addBtn) {
            const chartTitle = document.getElementById('chart_title');
            addBtn.disabled = fileCount === 0 || !selectedTemplate || !chartTitle?.value.trim();
        }
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

    // FUNCIÓN QUE FALTABA - validateChartForm
    function validateChartForm() {
        if (!selectedTemplate) {
            showNotification('Selecciona una plantilla', 'error');
            return false;
        }

        const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
        if (selectedFiles.length === 0) {
            showNotification('Selecciona al menos un archivo', 'error');
            return false;
        }

        const chartTitle = document.getElementById('chart_title');
        if (!chartTitle || !chartTitle.value.trim()) {
            showNotification('Ingresa un título para el gráfico', 'error');
            return false;
        }

        return true;
    }

    function createChartFormData() {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        const selectedFiles = document.querySelectorAll('.file-checkbox:checked');
        const assignedLevels = {};
        
        console.log('=== CREANDO FORM DATA ===');
        console.log('Selected files count:', selectedFiles.length);
        
        selectedFiles.forEach((input, index) => {
            formData.append('file_ids[]', input.value);
            const intendedLevel = input.dataset.intendedLevel;
            assignedLevels[input.value] = intendedLevel.charAt(0).toUpperCase() + intendedLevel.slice(1);
            
            console.log(`File ${index + 1}:`, {
                fileId: input.value,
                intendedLevel: intendedLevel,
                assignedLevel: assignedLevels[input.value]
            });
        });
        
        const assignedLevelsJson = JSON.stringify(assignedLevels);
        formData.append('assigned_levels', assignedLevelsJson);
        
        console.log('Assigned levels JSON:', assignedLevelsJson);
        console.log('=== FIN FORM DATA ===');

        return formData;
    }

    function previewChart() {
        if (!validateChartForm()) return;

        const formData = createChartFormData();
        
        const previewBtn = document.getElementById('previewChartBtn');
        const originalText = previewBtn.innerHTML;
        previewBtn.disabled = true;
        previewBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Cargando...';

        fetch(`/charts/template/${selectedTemplate.id}/generate`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPreviewChart(data.data, data.config, data.template);
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al generar vista previa', 'error');
        })
        .finally(() => {
            previewBtn.disabled = false;
            previewBtn.innerHTML = originalText;
        });
    }

    function addChartToReport() {
        if (!validateChartForm()) return;

        debugFileSelection();

        const formData = createChartFormData();
        formData.append('chart_title', document.getElementById('chart_title').value);
        formData.append('notes', document.getElementById('chart_notes').value || '');
        formData.append('template_id', selectedTemplate.id);

        const addBtn = document.getElementById('addChartBtn');
        const originalText = addBtn.innerHTML;
        addBtn.disabled = true;
        addBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Agregando...';

        // Debug logs detallados
        console.log('=== ENVIANDO DATOS AL SERVIDOR ===');
        console.log('Template ID:', selectedTemplate.id);
        console.log('Chart Title:', document.getElementById('chart_title').value);
        
        // Mostrar todos los datos del FormData
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        fetch(`/reports/{{ $report->id }}/charts`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, text: ${text}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('=== RESPUESTA DEL SERVIDOR ===');
            console.log('Response data:', data);
            
            if (data.success) {
                showNotification('Gráfico agregado exitosamente al reporte', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error: ' + (data.message || 'Error desconocido'), 'error');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('=== ERROR COMPLETO ===');
            console.error('Fetch error:', error);
            showNotification('Error al agregar gráfico al reporte: ' + error.message, 'error');
        })
        .finally(() => {
            addBtn.disabled = false;
            addBtn.innerHTML = originalText;
        });
    }

    function renderPreviewChart(chartData, config, template) {
        if (currentPreviewChart) {
            currentPreviewChart.destroy();
        }

        const options = createChartOptions(chartData, config, template);
        const previewContainer = document.querySelector("#chartPreviewContainer");
        
        if (previewContainer) {
            currentPreviewChart = new ApexCharts(previewContainer, options);
            
            currentPreviewChart.render().then(() => {
                document.getElementById('chartPreviewSection').classList.remove('hidden');
                document.getElementById('chartPreviewSection').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start' 
                });
            });
        }
    }

    function createChartOptions(chartData, config, template) {
        // Usar las funciones específicas según el tipo de gráfico
        switch(config.chart_type) {
            case 'pie':
                return createPieChart(chartData, config, template);
            case 'bar':
                return createBarChart(chartData, config, template);
            case 'column':
                return createColumnChart(chartData, config, template);
            case 'line':
                return createLineChart(chartData, config, template);
            default:
                return createColumnChart(chartData, config, template);
        }
    }
    // Función para actualizar reporte
    function updateReport(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        fetch(`/reports/{{ $report->id }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Información actualizada exitosamente', 'success');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al actualizar información', 'error');
        });
    }

    function removeChart(chartId) {
        if (confirm('¿Estás seguro de que quieres eliminar este gráfico?')) {
            fetch(`/reports/{{ $report->id }}/charts/${chartId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Gráfico eliminado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al eliminar gráfico', 'error');
            });
        }
    }

    function renderChartPreview(chartId, chartData, config) {
        const options = createChartOptions(chartData, config, { name: 'Gráfico' });
        const chartElement = document.querySelector("#chart_preview_" + chartId);
        
        if (chartElement) {
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }
    }

    function showNotification(message, type) {
        const color = type === 'success' ? 'green' : 'red';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 bg-${color}-500 text-white px-6 py-3 rounded-md shadow-lg z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Funciones de reordenación
    let isReordering = false;

    function enableReorder() {
        isReordering = true;
        const reorderControls = document.getElementById('reorderControls');
        if (reorderControls) {
            reorderControls.classList.remove('hidden');
        }
        document.querySelectorAll('.reorder-handle').forEach(el => el.classList.remove('hidden'));
    }

    function cancelReorder() {
        isReordering = false;
        const reorderControls = document.getElementById('reorderControls');
        if (reorderControls) {
            reorderControls.classList.add('hidden');
        }
        document.querySelectorAll('.reorder-handle').forEach(el => el.classList.add('hidden'));
    }

    function saveOrder() {
        const chartOrders = Array.from(document.querySelectorAll('.chart-item')).map((el, index) => ({
            id: parseInt(el.dataset.chartId),
            order: index
        }));

        fetch(`/reports/{{ $report->id }}/charts/reorder`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ chart_orders: chartOrders })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Orden guardado exitosamente', 'success');
                cancelReorder();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al guardar orden', 'error');
        });
    }

    function createColumnChart(chartData, config, template) {
    return {
        series: chartData.series.map(series => ({
            name: series.name,
            data: series.data,
            color: series.color
        })),
        chart: {
            type: 'bar',
            height: 400,
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
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            fontWeight: 600
        },
        title: {
            text: template.name,
            align: 'center',
            style: {
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#111827'
            }
        },
        colors: chartData.series.map(series => series.color)
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
            height: 400,
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
        yaxis: {
            title: {
                text: config.x_label,
                style: {
                    fontSize: '14px',
                    fontWeight: 600,
                    color: '#374151'
                }
            },
            categories: chartData.categories
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
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            fontWeight: 600
        },
        title: {
            text: template.name,
            align: 'center',
            style: {
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#111827'
            }
        },
        colors: chartData.series.map(series => series.color)
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
            height: 400,
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
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#111827'
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
            position: 'bottom',
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
        colors: chartData.series.map(series => series.color)
    };
}

// 4. GRÁFICO CIRCULAR (PIE)
function createPieChart(chartData, config, template) {
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
            width: 400,
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
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#111827'
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
                    size: '0%'
                }
            }
        }
    };
}
    </script>
</x-app-layout>