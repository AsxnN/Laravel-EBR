<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\charts\index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Plantillas de Gráficos') }}
            </h2>
            <a href="{{ route('charts.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Nueva Plantilla
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            @if($filesCount == 0)
                <!-- Mensaje cuando no hay archivos -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-6 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                No tienes archivos procesados
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Para usar las plantillas de gráficos, primero necesitas subir y procesar archivos Excel.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('files.create') }}" 
                                   class="text-sm font-medium text-yellow-800 underline hover:text-yellow-600">
                                    Subir primer archivo →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Plantillas de Gráficos Disponibles
                    </h1>
                    <p class="mt-2 text-gray-500 leading-relaxed">
                        Selecciona una plantilla para generar gráficos rápidamente con la configuración predefinida.
                    </p>
                </div>

                @if($templates->count() > 0)
                    <div class="p-6 lg:p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($templates as $template)
                                <div class="bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-200">
                                    <div class="p-6">
                                        <!-- Header de la plantilla -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
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
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                                    <p class="text-sm text-gray-500">{{ $template->chart_type_label }}</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activa
                                            </span>
                                        </div>

                                        <!-- Configuración del gráfico -->
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Configuración</h4>
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-500">Eje X:</span>
                                                    <span class="font-medium text-gray-900">{{ $template->x_axis_label }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Eje Y:</span>
                                                    <span class="font-medium text-gray-900">{{ $template->y_axis_label }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Descripción -->
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Descripción</h4>
                                            <p class="text-sm text-gray-600">{{ $template->description }}</p>
                                        </div>

                                        <!-- Para qué sirve -->
                                        <div class="mb-6">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2">¿Para qué sirve?</h4>
                                            <p class="text-sm text-gray-600">{{ $template->purpose }}</p>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                            <span>Creado por {{ $template->creator->name }}</span>
                                            <span>{{ $template->created_at->format('d/m/Y') }}</span>
                                        </div>

                                        <!-- Botón de acción -->
                                        <div class="flex space-x-2">
                                            @if($filesCount > 0)
                                                <a href="{{ route('charts.use-template', $template->id) }}" 
                                                   class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Usar Plantilla
                                                </a>
                                            @else
                                                <button disabled 
                                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    Sin archivos
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Información adicional -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Mostrando {{ $templates->count() }} plantilla(s) disponible(s)
                                </div>
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('files.index') }}" 
                                       class="text-sm text-indigo-600 hover:text-indigo-500">
                                        Gestionar archivos
                                    </a>
                                    <a href="{{ route('charts.create') }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Crear Plantilla
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Estado vacío -->
                    <div class="p-6 lg:p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay plantillas de gráficos</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Comienza creando tu primera plantilla de gráfico personalizada.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('charts.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Crear Primera Plantilla
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>