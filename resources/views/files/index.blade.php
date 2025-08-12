<!-- filepath: c:\laragon\www\EBR\resources\views\files\index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Archivos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Botón para subir archivo - Ahora redirige -->
            <div class="mb-6">
                <a href="{{ route('files.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Subir Nuevo Archivo
                </a>
            </div>

            <!-- Lista de archivos por mes -->
            @forelse($filesByMonth as $monthYear => $files)
                @php
                    $monthName = \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->translatedFormat('F Y');
                @endphp
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $monthName }} ({{ $files->count() }} archivos)
                        </h3>
                    </div>

                    <div class="bg-gray-50">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Archivo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Instituciones
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estudiantes
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tamaño
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($files as $file)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $file->original_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            Subido por {{ $file->user->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($file->document_type == 'inicial') bg-blue-100 text-blue-800 
                                                    @elseif($file->document_type == 'primaria') bg-green-100 text-green-800 
                                                    @else bg-purple-100 text-purple-800 @endif">
                                                    {{ ucfirst($file->document_type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $file->total_institutions ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($file->total_students ?? 0) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $file->uploaded_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $file->formatted_size }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="viewFileDetails({{ $file->id }})" 
                                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Ver Detalles
                                                </button>
                                                <button onclick="viewAllData({{ $file->id }})" 
                                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                                    Ver Datos
                                                </button>
                                                <a href="{{ route('files.download', $file->id) }}" 
                                                   class="text-green-600 hover:text-green-900 mr-3">
                                                    Descargar
                                                </a>
                                                <a href="{{ route('files.export', $file->id) }}" 
                                                   class="text-purple-600 hover:text-purple-900 mr-3">
                                                    Exportar Procesado
                                                </a>
                                                <button onclick="deleteFile({{ $file->id }})" 
                                                        class="text-red-600 hover:text-red-900">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay archivos</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza subiendo tu primer archivo Excel.</p>
                        <div class="mt-6">
                            <a href="{{ route('files.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Subir Archivo
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Mantener solo los modales de detalles y datos (quitar el modal de upload) -->
    <!-- Modal para ver detalles del archivo -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <!-- Contenido del modal de detalles igual que antes -->
    </div>

    <!-- Modal para ver todos los datos -->
    <div id="dataModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Datos Completos del Archivo</h3>
                    <button onclick="closeDataModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Información del archivo -->
                <div id="data-file-info" class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <!-- Se carga dinámicamente -->
                </div>
                
                <!-- Controles de paginación superior -->
                <div id="data-pagination-top" class="mb-4 flex justify-between items-center">
                    <!-- Se carga dinámicamente -->
                </div>
                
                <!-- Tabla de datos -->
                <div class="overflow-x-auto max-h-96 border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead id="data-table-header" class="bg-gray-50 sticky top-0">
                            <!-- Se carga dinámicamente -->
                        </thead>
                        <tbody id="data-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Controles de paginación inferior -->
                <div id="data-pagination-bottom" class="mt-4 flex justify-between items-center">
                    <!-- Se carga dinámicamente -->
                </div>
                
                <!-- Loading indicator -->
                <div id="data-loading" class="hidden text-center py-4">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-gray-600">Cargando datos...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mantener solo las funciones de los modales de detalles y datos
        // Remover todas las funciones relacionadas con uploadModal
        
        function deleteFile(fileId) {
            if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
                fetch(`/files/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el archivo');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el archivo');
                });
            }
        }

        function viewFileDetails(fileId) {
            // Esta función la implementas según necesites para mostrar detalles
            console.log('Ver detalles del archivo:', fileId);
        }

        function closeDataModal() {
            document.getElementById('dataModal').classList.add('hidden');
        }

        let currentFileId = null;
        let currentPage = 1;

        function viewAllData(fileId, page = 1) {
            currentFileId = fileId;
            currentPage = page;
            
            document.getElementById('dataModal').classList.remove('hidden');
            document.getElementById('data-loading').classList.remove('hidden');
            
            fetch(`/files/${fileId}/data?page=${page}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayFileData(data);
                } else {
                    alert('Error al cargar datos: ' + data.message);
                    closeDataModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar datos del archivo: ' + error.message);
                closeDataModal();
            })
            .finally(() => {
                document.getElementById('data-loading').classList.add('hidden');
            });
        }

        function displayFileData(data) {
            // Mostrar información del archivo
            document.getElementById('data-file-info').innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium">${data.file_info.name}</h4>
                        <p class="text-sm text-gray-600">Tipo: ${data.file_info.type} | Subido: ${data.file_info.uploaded_at}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm"><strong>Total registros:</strong> ${data.pagination.total_records}</p>
                        <p class="text-sm"><strong>Página:</strong> ${data.pagination.current_page} de ${data.pagination.total_pages}</p>
                    </div>
                </div>
            `;
            
            // Crear encabezados de tabla
            const headerRow = data.headers.map(header => 
                `<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">${header}</th>`
            ).join('');
            document.getElementById('data-table-header').innerHTML = `<tr>${headerRow}</tr>`;
            
            // Crear filas de datos
            const tableRows = data.data.map((row, index) => {
                const cells = row.map(cell => 
                    `<td class="px-3 py-2 text-xs text-gray-900 border-r border-gray-200 max-w-xs truncate" title="${cell || ''}">${cell || '-'}</td>`
                ).join('');
                return `<tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">${cells}</tr>`;
            }).join('');
            document.getElementById('data-table-body').innerHTML = tableRows;
            
            // Crear controles de paginación
            const paginationHtml = createPaginationControls(data.pagination);
            document.getElementById('data-pagination-top').innerHTML = paginationHtml;
            document.getElementById('data-pagination-bottom').innerHTML = paginationHtml;
        }

        function createPaginationControls(pagination) {
            const prevDisabled = !pagination.has_prev ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200 cursor-pointer';
            const nextDisabled = !pagination.has_next ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200 cursor-pointer';
            
            return `
                <div class="flex items-center space-x-2">
                    <button onclick="${pagination.has_prev ? `viewAllData(${currentFileId}, ${pagination.current_page - 1})` : ''}" 
                            class="px-3 py-1 text-sm border rounded ${prevDisabled}">
                        Anterior
                    </button>
                    <span class="text-sm text-gray-600">
                        ${pagination.current_page} de ${pagination.total_pages}
                    </span>
                    <button onclick="${pagination.has_next ? `viewAllData(${currentFileId}, ${pagination.current_page + 1})` : ''}" 
                            class="px-3 py-1 text-sm border rounded ${nextDisabled}">
                        Siguiente
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    Mostrando ${(pagination.current_page - 1) * pagination.per_page + 1} - 
                    ${Math.min(pagination.current_page * pagination.per_page, pagination.total_records)} 
                    de ${pagination.total_records} registros
                </div>
            `;
        }
    </script>
</x-app-layout>