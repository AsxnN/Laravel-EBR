<!-- filepath: c:\laragon\www\EBR\resources\views\files\upload.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subir Nuevo Archivo') }}
            </h2>
            <a href="{{ route('files.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Archivos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Información y guía -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Información importante
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Selecciona el tipo de documento educativo correcto (Inicial, Primaria o Secundaria)</li>
                                <li>Los archivos deben estar en formato Excel (.xlsx, .xls) o CSV</li>
                                <li>Tamaño máximo permitido: 10MB</li>
                                <li>El sistema procesará automáticamente los datos y creará los registros correspondientes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de subida -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">
                        Seleccionar Archivo y Configuración
                    </h3>

                    <form id="uploadForm" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Tipo de documento -->
                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Documento Educativo *
                            </label>
                            <select name="document_type" id="document_type" required 
                                    class="w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Seleccionar tipo de documento</option>
                                <option value="inicial">Educación Inicial</option>
                                <option value="primaria">Educación Primaria</option>
                                <option value="secundaria">Educación Secundaria</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                Selecciona el nivel educativo correspondiente al archivo que vas a subir
                            </p>
                        </div>
                        
                        <!-- Archivo Excel -->
                        <div>
                            <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Archivo Excel *
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="excel_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Subir un archivo</span>
                                            <input id="excel_file" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                                        </label>
                                        <p class="pl-1">o arrastra y suelta</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Excel, CSV hasta 10MB
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Vista previa del archivo seleccionado -->
                            <div id="file-preview" class="mt-3 hidden">
                                <div class="flex items-center p-3 bg-gray-50 rounded-md">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div class="ml-3 flex-1">
                                        <p id="file-name" class="text-sm font-medium text-gray-900"></p>
                                        <p id="file-size" class="text-sm text-gray-500"></p>
                                    </div>
                                    <button type="button" onclick="clearFile()" class="ml-3 text-gray-400 hover:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('files.index') }}" 
                               class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancelar
                            </a>
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="submitText" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Subir y Procesar Archivo
                                </span>
                                <span id="loadingText" class="hidden flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando archivo...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Progreso del procesamiento -->
            <div id="progress-section" class="hidden mt-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Progreso del Procesamiento
                        </h3>
                        <div class="space-y-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <div id="progress-text" class="text-sm text-gray-600 text-center">
                                Iniciando procesamiento...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resultado del procesamiento -->
            <div id="result-section" class="hidden mt-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <div id="result-content">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('excel_file');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const uploadForm = document.getElementById('uploadForm');

            // Manejar selección de archivo
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    showFilePreview(file);
                }
            });

            // Drag and drop
            const dropArea = fileInput.closest('.border-dashed');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropArea.classList.add('border-indigo-500', 'border-2');
            }

            function unhighlight(e) {
                dropArea.classList.remove('border-indigo-500', 'border-2');
            }

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files;
                    showFilePreview(files[0]);
                }
            }

            function showFilePreview(file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.remove('hidden');
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Envío del formulario
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validaciones
                const documentType = document.getElementById('document_type').value;
                const file = fileInput.files[0];
                
                if (!documentType) {
                    alert('Por favor seleccione el tipo de documento');
                    return;
                }
                
                if (!file) {
                    alert('Por favor seleccione un archivo');
                    return;
                }
                
                // Validar tipo de archivo
                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                    'text/csv'
                ];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Por favor seleccione un archivo Excel válido (.xlsx, .xls, .csv)');
                    return;
                }
                
                if (file.size > 10240 * 1024) { // 10MB
                    alert('El archivo es demasiado grande. Máximo 10MB permitido.');
                    return;
                }
                
                // Iniciar procesamiento
                startProcessing();
            });

            function startProcessing() {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const loadingText = document.getElementById('loadingText');
                const progressSection = document.getElementById('progress-section');
                const resultSection = document.getElementById('result-section');
                
                // Mostrar estado de carga
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                progressSection.classList.remove('hidden');
                resultSection.classList.add('hidden');
                
                // Simular progreso
                simulateProgress();
                
                const formData = new FormData(uploadForm);
                
                fetch('{{ route("files.upload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('La respuesta del servidor no es JSON válido');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showSuccessResult(data);
                    } else {
                        showErrorResult(data.message || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    showErrorResult('Error al procesar el archivo: ' + error.message);
                })
                .finally(() => {
                    // Restablecer botón
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                });
            }

            function simulateProgress() {
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                let progress = 0;
                
                const steps = [
                    'Validando archivo...',
                    'Leyendo datos de Excel...',
                    'Procesando instituciones...',
                    'Guardando registros...',
                    'Finalizando...'
                ];
                
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    
                    progressBar.style.width = progress + '%';
                    
                    const stepIndex = Math.floor((progress / 100) * steps.length);
                    if (stepIndex < steps.length) {
                        progressText.textContent = steps[stepIndex];
                    }
                    
                    if (progress >= 90) {
                        clearInterval(interval);
                    }
                }, 500);
            }

            function showSuccessResult(data) {
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const resultSection = document.getElementById('result-section');
                const resultContent = document.getElementById('result-content');
                
                // Completar progreso
                progressBar.style.width = '100%';
                progressText.textContent = 'Procesamiento completado exitosamente';
                
                // Mostrar resultados
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">¡Archivo procesado exitosamente!</h3>
                        <div class="text-sm text-gray-600 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-md mx-auto">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">${data.summary.institutions}</div>
                                    <div class="text-xs text-blue-700">Instituciones</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">${data.summary.students.toLocaleString()}</div>
                                    <div class="text-xs text-green-700">Estudiantes</div>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">${data.summary.total_records}</div>
                                    <div class="text-xs text-purple-700">Registros</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('files.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Ver Todos los Archivos
                            </a>
                            <button onclick="resetForm()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Subir Otro Archivo
                            </button>
                        </div>
                    </div>
                `;
                
                resultSection.classList.remove('hidden');
            }

            function showErrorResult(message) {
                const resultSection = document.getElementById('result-section');
                const resultContent = document.getElementById('result-content');
                
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Error al procesar el archivo</h3>
                        <p class="text-sm text-gray-600 mb-6">${message}</p>
                        <div class="flex justify-center space-x-4">
                            <button onclick="resetForm()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Intentar Nuevamente
                            </button>
                            <a href="{{ route('files.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Volver a Archivos
                            </a>
                        </div>
                    </div>
                `;
                
                resultSection.classList.remove('hidden');
            }

            window.resetForm = function() {
                uploadForm.reset();
                filePreview.classList.add('hidden');
                document.getElementById('progress-section').classList.add('hidden');
                document.getElementById('result-section').classList.add('hidden');
                document.getElementById('progress-bar').style.width = '0%';
            }

            window.clearFile = function() {
                fileInput.value = '';
                filePreview.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>