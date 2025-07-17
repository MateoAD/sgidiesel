<?php
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Préstamo - Sistema de Herramientas</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            background-image: url('./img/fondo_prestamo.png');
            /* Reemplaza con la ruta correcta de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            /* Color blanco con 70% de opacidad */
            z-index: -1;
        }

        /* Estilos Polymorphic */
        .polymorphic-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        .polymorphic-button {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.1);
            transition: all 0.3s ease;
        }

        .polymorphic-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(31, 38, 135, 0.15);
        }

        .polymorphic-input {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.05);
        }

        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }

        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }

        .autocomplete {
            position: relative;
        }

        .scanner-container {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .info-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .info-card-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .info-card-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
        }

        #interactive.viewport {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #interactive.viewport canvas,
        video {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        #interactive.viewport canvas.drawingBuffer {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        @media (max-width: 768px) {
            .scanner-container {
                height: 300px;
            }

            #interactive.viewport {
                height: 250px;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
<header class="bg-[#4A655D] text-white shadow-lg" style="background-color: #4A655D !important;">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="container mx-auto flex items-center px-4 py-3">
            <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto mr-4">
            <div>
                <h1 class="text-xl font-bold">PRESTAMOS</h1>
            </div>
        </div>
        
        <!-- Dropdown Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="bg-[#05976A] hover:bg-[#4A655D] px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center text-white">
                <i class="fas fa-bars mr-2"></i>
                <span>Menú</span>
            </button>
            
                <!-- Overlay -->
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="open = false"></div>
                
                <!-- Sidebar Menu con color claro -->
                <div x-show="open" class="fixed top-0 right-0 h-full w-64 bg-gray-50 shadow-xl z-50 transform transition-transform duration-300 ease-in-out"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    <div class="p-4 h-full flex flex-col">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Menú Principal</h2>
                            <button @click="open = false" class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="flex-grow overflow-y-auto space-y-2">
                            <a href="dashboard.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-home mr-3 text-[#05976A]"></i> Panel
                            </a>
                            <a href="inventario.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                 <i class="fas fa-box mr-3 text-[#05976A]"></i> Inventario
                            </a>
                            <a href="reporte.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-clipboard-list mr-3 text-[#05976A]"></i> Reportes
                            </a>
                            <a href="aprendicez.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-user-graduate mr-3 text-[#05976A]"></i> Aprendices
                            </a>
                        </div>
                        
                        <div class="border-t border-gray-200 mt-auto pt-4">
                            <a href="logout.php" class="block px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition duration-200">
                                <i class="fas fa-sign-out-alt mr-3"></i> Salir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    

    <!-- Contenido Principal -->
    <main class="container mx-auto px-4 py-8 max-w-6xl">
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-8"><i class="fas fa-hammer mr-2"></i>Agregar Préstamo</h1>

            <!-- Primera Fila: Escáner e Información de Herramienta -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Columna Izquierda - Escáner -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-barcode mr-2 text-blue-600"></i>
                        Escanear Herramienta
                    </h2>

                    <div class="scanner-container mb-4" id="scanner-container">
                        <div id="scanner-placeholder" class="text-center">
                            <i class="fas fa-qrcode text-5xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 mb-4">Enfoque el código QR de la herramienta</p>
                            <button id="btn-escanear"
                                class="polymorphic-button bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                                <i class="fas fa-camera mr-2"></i> Iniciar Escáner
                            </button>
                        </div>
                        <div id="scanner-area" class="hidden">
                            <div id="interactive" class="viewport w-full h-64"></div>
                            <button id="btn-cancelar-escaneo"
                                class="polymorphic-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg mt-4">
                                <i class="fas fa-times mr-2"></i> Cancelar Escaneo
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">O ingrese manualmente el
                            código:</label>
                        <input type="text" id="codigo-barras" class="polymorphic-input w-full px-4 py-3 text-center"
                            placeholder="Código QR">
                    </div>
                </div>

                <!-- Columna Derecha - Información de la Herramienta -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-tools mr-2 text-blue-600"></i>
                        Información de la Herramienta
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="info-card">
                            <p class="info-card-title">Herramienta</p>
                            <p id="herramienta-nombre" class="info-card-value">-</p>
                        </div>
                        <div class="info-card">
                            <p class="info-card-title">Foto</p>
                            <img id="herramienta-foto" src="" class="w-24 h-24 object-cover rounded-md" onerror="this.style.display='none'">
                        </div>
                        <div class="info-card">
                            <p class="info-card-title">Tipo</p>
                            <p id="herramienta-tipo" class="info-card-value">-</p>
                        </div>
                        <div class="info-card">
                            <p class="info-card-title">Stock</p>
                            <p id="herramienta-stock" class="info-card-value">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda Fila: Datos del Préstamo y Botón -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Columna Izquierda - Datos del Préstamo -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-user-graduate mr-2 text-blue-600"></i>
                        Datos del Préstamo
                    </h2>

                    <div class="space-y-4">
                        <div class="autocomplete">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Aprendiz:</label>
                            <input type="text" id="aprendiz-nombre" class="polymorphic-input w-full px-4 py-2"
                                placeholder="Buscar aprendiz...">
                            <div id="aprendiz-lista" class="autocomplete-items hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ficha:</label>
                            <input type="text" id="aprendiz-ficha"
                                class="polymorphic-input w-full px-4 py-2 bg-gray-100" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad a Prestar:</label>
                            <input type="number" id="cantidad-prestamo" min="1"
                                class="polymorphic-input w-full px-4 py-2" placeholder="Cantidad">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (Opcional):</label>
                            <textarea id="descripcion-prestamo" 
                                class="polymorphic-input w-full px-4 py-2" 
                                placeholder="Detalles adicionales del préstamo" 
                                rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Botón de Préstamo -->
                <div class="flex flex-col items-center justify-center space-y-4">
                    <button id="btn-prestar"
                        class="polymorphic-button bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white px-12 py-8 rounded-xl text-2xl flex flex-col items-center w-full h-full transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <div class="relative">
                            <i class="fas fa-tools mb-4 text-5xl text-amber-300"></i>
                            <i
                                class="fas fa-exchange-alt absolute -right-2 -top-2 bg-white text-green-600 rounded-full p-1 text-xs"></i>
                        </div>
                        <span class="font-semibold">Préstamo de Herramientas</span>
                        <span class="text-sm mt-2 opacity-80">Solicita herramientas disponibles</span>
                    </button>

                    <div class="mt-4 p-4 bg-blue-50 rounded-lg hidden w-full max-w-md" id="mensaje-prestamo">
                        <p class="text-blue-700 flex items-center">
                            <i class="fas fa-clipboard-check mr-3 text-xl"></i>
                            <span id="mensaje-texto" class="text-lg"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Variables globales
            let herramientaActual = null;
            let aprendizActual = null;
            let aprendices = [];
            let scannerIsRunning = false;
            let videoElement = null;
            let canvasElement = null;
            let canvasContext = null;
            // Elementos del DOM
            // Elementos del DOM
            const btnEscanear = document.getElementById('btn-escanear');
            const codigoBarras = document.getElementById('codigo-barras');
            const scannerContainer = document.querySelector('.scanner-container');
            const inputAprendiz = document.getElementById('aprendiz-nombre');
            const inputFicha = document.getElementById('aprendiz-ficha');
            const listaAprendices = document.getElementById('aprendiz-lista');
            const btnPrestar = document.getElementById('btn-prestar');
            const mensajePrestamo = document.getElementById('mensaje-prestamo');
            const mensajeTexto = document.getElementById('mensaje-texto');

            // Cargar lista de aprendices al iniciar
            cargarAprendices();

            // Evento para escanear herramienta
            btnEscanear.addEventListener('click', toggleScanner);
            codigoBarras.addEventListener('change', buscarPorCodigo);

            // Autocompletado para aprendices
            inputAprendiz.addEventListener('input', function () {
                const valor = this.value.toLowerCase();
                if (valor.length < 2) {
                    listaAprendices.classList.add('hidden');
                    return;
                }

                const resultados = aprendices.filter(aprendiz =>
                    aprendiz.nombre.toLowerCase().includes(valor)
                );

                mostrarResultadosAprendices(resultados);
            });

            // Función para cargar aprendices desde la BD
            async function cargarAprendices() {
                try {
                    const response = await fetch('includes/obtener_aprendices.php');
                    if (!response.ok) throw new Error('Error al cargar aprendices');

                    aprendices = await response.json();
                } catch (error) {
                    console.error('Error:', error);
                    mostrarMensaje('Error al cargar la lista de aprendices', 'error');
                }
            }

            // Función escaneo

            // Función para iniciar/detener el escáner
            function toggleScanner() {
                if (scannerIsRunning) {
                    stopScanner();
                    btnEscanear.innerHTML = '<i class="fas fa-camera mr-2"></i> Iniciar Escáner';
                } else {
                    startScanner();
                    btnEscanear.innerHTML = '<i class="fas fa-stop mr-2"></i> Detener Escáner';
                }
            }

            // Iniciar el escáner de código QR
            function startScanner() {
                // Preparar el contenedor para el escáner
                scannerContainer.innerHTML = `
                <div id="scanner-area" class="w-full h-full">
                    <video id="qr-video" class="w-full h-full"></video>
                    <canvas id="qr-canvas" class="hidden"></canvas>
                </div>
                <button id="btn-cancelar-escaneo" class="polymorphic-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg mt-4">
                    <i class="fas fa-times mr-2"></i> Cancelar Escaneo
                </button>
            `;

                // Configurar elementos
                videoElement = document.getElementById('qr-video');
                canvasElement = document.getElementById('qr-canvas');
                canvasContext = canvasElement.getContext('2d');

                // Configurar botón de cancelar
                document.getElementById('btn-cancelar-escaneo').addEventListener('click', stopScanner);

                // Solicitar acceso a la cámara
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                    .then(function (stream) {
                        videoElement.srcObject = stream;
                        videoElement.setAttribute('playsinline', true); // Requerido para iOS
                        videoElement.play();
                        scannerIsRunning = true;
                        requestAnimationFrame(tick);
                    })
                    .catch(function (error) {
                        console.error("Error al acceder a la cámara:", error);
                        mostrarMensaje("Error al iniciar la cámara: " + error.message, "error");
                    });

                function tick() {
                    if (!scannerIsRunning) return;

                    if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                        // Configurar el canvas con las dimensiones del video
                        canvasElement.height = videoElement.videoHeight;
                        canvasElement.width = videoElement.videoWidth;

                        // Dibujar el frame actual del video en el canvas
                        canvasContext.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

                        // Obtener los datos de imagen del canvas
                        const imageData = canvasContext.getImageData(0, 0, canvasElement.width, canvasElement.height);

                        // Procesar la imagen con jsQR
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        // Si se detecta un código QR
                        if (code) {
                            // Reproducir sonido de éxito (opcional)
                            const beepSound = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU...'); // Aquí iría el sonido en base64
                            beepSound.play().catch(e => console.log("No se pudo reproducir el sonido"));

                            // Detener el escáner después de una detección exitosa
                            stopScanner();

                            // Actualizar el campo de código de barras y buscar la herramienta
                            codigoBarras.value = code.data;
                            buscarPorCodigo();
                        }
                    }

                    // Continuar escaneando
                    if (scannerIsRunning) {
                        requestAnimationFrame(tick);
                    }
                }
            }

            // Detener el escáner
            function stopScanner() {
                if (scannerIsRunning) {
                    scannerIsRunning = false;

                    // Detener la transmisión de video
                    if (videoElement && videoElement.srcObject) {
                        const tracks = videoElement.srcObject.getTracks();
                        tracks.forEach(track => track.stop());
                        videoElement.srcObject = null;
                    }

                    // Restaurar el contenedor del escáner
                    scannerContainer.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-qrcode text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 mb-4">Enfoque el código QR de la herramienta</p>
                        <button id="btn-escanear" class="polymorphic-button bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                            <i class="fas fa-camera mr-2"></i> Iniciar Escáner
                        </button>
                    </div>
                `;

                    // Volver a asignar el evento al nuevo botón
                    document.getElementById('btn-escanear').addEventListener('click', toggleScanner);
                }
            }
            // Función para buscar herramienta por código
            async function buscarPorCodigo() {
                const codigo = codigoBarras.value.trim();
                if (!codigo) return;

                try {
                    const response = await fetch(`includes/buscar_herramienta.php?codigo=${codigo}`);
                    if (!response.ok) throw new Error('Herramienta no encontrada');

                    herramientaActual = await response.json();
                    mostrarDatosHerramienta();
                    mostrarMensaje('Herramienta escaneada correctamente', 'success');
                } catch (error) {
                    console.error('Error:', error);
                    mostrarMensaje(error.message, 'error');
                    limpiarDatosHerramienta();
                }
            }

            // Mostrar datos de la herramienta escaneada
            function mostrarDatosHerramienta() {
                document.getElementById('herramienta-nombre').textContent = herramientaActual.nombre || '-';
                const imgElement = document.getElementById('herramienta-foto');
                if (herramientaActual.foto) {
                    imgElement.src = 'uploads/herramientas/' + herramientaActual.foto;
                    imgElement.style.display = 'block';
                } else {
                    imgElement.style.display = 'none';
                }
                document.getElementById('herramienta-tipo').textContent = herramientaActual.tipo === 'consumible' ? 'Consumible' : 'No Consumible';
                document.getElementById('herramienta-stock').textContent = herramientaActual.cantidad || '0';

                // Actualizar cantidad máxima a prestar
                const inputCantidad = document.getElementById('cantidad-prestamo');
                inputCantidad.max = herramientaActual.cantidad;
                inputCantidad.placeholder = `Máximo: ${herramientaActual.cantidad}`;
            }

            // Limpiar datos de herramienta
            function limpiarDatosHerramienta() {
                document.getElementById('herramienta-nombre').textContent = '-';
                const imgElement = document.getElementById('herramienta-foto');
    imgElement.src = '';
    imgElement.style.display = 'none';
                document.getElementById('herramienta-tipo').textContent = '-';
                document.getElementById('herramienta-stock').textContent = '-';
                document.getElementById('cantidad-prestamo').removeAttribute('max');
                document.getElementById('cantidad-prestamo').placeholder = 'Cantidad';

                herramientaActual = null;
            }

            // Mostrar resultados de búsqueda de aprendices
            function mostrarResultadosAprendices(resultados) {
                listaAprendices.innerHTML = '';

                if (resultados.length === 0) {
                    listaAprendices.innerHTML = '<div class="p-2 text-gray-500">No se encontraron aprendices</div>';
                } else {
                    resultados.forEach(aprendiz => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-blue-50';
                        div.innerHTML = `
                            <div class="font-medium">${aprendiz.nombre}</div>
                            <div class="text-sm text-gray-500">Ficha: ${aprendiz.ficha}</div>
                        `;
                        div.addEventListener('click', function () {
                            seleccionarAprendiz(aprendiz);
                        });
                        listaAprendices.appendChild(div);
                    });
                }

                listaAprendices.classList.remove('hidden');
            }

            // Seleccionar aprendiz de la lista
            function seleccionarAprendiz(aprendiz) {
                aprendizActual = aprendiz;
                inputAprendiz.value = aprendiz.nombre;
                inputFicha.value = aprendiz.ficha;
                listaAprendices.classList.add('hidden');
                mostrarMensaje(`Aprendiz seleccionado: ${aprendiz.nombre}`, 'success');
            }

            // Registrar préstamo
            btnPrestar.addEventListener('click', async function () {
                if (!validarPrestamo()) return;

                try {
                    btnPrestar.disabled = true;
                    btnPrestar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';

                    const payload = {
                        herramienta_id: herramientaActual.id,
                        herramienta_tipo: herramientaActual.tipo,
                        aprendiz_id: aprendizActual.id,
                        cantidad: document.getElementById('cantidad-prestamo').value,
                        descripcion: document.getElementById('descripcion-prestamo').value
                    };

                    const response = await fetch('includes/registrar_prestamo.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Respuesta inesperada: ${text.substring(0, 100)}...`);
                    }

                    const resultado = await response.json();

                    if (!response.ok || !resultado.success) {
                        throw new Error(resultado.error || resultado.message || 'Error desconocido al registrar préstamo');
                    }

                    mostrarMensaje(resultado.message, 'success');
                    limpiarFormulario();

                } catch (error) {
                    console.error('Error en el registro:', error);
                    mostrarMensaje(error.message, 'error');

                } finally {
                    btnPrestar.disabled = false;
                    btnPrestar.innerHTML = `
            <div class="relative">
                <i class="fas fa-tools mb-4 text-5xl text-amber-300"></i>
                <i class="fas fa-exchange-alt absolute -right-2 -top-2 bg-white text-green-600 rounded-full p-1 text-xs"></i>
            </div>
            <span class="font-semibold">Préstamo de Herramientas</span>
            <span class="text-sm mt-2 opacity-80">Solicita herramientas disponibles</span>
        `;
                }
            });

            // Validar datos del préstamo
            function validarPrestamo() {
                if (!herramientaActual) {
                    mostrarMensaje('Por favor escanee una herramienta primero', 'error');
                    return false;
                }

                if (!aprendizActual) {
                    mostrarMensaje('Por favor seleccione un aprendiz', 'error');
                    return false;
                }

                const cantidad = parseInt(document.getElementById('cantidad-prestamo').value);
                if (isNaN(cantidad)) {
                    mostrarMensaje('Por favor ingrese una cantidad válida', 'error');
                    return false;
                }

                if (cantidad > herramientaActual.cantidad) {
                    mostrarMensaje('No hay suficiente stock disponible', 'error');
                    return false;
                }

                return true;
            }


            // Mostrar mensajes al usuario
            function mostrarMensaje(texto, tipo = 'info') {
                mensajeTexto.textContent = texto;
                mensajePrestamo.className = `mt-6 p-4 rounded-lg flex items-center ${tipo === 'error' ? 'bg-red-50 text-red-700' : tipo === 'success' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'}`;
                mensajePrestamo.classList.remove('hidden');

                // Ocultar mensaje después de 5 segundos
                setTimeout(() => {
                    mensajePrestamo.classList.add('hidden');
                }, 5000);
            }

            // Limpiar formulario después de préstamo
           // Función para limpiar el formulario después de un préstamo exitoso
           function limpiarFormulario() {
                // Limpiar información de herramienta
                herramientaActual = null;
                document.getElementById('herramienta-nombre').textContent = '-';
                const imgElement = document.getElementById('herramienta-foto');
    imgElement.src = '';
    imgElement.style.display = 'none';
                document.getElementById('herramienta-tipo').textContent = '-';
                document.getElementById('herramienta-stock').textContent = '-';
                document.getElementById('codigo-barras').value = '';

                // Limpiar información de aprendiz
                aprendizActual = null;
                document.getElementById('aprendiz-nombre').value = '';
                document.getElementById('aprendiz-ficha').value = '';
                document.getElementById('cantidad-prestamo').value = '';
                document.getElementById('descripcion-prestamo').value = '';
            }
        });
    </script>
</body>

</html>