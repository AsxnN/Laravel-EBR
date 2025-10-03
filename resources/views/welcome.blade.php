<!-- filepath: c:\laragon\www\Laravel-EBR\resources\views\welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema EBR - Análisis y Reportes Educativos</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
        
        <!-- Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
            </style>
        @endif
    </head>
    <body class="bg-gray-50 text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h1 class="text-xl font-semibold text-gray-900">Sistema EBR</h1>
                                <p class="text-sm text-gray-500">Análisis y Reportes Educativos</p>
                            </div>
                        </div>
                        
                        @if (Route::has('login'))
                            <nav class="flex items-center space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                        Ir al Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition duration-150 ease-in-out">
                                        Iniciar Sesión
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" 
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                            Registrarse
                                        </a>
                                    @endif
                                @endauth
                            </nav>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Hero Section -->
                <div class="bg-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <div class="text-center">
                            <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                                Sistema de Análisis
                                <span class="text-blue-600">Educativo EBR</span>
                            </h1>
                            <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600">
                                Plataforma integral para el análisis de datos educativos, generación de reportes y visualización de métricas del sector educativo peruano.
                            </p>
                            <div class="mt-8 flex justify-center space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" 
                                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Acceder al Sistema
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Comenzar
                                    </a>
                                    <a href="#features" 
                                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Ver Características
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div id="features" class="bg-gray-50 py-16">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center">
                            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                                Características Principales
                            </h2>
                            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                                Herramientas profesionales para el análisis y reporte de datos educativos
                            </p>
                        </div>

                        <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Feature 1 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Carga de Archivos</h3>
                                <p class="text-gray-600">
                                    Sube y procesa archivos Excel con datos educativos de manera segura y eficiente.
                                </p>
                            </div>

                            <!-- Feature 2 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Gráficos Interactivos</h3>
                                <p class="text-gray-600">
                                    Genera visualizaciones profesionales con plantillas predefinidas para diferentes métricas educativas.
                                </p>
                            </div>

                            <!-- Feature 3 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reportes Completos</h3>
                                <p class="text-gray-600">
                                    Crea reportes detallados combinando múltiples gráficos y análisis para presentaciones ejecutivas.
                                </p>
                            </div>

                            <!-- Feature 4 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Análisis por Niveles</h3>
                                <p class="text-gray-600">
                                    Segmenta automáticamente los datos por niveles educativos: Inicial, Primaria y Secundaria.
                                </p>
                            </div>

                            <!-- Feature 5 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Seguridad de Datos</h3>
                                <p class="text-gray-600">
                                    Manejo seguro de información educativa sensible con autenticación y control de acceso.
                                </p>
                            </div>

                            <!-- Feature 6 -->
                            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Exportación</h3>
                                <p class="text-gray-600">
                                    Exporta gráficos y reportes en formatos PNG, SVG y PDF para uso en presentaciones.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                @guest
                <div class="bg-blue-600">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">
                            ¿Listo para comenzar?
                        </h2>
                        <p class="mt-4 text-xl text-blue-100">
                            Inicia sesión y comienza a analizar tus datos educativos hoy mismo.
                        </p>
                        <div class="mt-8 flex justify-center space-x-4">
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                                    Crear Cuenta
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endguest
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">
                            © {{ date('Y') }} Sistema EBR. Plataforma de análisis educativo.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>