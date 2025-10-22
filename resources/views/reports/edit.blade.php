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
                        <label for="templateSelect" class="block text-sm font-medium text-gray-700 mb-4">
                            Seleccionar Plantilla *
                        </label>
                        
                        <!-- PLANTILLAS DE UN SOLO NIVEL -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Plantillas de Un Solo Nivel</h3>
                                    <p class="text-sm text-gray-600">Compara múltiples archivos del mismo nivel educativo</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                                @foreach($templates->where('level_type', 'single') as $template)
                                    <div class="template-card bg-white border-2 border-blue-300 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-500 hover:shadow-lg transition-all duration-200 cursor-pointer relative"
                                         data-template-id="{{ $template->id }}">
                                        <!-- Badge "Un Solo Nivel" -->
                                        <div class="absolute -top-2 -right-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-600 text-white shadow-md">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5z"></path>
                                                </svg>
                                                1 Nivel
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center ring-2 ring-blue-300">
                                                    @if($template->chart_type == 'bar' || $template->chart_type == 'column')
                                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                        </svg>
                                                    @elseif($template->chart_type == 'line')
                                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <input type="radio" 
                                                       name="selected_template" 
                                                       value="{{ $template->id }}" 
                                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-blue-300">
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-300">
                                                {{ $template->chart_type_label }}
                                            </span>
                                        </div>
                                        
                                        <h3 class="text-base font-semibold text-gray-900 mb-2">{{ $template->name }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $template->description }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-blue-50 rounded p-2 border border-blue-200">
                                                <span class="font-medium text-blue-900">Eje X:</span>
                                                <span class="text-blue-700 block truncate">{{ $template->x_axis_label }}</span>
                                            </div>
                                            <div class="bg-blue-50 rounded p-2 border border-blue-200">
                                                <span class="font-medium text-blue-900">Eje Y:</span>
                                                <span class="text-blue-700 block truncate">{{ $template->y_axis_label }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($templates->where('level_type', 'single')->count() == 0)
                                    <div class="col-span-3 text-center py-8 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p>No hay plantillas de un solo nivel disponibles</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- PLANTILLAS MULTI-NIVEL -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Plantillas Multi-Nivel</h3>
                                    <p class="text-sm text-gray-600">Compara archivos de diferentes niveles educativos</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4 bg-green-50 rounded-lg border-2 border-green-200">
                                @foreach($templates->where('level_type', 'multiple') as $template)
                                    <div class="template-card bg-white border-2 border-green-300 rounded-lg p-4 hover:bg-green-50 hover:border-green-500 hover:shadow-lg transition-all duration-200 cursor-pointer relative"
                                         data-template-id="{{ $template->id }}">
                                        <!-- Badge "Multi-Nivel" -->
                                        <div class="absolute -top-2 -right-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-600 text-white shadow-md">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                                                </svg>
                                                Multi-Nivel
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center ring-2 ring-green-300">
                                                    @if($template->chart_type == 'bar' || $template->chart_type == 'column')
                                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                        </svg>
                                                    @elseif($template->chart_type == 'line')
                                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <input type="radio" 
                                                       name="selected_template" 
                                                       value="{{ $template->id }}" 
                                                       class="h-5 w-5 text-green-600 focus:ring-green-500 border-green-300">
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-300">
                                                {{ $template->chart_type_label }}
                                            </span>
                                        </div>
                                        
                                        <h3 class="text-base font-semibold text-gray-900 mb-2">{{ $template->name }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $template->description }}</p>
                                        
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-green-50 rounded p-2 border border-green-200">
                                                <span class="font-medium text-green-900">Eje X:</span>
                                                <span class="text-green-700 block truncate">{{ $template->x_axis_label }}</span>
                                            </div>
                                            <div class="bg-green-50 rounded p-2 border border-green-200">
                                                <span class="font-medium text-green-900">Eje Y:</span>
                                                <span class="text-green-700 block truncate">{{ $template->y_axis_label }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($templates->where('level_type', 'multiple')->count() == 0)
                                    <div class="col-span-3 text-center py-8 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p>No hay plantillas multi-nivel disponibles</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información de la plantilla seleccionada -->
                    <div id="templateInfo" class="hidden mb-6 p-4 bg-blue-50 rounded-lg">
                        <div id="templateDetails">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>

                    <!-- Selección de archivos por nivel educativo -->
                    @php
                        $allFiles = $files->flatten();
                        $levels = [
                            'inicial' => ['name' => 'Inicial', 'color' => 'blue', 'icon' => '🎨'],
                            'primaria' => ['name' => 'Primaria', 'color' => 'green', 'icon' => '📚'],
                            'secundaria' => ['name' => 'Secundaria', 'color' => 'purple', 'icon' => '🎓'],
                            'global' => ['name' => 'Global', 'color' => 'gray', 'icon' => '🌐']
                        ];
                    @endphp

                    <!-- Contenedor principal de selección de archivos -->
                    <div id="fileSelectionSection" class="hidden">
                        
                        <!-- ============================================ -->
                        <!-- SECCIÓN PARA PLANTILLAS DE UN SOLO NIVEL -->
                        <!-- ============================================ -->
                        <div id="singleLevelSection" class="hidden">
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Comparación de Un Solo Nivel</h3>
                                        <p class="mt-1 text-sm text-blue-700">
                                            Selecciona un nivel educativo y elige múltiples archivos de ese mismo nivel para compararlos.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- 1. Selección del Nivel Educativo -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    1. Selecciona el Nivel Educativo *
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($levels as $levelKey => $levelInfo)
                                        <label class="relative">
                                            <input type="radio" name="single_level_selection" value="{{ ucfirst($levelKey) }}" 
                                                   class="sr-only peer single-level-radio">
                                            <div class="bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-{{ $levelInfo['color'] }}-300 peer-checked:border-{{ $levelInfo['color'] }}-600 peer-checked:bg-{{ $levelInfo['color'] }}-50 transition-colors">
                                                <div class="text-center">
                                                    <div class="text-2xl mb-2">{{ $levelInfo['icon'] }}</div>
                                                    <div class="font-medium text-gray-900">{{ $levelInfo['name'] }}</div>
                                                    <div id="single-{{ $levelKey }}-count" class="text-xs text-gray-500 mt-1">
                                                        0 archivos
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 2. Archivos del Nivel Seleccionado -->
                            <div id="singleLevelFilesContainer" class="hidden">
                                <div class="mb-4 flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">
                                        2. Selecciona los Archivos a Comparar
                                    </label>
                                    <div>
                                        <button type="button" onclick="selectAllSingleLevel()" 
                                                class="text-sm text-blue-600 hover:text-blue-800">
                                            Seleccionar todos
                                        </button>
                                        <span class="text-gray-300 mx-2">|</span>
                                        <button type="button" onclick="clearAllSingleLevel()" 
                                                class="text-sm text-gray-600 hover:text-gray-800">
                                            Deseleccionar todos
                                        </button>
                                    </div>
                                </div>

                                <!-- Mensaje cuando no hay nivel seleccionado -->
                                <div id="no-level-message" class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                                    </svg>
                                    <p>Selecciona un nivel educativo para ver los archivos disponibles</p>
                                </div>

                                <div id="singleLevelFilesList" class="space-y-2">
                                    <!-- Los archivos se cargan dinámicamente aquí -->
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN PARA PLANTILLAS MULTI-NIVEL -->
                        <!-- ============================================ -->
                        <div id="multiLevelSection" class="hidden">
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Comparación Multi-Nivel</h3>
                                        <p class="mt-1 text-sm text-green-700">
                                            Selecciona archivos de diferentes niveles educativos para compararlos entre sí.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Seleccionar Archivos por Nivel Educativo
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                                @foreach($levels as $levelKey => $levelInfo)
                                    <div class="border border-gray-200 rounded-lg">
                                        <div class="bg-{{ $levelInfo['color'] }}-50 px-4 py-3 border-b border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <span class="text-lg mr-2">{{ $levelInfo['icon'] }}</span>
                                                    <div>
                                                        <h3 class="text-sm font-medium text-gray-900">{{ $levelInfo['name'] }}</h3>
                                                        <p class="text-xs text-gray-500">Selecciona archivos</p>
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
                                                        <label class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer border border-gray-100">
                                                            <input type="checkbox" 
                                                                class="form-checkbox h-4 w-4 text-{{ $levelInfo['color'] }}-600 file-checkbox multi-level-checkbox" 
                                                                value="{{ $file->id }}"
                                                                data-level="{{ $levelKey }}"
                                                                data-intended-level="{{ $levelKey }}"
                                                                data-file-id="{{ $file->id }}"
                                                                data-document-type="{{ $file->document_type }}"
                                                                data-original-name="{{ basename($file->file_path ?? $file->original_name) }}">
                                                            
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                                    {{ basename($file->file_path ?? $file->original_name) }}
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    <span>{{ number_format($file->total_students ?? 0) }} estudiantes</span>
                                                                    <span class="mx-1">•</span>
                                                                    <span>{{ $file->uploaded_at->format('d/m/Y') }}</span>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 text-gray-400 text-sm">
                                                    No hay archivos de {{ $levelInfo['name'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN COMÚN: Configuración del Gráfico -->
                        <!-- ============================================ -->
                        <div id="chartConfigSection" class="hidden">
                            <!-- Resumen de Selección -->
                            <div id="selectionSummary" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Resumen de Selección</h3>
                                <div id="summaryContent" class="text-sm text-gray-600">
                                    <!-- Se llena dinámicamente -->
                                </div>
                            </div>

                            <!-- Título y Notas -->
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

                            <!-- Botones de Acción -->
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
    const allFiles = @json($allFiles->values());

    console.log('📁 Archivos totales:', allFiles.length);
    console.log('📋 Plantillas:', templates.length);

    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        updateFileCounts();
        
        // Renderizar gráficos existentes
        @foreach($report->charts as $chart)
            renderChartPreview({{ $chart->id }}, @json($chart->chart_data), @json($chart->chart_config));
        @endforeach
    });

    function initializeEventListeners() {
        // Form del reporte
        document.getElementById('updateReportForm')?.addEventListener('submit', updateReport);

        // Selección de plantillas
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    // Obtener el level_type directamente de la plantilla
                    const templateId = radio.value;
                    const template = templates.find(t => t.id == templateId);
                    if (template) {
                        console.log('🎯 Template seleccionado:', template.name, '| Tipo:', template.level_type);
                        selectTemplate(templateId, template.level_type);
                    }
                }
            });
        });

        // Radio buttons de nivel (single level)
        document.querySelectorAll('.single-level-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    const selectedLevel = this.value;
                    console.log('📍 Nivel seleccionado:', selectedLevel);
                    loadSingleLevelFiles(selectedLevel);
                }
            });
        });

        // Multi-level checkboxes
        document.querySelectorAll('.select-all-level').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const level = this.dataset.level;
                document.querySelectorAll(`input[data-level="${level}"].multi-level-checkbox`)
                    .forEach(cb => cb.checked = this.checked);
                updateFileSelectionState();
            });
        });

        document.querySelectorAll('.multi-level-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateFileSelectionState);
        });

        // Botones de acción
        document.getElementById('previewChartBtn')?.addEventListener('click', previewChart);
        document.getElementById('addChartBtn')?.addEventListener('click', addChartToReport);
        
        // Input de título
        document.getElementById('chart_title')?.addEventListener('input', updateFileSelectionState);
    }

    function showTemplateInfo(template) {
        const templateDetails = document.getElementById('templateDetails');
        if (!templateDetails) return;

        const levelTypeColor = template.level_type === 'single' ? 'blue' : 'green';
        const levelTypeText = template.level_type === 'single' ? 'Un Nivel' : 'Multi-Nivel';
        
        templateDetails.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h4 class="text-lg font-medium text-gray-900">${template.name}</h4>
                    <p class="text-sm text-gray-600 mt-1">${template.description || ''}</p>
                </div>
                <div class="flex flex-col items-end space-y-1 ml-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        ${template.chart_type_label || template.chart_type}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${levelTypeColor}-100 text-${levelTypeColor}-800">
                        ${levelTypeText}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="bg-white rounded-lg p-3 border border-gray-200">
                    <h5 class="text-xs font-medium text-gray-500 uppercase">Eje X</h5>
                    <p class="text-sm text-gray-900 font-medium mt-1">${template.x_axis_label || template.x_axis}</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-gray-200">
                    <h5 class="text-xs font-medium text-gray-500 uppercase">Eje Y</h5>
                    <p class="text-sm text-gray-900 font-medium mt-1">${template.y_axis_label || template.y_axis}</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-gray-200">
                    <h5 class="text-xs font-medium text-gray-500 uppercase">Comparación</h5>
                    <p class="text-sm text-gray-900 font-medium mt-1">
                        ${template.level_type === 'single' ? 'Mismo nivel' : 'Entre niveles'}
                    </p>
                </div>
            </div>
            ${template.purpose ? `
            <div class="mt-4 p-3 bg-${levelTypeColor}-50 border border-${levelTypeColor}-200 rounded-lg">
                <h5 class="text-xs font-medium text-${levelTypeColor}-900 uppercase mb-1">Propósito</h5>
                <p class="text-sm text-${levelTypeColor}-800">${template.purpose}</p>
            </div>
            ` : ''}
        `;
        
        document.getElementById('templateInfo')?.classList.remove('hidden');
    }

    function debugFileSelection() {
        // ✅ CAMBIO: Seleccionar según el tipo
        let selectedFiles;
        if (selectedTemplate?.level_type === 'single') {
            selectedFiles = document.querySelectorAll('.single-level-checkbox:checked');
        } else {
            selectedFiles = document.querySelectorAll('.multi-level-checkbox:checked');
        }
        
        console.log('=== DEBUG FILE SELECTION ===');
        console.log('Template type:', selectedTemplate?.level_type);
        console.log('Total selected:', selectedFiles.length);
        
        selectedFiles.forEach((checkbox, index) => {
            console.log(`Archivo ${index + 1}:`, {
                fileId: checkbox.dataset.fileId || checkbox.value,
                documentType: checkbox.dataset.documentType,
                originalName: checkbox.dataset.originalName,
                level: checkbox.dataset.level
            });
        });
        
        console.log('============================');
    }

    function selectTemplate(templateId, levelType) {
        selectedTemplate = templates.find(t => t.id == templateId);
        
        if (!selectedTemplate) {
            console.error('❌ Plantilla no encontrada:', templateId);
            return;
        }

        console.log('✅ Plantilla seleccionada:', selectedTemplate.name);
        console.log('📊 Tipo:', levelType);
        
        // Mostrar info de la plantilla
        showTemplateInfo(selectedTemplate);
        
        // Limpiar selecciones previas
        clearAllSelections();
        
        // Ocultar TODAS las secciones
        document.getElementById('fileSelectionSection')?.classList.remove('hidden');
        document.getElementById('singleLevelSection')?.classList.add('hidden');
        document.getElementById('multiLevelSection')?.classList.add('hidden');
        document.getElementById('chartConfigSection')?.classList.add('hidden');

        // Mostrar la sección correspondiente
        if (levelType === 'single') {
            console.log('🔵 Mostrando modo SINGLE LEVEL');
            document.getElementById('singleLevelSection')?.classList.remove('hidden');
            document.getElementById('chartConfigSection')?.classList.remove('hidden');
        } else if (levelType === 'multiple') {
            console.log('🟢 Mostrando modo MULTI LEVEL');
            document.getElementById('multiLevelSection')?.classList.remove('hidden');
            document.getElementById('chartConfigSection')?.classList.remove('hidden');
        }

        // Auto-completar título
        const titleInput = document.getElementById('chart_title');
        if (titleInput && !titleInput.value.trim()) {
            titleInput.value = selectedTemplate.name;
        }

        // Scroll suave
        setTimeout(() => {
            const targetSection = levelType === 'single' 
                ? document.getElementById('singleLevelSection')
                : document.getElementById('multiLevelSection');
            
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 150);
    }
    

    function clearAllSelections() {
        // Limpiar single level
        document.querySelectorAll('.single-level-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.single-level-radio').forEach(radio => radio.checked = false);
        
        // Limpiar multi level
        document.querySelectorAll('.multi-level-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.select-all-level').forEach(cb => cb.checked = false);
        
        // Ocultar contenedor de archivos single
        document.getElementById('singleLevelFilesContainer')?.classList.add('hidden');
        
        // Resetear contador
        const fileCountElement = document.getElementById('fileCount');
        if (fileCountElement) fileCountElement.textContent = '0';
        
        // Ocultar resumen
        document.getElementById('selectionSummary')?.classList.add('hidden');
        
        // Limpiar formulario
        document.getElementById('chart_title').value = '';
        document.getElementById('chart_notes').value = '';
    }

    function updateFileCounts() {
        const levels = ['inicial', 'primaria', 'secundaria', 'global'];
        
        levels.forEach(level => {
            const count = allFiles.filter(file => 
                file.document_type && file.document_type.toLowerCase() === level
            ).length;
            
            const element = document.getElementById(`single-${level}-count`);
            if (element) {
                element.textContent = `${count} archivo${count !== 1 ? 's' : ''}`;
            }
        });
    }

    function loadSingleLevelFiles(levelName) {
        const levelKey = levelName.toLowerCase();
        const filesContainer = document.getElementById('singleLevelFilesContainer');
        const filesList = document.getElementById('singleLevelFilesList');
        
        if (!filesList) {
            console.error('❌ Elemento singleLevelFilesList no encontrado');
            return;
        }

        const levelFiles = allFiles.filter(file => 
            file.document_type && file.document_type.toLowerCase() === levelKey
        );

        console.log(`📂 Archivos de ${levelName}:`, levelFiles.length);

        if (levelFiles.length === 0) {
            filesList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="font-medium">No hay archivos de ${levelName}</p>
                    <a href="/files/create" class="text-sm text-blue-600 hover:text-blue-500 mt-2 inline-block">
                        Subir archivo de ${levelName}
                    </a>
                </div>
            `;
            filesContainer?.classList.remove('hidden');
            return;
        }

        filesList.innerHTML = levelFiles.map(file => {
            const fileName = file.file_path ? file.file_path.split('/').pop() : (file.original_name || 'Sin nombre');
            const uploadDate = file.uploaded_at ? new Date(file.uploaded_at).toLocaleDateString('es-ES') : 'N/A';
            
            return `
                <label class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-colors cursor-pointer">
                    <input type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded single-level-checkbox" 
                           value="${file.id}"
                           data-level="${levelKey}"
                           data-file-id="${file.id}"
                           data-original-name="${fileName}"
                           onchange="updateFileSelectionState()">
                    <div class="ml-3 flex-1">
                        <div class="text-sm font-medium text-gray-900">${fileName}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            <span>👥 ${(file.total_students || 0).toLocaleString()} estudiantes</span>
                            <span class="mx-2">•</span>
                            <span>📅 ${uploadDate}</span>
                        </div>
                    </div>
                </label>
            `;
        }).join('');

        filesContainer?.classList.remove('hidden');
        updateFileSelectionState();
    }

    function selectAllSingleLevel() {
        document.querySelectorAll('.single-level-checkbox').forEach(cb => cb.checked = true);
        updateFileSelectionState();
    }

    function clearAllSingleLevel() {
        document.querySelectorAll('.single-level-checkbox').forEach(cb => cb.checked = false);
        updateFileSelectionState();
    }

    function updateFileSelectionState() {
        let selectedCount = 0;
        
        if (selectedTemplate?.level_type === 'single') {
            selectedCount = document.querySelectorAll('.single-level-checkbox:checked').length;
        } else {
            selectedCount = document.querySelectorAll('.multi-level-checkbox:checked').length;
        }

        console.log('📊 Archivos seleccionados:', selectedCount);

        const fileCountElement = document.getElementById('fileCount');
        if (fileCountElement) {
            fileCountElement.textContent = selectedCount;
        }

        const previewBtn = document.getElementById('previewChartBtn');
        const addBtn = document.getElementById('addChartBtn');
        const chartTitle = document.getElementById('chart_title');

        if (previewBtn) {
            previewBtn.disabled = selectedCount === 0 || !selectedTemplate;
        }

        if (addBtn) {
            addBtn.disabled = selectedCount === 0 || !selectedTemplate || !chartTitle?.value.trim();
        }

        // Actualizar resumen
        if (selectedCount > 0) {
            updateSelectionSummary(selectedCount);
        } else {
            document.getElementById('selectionSummary')?.classList.add('hidden');
        }
    }

    function updateSelectionSummary(count) {
        const summaryElement = document.getElementById('selectionSummary');
        const summaryContent = document.getElementById('summaryContent');
        
        if (!summaryElement || !summaryContent) return;
        
        if (selectedTemplate?.level_type === 'single') {
            const selectedLevel = document.querySelector('input[name="single_level_selection"]:checked')?.value;
            summaryContent.innerHTML = `
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm"><strong>Tipo:</strong> Comparación de un solo nivel</p>
                        <p class="text-sm"><strong>Nivel:</strong> ${selectedLevel || 'N/A'}</p>
                        <p class="text-sm"><strong>Archivos:</strong> ${count} seleccionado${count !== 1 ? 's' : ''}</p>
                    </div>
                </div>
            `;
        } else {
            const levelCounts = {};
            document.querySelectorAll('.multi-level-checkbox:checked').forEach(cb => {
                const level = cb.dataset.level;
                levelCounts[level] = (levelCounts[level] || 0) + 1;
            });
            
            const levelsInfo = Object.entries(levelCounts)
                .map(([level, count]) => `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">${level.chart(0).toUpperCase() + level.slice(1)}: ${count}</span>`)
                .join(' ');
            
            summaryContent.innerHTML = `
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm mb-2"><strong>Tipo:</strong> Comparación multi-nivel</p>
                        <p class="text-sm mb-2"><strong>Total:</strong> ${count} archivo${count !== 1 ? 's' : ''} seleccionado${count !== 1 ? 's' : ''}</p>
                        <div class="flex flex-wrap gap-2">${levelsInfo}</div>
                    </div>
                </div>
            `;
        }
        
        summaryElement.classList.remove('hidden');
    }

    // FUNCIÓN QUE FALTABA - validateChartForm
    function validateChartForm() {
        if (!selectedTemplate) {
            showNotification('Selecciona una plantilla', 'error');
            return false;
        }

        // ✅ CAMBIO: Validar según el tipo de plantilla
        let selectedFiles;
        if (selectedTemplate.level_type === 'single') {
            selectedFiles = document.querySelectorAll('.single-level-checkbox:checked');
            
            // Validar que se haya seleccionado un nivel
            const selectedLevel = document.querySelector('input[name="single_level_selection"]:checked');
            if (!selectedLevel) {
                showNotification('Selecciona un nivel educativo', 'error');
                return false;
            }
        } else {
            selectedFiles = document.querySelectorAll('.multi-level-checkbox:checked');
        }

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
        formData.append('template_id', selectedTemplate.id);
        formData.append('chart_title', document.getElementById('chart_title').value);
        formData.append('notes', document.getElementById('chart_notes').value || '');

        // ✅ CAMBIO: Seleccionar archivos según el tipo de plantilla
        let selectedFiles;
        if (selectedTemplate?.level_type === 'single') {
            selectedFiles = document.querySelectorAll('.single-level-checkbox:checked');
        } else {
            selectedFiles = document.querySelectorAll('.multi-level-checkbox:checked');
        }

        const assignedLevels = {};
        
        console.log('=== CREANDO FORM DATA ===');
        console.log('Template type:', selectedTemplate?.level_type);
        console.log('Selected files count:', selectedFiles.length);
        
        selectedFiles.forEach((input, index) => {
            formData.append('file_ids[]', input.value);
            
            // ✅ CORREGIR AQUÍ: Definir intendedLevel para AMBOS casos
            let intendedLevel;
            
            if (selectedTemplate?.level_type === 'single') {
                // Para single level, obtener el nivel seleccionado del radio button
                const selectedLevelRadio = document.querySelector('input[name="single_level_selection"]:checked');
                intendedLevel = selectedLevelRadio ? selectedLevelRadio.value.toLowerCase() : 'general';
                
                // Enviar el nivel seleccionado (solo una vez, fuera del loop)
                if (index === 0 && selectedLevelRadio) {
                    formData.append('selected_level', selectedLevelRadio.value);
                }
            } else {
                // Para multi-level, usar el nivel del dataset
                intendedLevel = input.dataset.intendedLevel || input.dataset.level;
            }
            
            // Asignar nivel con primera letra mayúscula
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