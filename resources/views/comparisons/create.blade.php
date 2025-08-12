<!-- filepath: resources/views/comparisons/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Nueva Comparativa') }}
            </h2>
            <a href="{{ route('comparisons.index') }}" 
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
            
            <!-- Selector de tipo de comparativa -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Tipo de Comparativa
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Selecciona el tipo de comparativa que deseas crear según tus necesidades de análisis.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Gráficos de Un Solo Nivel -->
                        <div class="comparison-type-card cursor-pointer border-2 border-gray-200 rounded-lg p-6 hover:border-indigo-500 transition-colors"
                             onclick="selectComparisonType('single_level')"
                             data-type="single_level">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-100 text-indigo-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        Gráficos de Un Solo Nivel
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        Analizar datos específicos de un nivel educativo
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                <ul class="space-y-1">
                                    <li>• Selecciona campos específicos de datos</li>
                                    <li>• Compara múltiples archivos del mismo nivel</li>
                                    <li>• Ideal para análisis detallados</li>
                                    <li>• Inicial, Primaria o Secundaria por separado</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Gráficos Comparativos Multi-nivel -->
                        <div class="comparison-type-card cursor-pointer border-2 border-gray-200 rounded-lg p-6 hover:border-green-500 transition-colors"
                             onclick="selectComparisonType('multi_level')"
                             data-type="multi_level">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        Gráficos Comparativos Multi-nivel
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        Comparar los 3 niveles educativos juntos
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                <ul class="space-y-1">
                                    <li>• Compara Inicial, Primaria y Secundaria</li>
                                    <li>• Filtro por período (mes/año)</li>
                                    <li>• Máximo 1 archivo por nivel</li>
                                    <li>• Ideal para análisis globales</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario Single Level -->
            <div id="singleLevelForm" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8" style="display: none;">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Configurar Gráficos de Un Solo Nivel
                    </h3>
                    <p class="text-sm text-gray-600">
                        Selecciona una plantilla y configura los parámetros para generar gráficos de un nivel educativo específico.
                    </p>
                </div>

                <form id="singleLevelComparisonForm" class="p-6 lg:p-8">
                    @csrf
                    <input type="hidden" name="comparison_type" value="single_level">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Nombre de la comparativa -->
                        <div class="lg:col-span-2">
                            <label for="single_comparison_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Comparativa *
                            </label>
                            <input type="text" 
                                   id="single_comparison_name" 
                                   name="comparison_name" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Ej: Análisis Matrícula Primaria - Marzo 2024"
                                   required>
                        </div>

                        <!-- Selección de plantilla -->
                        <div>
                            <label for="single_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Plantilla de Gráficos *
                            </label>
                            <select id="single_template_id" 
                                    name="template_id" 
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    onchange="loadSingleLevelTemplate()"
                                    required>
                                <option value="">Seleccionar plantilla...</option>
                                @foreach($singleLevelTemplates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-charts="{{ $template->charts->count() }}"
                                            data-description="{{ $template->description }}">
                                        {{ $template->name }} ({{ $template->charts->count() }} gráficos)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nivel educativo -->
                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">
                                Nivel Educativo *
                            </label>
                            <select id="education_level" 
                                    name="education_level" 
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    onchange="filterFilesByLevel()"
                                    required>
                                <option value="">Seleccionar nivel...</option>
                                <option value="inicial">Inicial</option>
                                <option value="primaria">Primaria</option>
                                <option value="secundaria">Secundaria</option>
                            </select>
                        </div>
                    </div>

                    <!-- Información de la plantilla seleccionada -->
                    <div id="singleTemplateInfo" class="mb-6" style="display: none;">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Información de la Plantilla</h4>
                            <div id="singleTemplateDetails" class="text-sm text-blue-700"></div>
                        </div>
                    </div>

                    <!-- Filtros geográficos opcionales -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtros Geográficos (Opcional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label for="single_dre_filter" class="block text-xs font-medium text-gray-600 mb-1">DRE</label>
                                <select id="single_dre_filter" name="geo_filters[dre]" class="geo-filter-select">
                                    <option value="">Todas las DREs</option>
                                    @foreach($geoFilters['dre'] as $dre)
                                        <option value="{{ $dre }}">{{ $dre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="single_ugel_filter" class="block text-xs font-medium text-gray-600 mb-1">UGEL</label>
                                <select id="single_ugel_filter" name="geo_filters[ugel]" class="geo-filter-select">
                                    <option value="">Todas las UGELs</option>
                                    @foreach($geoFilters['ugel'] as $ugel)
                                        <option value="{{ $ugel }}">{{ $ugel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="single_departamento_filter" class="block text-xs font-medium text-gray-600 mb-1">Departamento</label>
                                <select id="single_departamento_filter" name="geo_filters[departamento]" class="geo-filter-select">
                                    <option value="">Todos los Departamentos</option>
                                    @foreach($geoFilters['departamento'] as $departamento)
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="single_provincia_filter" class="block text-xs font-medium text-gray-600 mb-1">Provincia</label>
                                <select id="single_provincia_filter" name="geo_filters[provincia]" class="geo-filter-select">
                                    <option value="">Todas las Provincias</option>
                                    @foreach($geoFilters['provincia'] as $provincia)
                                        <option value="{{ $provincia }}">{{ $provincia }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="single_distrito_filter" class="block text-xs font-medium text-gray-600 mb-1">Distrito</label>
                                <select id="single_distrito_filter" name="geo_filters[distrito]" class="geo-filter-select">
                                    <option value="">Todos los Distritos</option>
                                    @foreach($geoFilters['distrito'] as $distrito)
                                        <option value="{{ $distrito }}">{{ $distrito }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de archivos -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-700">Archivos Disponibles</h4>
                            <button type="button" onclick="selectAllSingleFiles()" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Seleccionar todos
                            </button>
                        </div>
                        <div id="singleLevelFiles" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Los archivos se cargarán dinámicamente -->
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <span id="singleSelectedCount">0</span> archivos seleccionados
                        </div>
                    </div>

                    <!-- Botón de generar -->
                    <div class="flex items-center justify-end border-t border-gray-200 pt-6">
                        <button type="submit" 
                                id="generateSingleBtn"
                                disabled
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span id="generateSingleBtnText">Generar Gráficos</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Formulario Multi Level -->
            <div id="multiLevelForm" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8" style="display: none;">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Configurar Gráficos Comparativos Multi-nivel
                    </h3>
                    <p class="text-sm text-gray-600">
                        Selecciona una plantilla y período para comparar los tres niveles educativos.
                    </p>
                </div>

                <form id="multiLevelComparisonForm" class="p-6 lg:p-8">
                    @csrf
                    <input type="hidden" name="comparison_type" value="multi_level">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Nombre de la comparativa -->
                        <div class="lg:col-span-2">
                            <label for="multi_comparison_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Comparativa *
                            </label>
                            <input type="text" 
                                   id="multi_comparison_name" 
                                   name="comparison_name" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Ej: Comparativa Niveles Educativos - Marzo 2024"
                                   required>
                        </div>

                        <!-- Selección de plantilla -->
                        <div>
                            <label for="multi_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Plantilla de Gráficos *
                            </label>
                            <select id="multi_template_id" 
                                    name="template_id" 
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    onchange="loadMultiLevelTemplate()"
                                    required>
                                <option value="">Seleccionar plantilla...</option>
                                @foreach($multiLevelTemplates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-charts="{{ $template->charts->count() }}"
                                            data-description="{{ $template->description }}">
                                        {{ $template->name }} ({{ $template->charts->count() }} gráficos)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Período -->
                        <div>
                            <label for="selected_period" class="block text-sm font-medium text-gray-700 mb-2">
                                Período (Mes/Año) *
                            </label>
                            <select id="selected_period" 
                                    name="selected_period" 
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    onchange="filterFilesByPeriod()"
                                    required>
                                <option value="">Seleccionar período...</option>
                                @foreach($availablePeriods as $period)
                                    @php
                                        $date = \Carbon\Carbon::createFromFormat('Y-m', $period);
                                    @endphp
                                    <option value="{{ $period }}">{{ $date->format('F Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Información de la plantilla seleccionada -->
                    <div id="multiTemplateInfo" class="mb-6" style="display: none;">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">Información de la Plantilla</h4>
                            <div id="multiTemplateDetails" class="text-sm text-green-700"></div>
                        </div>
                    </div>

                    <!-- Filtros geográficos opcionales -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtros Geográficos (Opcional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label for="multi_dre_filter" class="block text-xs font-medium text-gray-600 mb-1">DRE</label>
                                <select id="multi_dre_filter" name="geo_filters[dre]" class="geo-filter-select">
                                    <option value="">Todas las DREs</option>
                                    @foreach($geoFilters['dre'] as $dre)
                                        <option value="{{ $dre }}">{{ $dre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="multi_ugel_filter" class="block text-xs font-medium text-gray-600 mb-1">UGEL</label>
                                <select id="multi_ugel_filter" name="geo_filters[ugel]" class="geo-filter-select">
                                    <option value="">Todas las UGELs</option>
                                    @foreach($geoFilters['ugel'] as $ugel)
                                        <option value="{{ $ugel }}">{{ $ugel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="multi_departamento_filter" class="block text-xs font-medium text-gray-600 mb-1">Departamento</label>
                                <select id="multi_departamento_filter" name="geo_filters[departamento]" class="geo-filter-select">
                                    <option value="">Todos los Departamentos</option>
                                    @foreach($geoFilters['departamento'] as $departamento)
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="multi_provincia_filter" class="block text-xs font-medium text-gray-600 mb-1">Provincia</label>
                                <select id="multi_provincia_filter" name="geo_filters[provincia]" class="geo-filter-select">
                                    <option value="">Todas las Provincias</option>
                                    @foreach($geoFilters['provincia'] as $provincia)
                                        <option value="{{ $provincia }}">{{ $provincia }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="multi_distrito_filter" class="block text-xs font-medium text-gray-600 mb-1">Distrito</label>
                                <select id="multi_distrito_filter" name="geo_filters[distrito]" class="geo-filter-select">
                                    <option value="">Todos los Distritos</option>
                                    @foreach($geoFilters['distrito'] as $distrito)
                                        <option value="{{ $distrito }}">{{ $distrito }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de archivos por nivel -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Archivos por Nivel Educativo</h4>
                        <p class="text-xs text-gray-500 mb-4">Selecciona máximo 1 archivo por cada nivel educativo del período seleccionado.</p>
                        
                        <div class="space-y-6">
                            <!-- Inicial -->
                            <div>
                                <h5 class="text-sm font-medium text-blue-700 mb-2">Educación Inicial</h5>
                                <div id="multiLevelFilesInicial" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Los archivos se cargarán dinámicamente -->
                                </div>
                            </div>

                            <!-- Primaria -->
                            <div>
                                <h5 class="text-sm font-medium text-green-700 mb-2">Educación Primaria</h5>
                                <div id="multiLevelFilesPrimaria" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Los archivos se cargarán dinámicamente -->
                                </div>
                            </div>

                            <!-- Secundaria -->
                            <div>
                                <h5 class="text-sm font-medium text-purple-700 mb-2">Educación Secundaria</h5>
                                <div id="multiLevelFilesSecundaria" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Los archivos se cargarán dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-sm text-gray-500">
                            <span id="multiSelectedCount">0</span> archivos seleccionados (máximo 3)
                        </div>
                    </div>

                    <!-- Botón de generar -->
                    <div class="flex items-center justify-end border-t border-gray-200 pt-6">
                        <button type="submit" 
                                id="generateMultiBtn"
                                disabled
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            </svg>
                            <span id="generateMultiBtnText">Generar Comparativa</span>
                        </button>
                    </div>
                </form>
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
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Generando Comparativa</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="loadingMessage">
                        Procesando archivos y creando visualizaciones...
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comparison-type-card.selected {
            @apply border-indigo-500 bg-indigo-50;
        }
        
        .geo-filter-select {
            @apply mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xs border-gray-300 rounded-md;
        }
    </style>

    <script>
        // Datos de archivos por tipo desde el backend
        const filesByType = @json($filesByType);
        let selectedComparisonType = null;

        // Seleccionar tipo de comparativa
        function selectComparisonType(type) {
            selectedComparisonType = type;
            
            // Actualizar estilos de las tarjetas
            document.querySelectorAll('.comparison-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            document.querySelector(`[data-type="${type}"]`).classList.add('selected');
            
            // Mostrar/ocultar formularios
            document.getElementById('singleLevelForm').style.display = type === 'single_level' ? 'block' : 'none';
            document.getElementById('multiLevelForm').style.display = type === 'multi_level' ? 'block' : 'none';
        }

        // Cargar información de plantilla single level
        function loadSingleLevelTemplate() {
            const select = document.getElementById('single_template_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const charts = selectedOption.dataset.charts;
                const description = selectedOption.dataset.description;
                
                document.getElementById('singleTemplateDetails').innerHTML = `
                    <p><strong>Gráficos configurados:</strong> ${charts}</p>
                    ${description ? `<p><strong>Descripción:</strong> ${description}</p>` : ''}
                `;
                
                document.getElementById('singleTemplateInfo').style.display = 'block';
            } else {
                document.getElementById('singleTemplateInfo').style.display = 'none';
            }
            
            updateSingleLevelButton();
        }

        // Cargar información de plantilla multi level
        function loadMultiLevelTemplate() {
            const select = document.getElementById('multi_template_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const charts = selectedOption.dataset.charts;
                const description = selectedOption.dataset.description;
                
                document.getElementById('multiTemplateDetails').innerHTML = `
                    <p><strong>Gráficos configurados:</strong> ${charts}</p>
                    ${description ? `<p><strong>Descripción:</strong> ${description}</p>` : ''}
                `;
                
                document.getElementById('multiTemplateInfo').style.display = 'block';
            } else {
                document.getElementById('multiTemplateInfo').style.display = 'none';
            }
            
            updateMultiLevelButton();
        }

        // Filtrar archivos por nivel educativo
        function filterFilesByLevel() {
            const level = document.getElementById('education_level').value;
            const container = document.getElementById('singleLevelFiles');
            
            if (!level) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Selecciona un nivel educativo para ver los archivos disponibles.</p>';
                return;
            }
            
            const files = filesByType[level] || {};
            let html = '';
            
            if (Object.keys(files).length === 0) {
                html = '<p class="text-gray-500 text-center py-4">No hay archivos disponibles para este nivel educativo.</p>';
            } else {
                Object.entries(files).forEach(([period, periodFiles]) => {
                    periodFiles.forEach(file => {
                        html += createFileCard(file, 'single');
                    });
                });
            }
            
            container.innerHTML = html;
            updateSingleLevelButton();
        }

        // Filtrar archivos por período
        function filterFilesByPeriod() {
            const period = document.getElementById('selected_period').value;
            
            if (!period) {
                ['inicial', 'primaria', 'secundaria'].forEach(level => {
                    const container = document.getElementById(`multiLevelFiles${level.charAt(0).toUpperCase() + level.slice(1)}`);
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">Selecciona un período para ver los archivos disponibles.</p>';
                });
                return;
            }
            
            ['inicial', 'primaria', 'secundaria'].forEach(level => {
                const container = document.getElementById(`multiLevelFiles${level.charAt(0).toUpperCase() + level.slice(1)}`);
                const files = filesByType[level]?.[period] || [];
                
                let html = '';
                if (files.length === 0) {
                    html = '<p class="text-gray-500 text-center py-4">No hay archivos disponibles para este período.</p>';
                } else {
                    files.forEach(file => {
                        html += createFileCard(file, 'multi', level);
                    });
                }
                
                container.innerHTML = html;
            });
            
            updateMultiLevelButton();
        }

        // Crear tarjeta de archivo
        function createFileCard(file, mode, level = null) {
            const radioName = mode === 'multi' ? `multi_file_${level}` : 'single_files';
            const inputType = mode === 'multi' ? 'radio' : 'checkbox';
            const onChangeFunction = mode === 'multi' ? 'updateMultiLevelSelection' : 'updateSingleLevelSelection';
            
            return `
                <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="${inputType}" 
                           name="${radioName}" 
                           value="${file.id}"
                           class="mt-1 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 ${inputType === 'radio' ? 'rounded-full' : 'rounded'}"
                           onchange="${onChangeFunction}()">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate">
                            ${file.original_name}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-${getEducationLevelColor(file.document_type)}-100 text-${getEducationLevelColor(file.document_type)}-800">
                                ${file.document_type.charAt(0).toUpperCase() + file.document_type.slice(1)}
                            </span>
                            <span class="ml-2">${formatDate(file.uploaded_at)}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            ${file.total_institutions || 0} IE • ${file.total_students || 0} estudiantes
                        </div>
                    </div>
                </label>
            `;
        }

        // Obtener color por nivel educativo
        function getEducationLevelColor(level) {
            const colors = {
                'inicial': 'blue',
                'primaria': 'green',
                'secundaria': 'purple'
            };
            return colors[level] || 'gray';
        }

        // Formatear fecha
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        // Actualizar selección single level
        function updateSingleLevelSelection() {
            const selected = document.querySelectorAll('input[name="single_files"]:checked');
            document.getElementById('singleSelectedCount').textContent = selected.length;
            updateSingleLevelButton();
        }

        // Actualizar selección multi level
        function updateMultiLevelSelection() {
            const selectedInicial = document.querySelectorAll('input[name="multi_file_inicial"]:checked');
            const selectedPrimaria = document.querySelectorAll('input[name="multi_file_primaria"]:checked');
            const selectedSecundaria = document.querySelectorAll('input[name="multi_file_secundaria"]:checked');
            
            const total = selectedInicial.length + selectedPrimaria.length + selectedSecundaria.length;
            document.getElementById('multiSelectedCount').textContent = total;
            updateMultiLevelButton();
        }

        // Seleccionar todos los archivos (single level)
        function selectAllSingleFiles() {
            const checkboxes = document.querySelectorAll('input[name="single_files"]');
            const allSelected = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allSelected;
            });
            
            updateSingleLevelSelection();
        }

        // Actualizar botón single level
        function updateSingleLevelButton() {
            const btn = document.getElementById('generateSingleBtn');
            const name = document.getElementById('single_comparison_name').value.trim();
            const template = document.getElementById('single_template_id').value;
            const level = document.getElementById('education_level').value;
            const selected = document.querySelectorAll('input[name="single_files"]:checked');
            
            const isValid = name && template && level && selected.length > 0;
            btn.disabled = !isValid;
            
            if (isValid) {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Actualizar botón multi level
        function updateMultiLevelButton() {
            const btn = document.getElementById('generateMultiBtn');
            const name = document.getElementById('multi_comparison_name').value.trim();
            const template = document.getElementById('multi_template_id').value;
            const period = document.getElementById('selected_period').value;
            const selectedInicial = document.querySelectorAll('input[name="multi_file_inicial"]:checked');
            const selectedPrimaria = document.querySelectorAll('input[name="multi_file_primaria"]:checked');
            const selectedSecundaria = document.querySelectorAll('input[name="multi_file_secundaria"]:checked');
            
            const totalSelected = selectedInicial.length + selectedPrimaria.length + selectedSecundaria.length;
            const isValid = name && template && period && totalSelected > 0;
            
            btn.disabled = !isValid;
            
            if (isValid) {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Event listeners para formularios
        document.addEventListener('DOMContentLoaded', function() {
            // Single level form listeners
            document.getElementById('single_comparison_name').addEventListener('input', updateSingleLevelButton);
            
            // Multi level form listeners
            document.getElementById('multi_comparison_name').addEventListener('input', updateMultiLevelButton);
            
            // Form submissions
            document.getElementById('singleLevelComparisonForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitComparison('single');
            });
            
            document.getElementById('multiLevelComparisonForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitComparison('multi');
            });
        });

        // Enviar formulario de comparativa
        function submitComparison(type) {
            const form = document.getElementById(`${type}LevelComparisonForm`);
            const formData = new FormData(form);
            
            // Agregar archivos seleccionados según el tipo
            if (type === 'single') {
                const selectedFiles = document.querySelectorAll('input[name="single_files"]:checked');
                selectedFiles.forEach(checkbox => {
                    formData.append('selected_files[]', checkbox.value);
                });
            } else {
                const selectedInicial = document.querySelectorAll('input[name="multi_file_inicial"]:checked');
                const selectedPrimaria = document.querySelectorAll('input[name="multi_file_primaria"]:checked');
                const selectedSecundaria = document.querySelectorAll('input[name="multi_file_secundaria"]:checked');
                
                selectedInicial.forEach(checkbox => formData.append('selected_files[]', checkbox.value));
                selectedPrimaria.forEach(checkbox => formData.append('selected_files[]', checkbox.value));
                selectedSecundaria.forEach(checkbox => formData.append('selected_files[]', checkbox.value));
            }
            
            // Mostrar modal de carga
            showLoadingModal();
            
            // Deshabilitar botón
            const btn = document.getElementById(`generate${type === 'single' ? 'Single' : 'Multi'}Btn`);
            const btnText = document.getElementById(`generate${type === 'single' ? 'Single' : 'Multi'}BtnText`);
            btn.disabled = true;
            btnText.textContent = 'Generando...';
            
            // Enviar datos
            fetch('{{ route("comparisons.generate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    alert('Error: ' + data.message);
                    resetButton(type);
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Error:', error);
                alert('Error al generar la comparativa. Por favor, intenta nuevamente.');
                resetButton(type);
            });
        }

        // Mostrar modal de carga
        function showLoadingModal() {
            document.getElementById('loadingModal').classList.remove('hidden');
        }

        // Ocultar modal de carga
        function hideLoadingModal() {
            document.getElementById('loadingModal').classList.add('hidden');
        }

        // Resetear botón después de error
        function resetButton(type) {
            const btn = document.getElementById(`generate${type === 'single' ? 'Single' : 'Multi'}Btn`);
            const btnText = document.getElementById(`generate${type === 'single' ? 'Single' : 'Multi'}BtnText`);
            btn.disabled = false;
            btnText.textContent = type === 'single' ? 'Generar Gráficos' : 'Generar Comparativa';
        }
    </script>
</x-app-layout>