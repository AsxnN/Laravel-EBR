<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\charts\create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Nueva Plantilla de Gráfico') }}
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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Nueva Plantilla de Gráfico
                    </h1>
                    <p class="mt-2 text-gray-500 leading-relaxed">
                        Define la configuración de tu plantilla para reutilizarla fácilmente en el módulo de reportes.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <form id="templateForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información básica -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Plantilla *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       required
                                       placeholder="Ej: Matrícula por UGEL"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Nombre descriptivo para identificar fácilmente la plantilla</p>
                            </div>

                            <div>
                                <label for="chart_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Gráfico *
                                </label>
                                <select id="chart_type" name="chart_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="column">Gráfico de Columnas</option>
                                    <option value="bar">Gráfico de Barras</option>
                                    <option value="line">Gráfico de Líneas</option>
                                    <option value="pie">Gráfico Circular</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Tipo de visualización que mejor represente tus datos</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción *
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3" 
                                          required
                                          placeholder="Describe qué muestra este gráfico..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Explica brevemente qué información visualiza esta plantilla</p>
                            </div>

                            <!-- Configuración de ejes -->
                            <div class="md:col-span-2 mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Ejes</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                <strong>Eje X:</strong> Categorías o agrupaciones (departamento, UGEL, etc.)
                                                <br>
                                                <strong>Eje Y:</strong> Valores numéricos (estudiantes matriculados, instituciones, etc.)
                                                <br>
                                                <strong>Leyenda:</strong> Siempre será por nivel educativo (Inicial, Primaria, Secundaria)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="x_axis" class="block text-sm font-medium text-gray-700 mb-2">
                                    Eje X (Categorías) *
                                </label>
                                <select id="x_axis" name="x_axis" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($axisOptions['x_axis'] as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Campo que se usará para agrupar los datos</p>
                            </div>

                            <div>
                                <label for="y_axis" class="block text-sm font-medium text-gray-700 mb-2">
                                    Eje Y (Valores) *
                                </label>
                                <select id="y_axis" name="y_axis" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar...</option>
                                    @foreach($axisOptions['y_axis'] as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Campo numérico que se mostrará en el eje vertical</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                    ¿Para qué sirve este gráfico? *
                                </label>
                                <textarea id="purpose" 
                                          name="purpose" 
                                          rows="3" 
                                          required
                                          placeholder="Explica la utilidad y casos de uso de este gráfico..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Describe los casos de uso y el valor que aporta esta visualización</p>
                            </div>
                        </div>

                        <!-- Vista previa de configuración -->
                        <div id="previewConfig" class="mt-8 p-4 bg-gray-50 rounded-lg hidden">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Vista Previa de Configuración</h4>
                            <div id="configSummary" class="text-sm text-gray-600">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Ejemplo visual -->
                        <div id="exampleSection" class="mt-8 p-4 bg-green-50 rounded-lg hidden">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Ejemplo de uso</h4>
                            <div id="exampleText" class="text-sm text-green-700">
                                <!-- Se llena dinámicamente con ejemplos -->
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <a href="{{ route('charts.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    id="saveBtn"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Plantilla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('templateForm');
            const saveBtn = document.getElementById('saveBtn');
            const previewConfig = document.getElementById('previewConfig');
            const configSummary = document.getElementById('configSummary');
            const exampleSection = document.getElementById('exampleSection');
            const exampleText = document.getElementById('exampleText');

            // Mapeo de etiquetas
            const axisLabels = {
                x_axis: @json($axisOptions['x_axis']),
                y_axis: @json($axisOptions['y_axis'])
            };

            const chartTypeLabels = {
                'column': 'Gráfico de Columnas',
                'bar': 'Gráfico de Barras',
                'line': 'Gráfico de Líneas',
                'pie': 'Gráfico Circular'
            };

            // Ejemplos de uso por combinación
            const examples = {
                'ugel_total_matriculados': 'Útil para comparar la cantidad de estudiantes matriculados entre diferentes UGELs, segmentado por nivel educativo. Ideal para identificar UGELs con mayor demanda educativa.',
                'departamento_total_matriculados': 'Permite visualizar la distribución de estudiantes matriculados a nivel departamental, facilitando la planificación de recursos a nivel regional.',
                'distrito_total_matriculados': 'Muestra la concentración de estudiantes por distrito, útil para la asignación de presupuesto y recursos educativos locales.',
                'ugel_total_secciones': 'Compara la cantidad de secciones disponibles entre UGELs, ayudando a identificar necesidades de infraestructura educativa.',
                'departamento_nomina_aprobada': 'Visualiza el avance en la aprobación de nóminas por departamento, útil para el seguimiento de procesos administrativos.',
                'provincia_dni_validado': 'Muestra el progreso en la validación de DNI por provincia, importante para el control de calidad de datos.',
                'ugel_matricula_proceso': 'Identifica UGELs con mayor cantidad de matrículas en proceso, útil para priorizar soporte técnico.',
                'distrito_total_grados': 'Compara la oferta educativa por grados entre distritos, ayudando en la planificación curricular.'
            };

            // Actualizar vista previa cuando cambien los campos
            ['x_axis', 'y_axis', 'chart_type'].forEach(fieldId => {
                document.getElementById(fieldId).addEventListener('change', updatePreview);
            });

            function updatePreview() {
                const xAxis = document.getElementById('x_axis').value;
                const yAxis = document.getElementById('y_axis').value;
                const chartType = document.getElementById('chart_type').value;

                if (xAxis && yAxis && chartType) {
                    const summary = `
                        <strong>${chartTypeLabels[chartType]}</strong> que muestra 
                        <strong>${axisLabels.y_axis[yAxis]}</strong> agrupado por 
                        <strong>${axisLabels.x_axis[xAxis]}</strong>, segmentado por nivel educativo (Inicial, Primaria, Secundaria).
                    `;
                    configSummary.innerHTML = summary;
                    previewConfig.classList.remove('hidden');

                    // Mostrar ejemplo si existe
                    const exampleKey = `${xAxis}_${yAxis}`;
                    if (examples[exampleKey]) {
                        exampleText.innerHTML = examples[exampleKey];
                        exampleSection.classList.remove('hidden');
                    } else {
                        exampleSection.classList.add('hidden');
                    }
                } else {
                    previewConfig.classList.add('hidden');
                    exampleSection.classList.add('hidden');
                }
            }

            // Validación en tiempo real
            function validateForm() {
                const name = document.getElementById('name').value.trim();
                const description = document.getElementById('description').value.trim();
                const purpose = document.getElementById('purpose').value.trim();
                const xAxis = document.getElementById('x_axis').value;
                const yAxis = document.getElementById('y_axis').value;
                const chartType = document.getElementById('chart_type').value;

                const isValid = name && description && purpose && xAxis && yAxis && chartType;
                saveBtn.disabled = !isValid;
                
                if (isValid) {
                    saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            // Escuchar cambios en todos los campos
            ['name', 'description', 'purpose', 'x_axis', 'y_axis', 'chart_type'].forEach(fieldId => {
                document.getElementById(fieldId).addEventListener('input', validateForm);
                document.getElementById(fieldId).addEventListener('change', validateForm);
            });

            // Validación inicial
            validateForm();

            // Manejar envío del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Guardando...';

                fetch('{{ route("charts.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        const successAlert = document.createElement('div');
                        successAlert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                        successAlert.innerHTML = `
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">Plantilla creada exitosamente</p>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(successAlert);

                        // Redireccionar después de 2 segundos
                        setTimeout(() => {
                            window.location.href = '{{ route("charts.index") }}';
                        }, 2000);
                    } else {
                        alert('Error: ' + data.message);
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Guardar Plantilla';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar la plantilla');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Guardar Plantilla';
                });
            });
        });
    </script>
</x-app-layout>