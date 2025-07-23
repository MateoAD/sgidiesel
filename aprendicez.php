<?php
session_start();
require 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGS de Taller Diesel - Gestión de Aprendices</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Style -->
    <style>
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        body {
            background-image: url('./img/fondo_prestamo.png');
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
            z-index: -1;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background-color: #1f2937;
            z-index: 10;
        }

        .polymorphic-container {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        #modalSystem {
            z-index: 60; /* Incrementado para asegurar que esté por encima de reportModal */
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Modal System -->
    <div id="modalSystem" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-60">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div id="modalHeader" class="flex justify-between items-center border-b px-5 py-4">
                <div class="flex items-center">
                    <i id="modalIcon" class="text-2xl mr-3"></i>
                    <h3 id="modalTitle" class="text-lg font-semibold"></h3>
                </div>
                <button onclick="hideModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="p-5 text-gray-700"></div>
            <div id="modalActions" class="flex justify-end px-5 py-4 border-t"></div>
        </div>
    </div>

    <header class="bg-[#4A655D] text-white shadow-lg" style="background-color: #4A655D !important;">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="container mx-auto flex items-center px-4 py-3">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto mr-4">
                <div>
                    <h1 class="text-xl font-bold">APRENDICES</h1>
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
                            <a href="prestamos.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                               <i class="fas fa-hand-holding mr-3 text-[#05976A]"></i>  Prestamos
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

    <div id="reportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Reportes del Aprendiz</h3>
                <button onclick="closeReportModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="reportsList" class="max-h-96 overflow-y-auto">
                <!-- Reports will be loaded here -->
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-8">
        <!-- Panel Principal -->
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <div class="bg-gray-800 text-white text-center py-4 rounded-t-2xl -mt-6 -mx-6 mb-6">
                <h1 class="text-2xl font-bold">Gestión de Aprendices</h1>
            </div>

            <div class="p-6">
                <!-- Form añadir aprendices -->
                <form id="apprenticeForm" class="mb-8">
                    <div class="mb-4">
                        <label for="ficha" class="block text-gray-700 text-sm font-bold mb-2">Número de Ficha:</label>
                        <input type="text" id="ficha" name="ficha" required
                            class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                            placeholder="Ingrese el número de ficha">
                    </div>

                    <div id="apprenticesContainer">
                        <div class="apprentice-entry mb-4 flex gap-4">
                            <div class="flex-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nombre del Aprendiz:</label>
                                <input type="text" name="nombres[]" required
                                    class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                                    placeholder="Ingrese el nombre del aprendiz">
                            </div>
                            <button type="button" class="remove-apprentice text-red-500 self-end pb-2"
                                style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" id="addMore"
                        class="mb-4 text-[#00AF00] hover:text-[#4A655D] transition duration-300">
                        <i class="fas fa-plus mr-2"></i>Agregar otro aprendiz
                    </button>

                    <button type="submit" id="submitButton"
                        class="w-full bg-[#00AF00] text-white font-bold py-2 px-4 rounded hover:bg-[#4A655D] transition duration-300 flex justify-center items-center">
                        <span id="buttonText">Guardar Aprendices</span>
                        <div id="loadingSpinner" class="loading-spinner ml-2"></div>
                    </button>
                </form>

                <!-- Formulario para cargar archivo Excel -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Cargar Aprendices desde CSV</h3>
                    <p class="text-sm text-gray-600 mb-4">Sube un archivo CSV con los datos de los aprendices. El
                        archivo debe tener dos columnas: Nombre y Ficha.</p>

                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="text-md font-semibold text-blue-700 mb-2">¿Cómo convertir Excel a CSV?</h4>
                        <ol class="text-sm text-gray-700 list-decimal pl-5 space-y-1">
                            <li>Abre tu archivo Excel</li>
                            <li>Haz clic en "Archivo" > "Guardar como"</li>
                            <li>Selecciona "CSV (delimitado por comas) (*.csv)" en el tipo de archivo</li>
                            <li>Haz clic en "Guardar"</li>
                            <li>Confirma los mensajes que Excel muestre sobre la compatibilidad</li>
                        </ol>

                        <div class="mt-4 pt-3 border-t border-blue-200">
                            <a href="PLANTILLA EXCEL.xlsx" download
                                class="flex items-center text-blue-700 hover:text-blue-900 font-medium">
                                <i class="fas fa-file-excel mr-2"></i>
                                Descargar plantilla Excel
                                <i class="fas fa-download ml-2"></i>
                            </a>
                            <p class="text-xs text-gray-600 mt-1">Descarga esta plantilla y úsala como base para crear
                                tu archivo CSV.</p>
                        </div>
                    </div>

                    <form id="excelUploadForm" enctype="multipart/form-data" class="flex flex-col space-y-4">
                        <div class="flex items-center space-x-2">
                            <input type="file" id="excelFile" name="excelFile" accept=".csv" class="block w-full text-sm text-gray-500 
                   file:mr-4 file:py-2 file:px-4 
                   file:rounded-full file:border-0 
                   file:text-sm file:font-semibold 
                   file:bg-[#05976A] file:text-white 
                   hover:file:bg-[#4A655D]">
                        </div>

                        <button type="submit" id="uploadExcelButton"
                            class="w-full bg-[#00AF00] text-white font-bold py-2 px-4 rounded hover:bg-[#4A655D] transition duration-300 flex justify-center items-center">
                            <i class="fas fa-file-csv mr-2"></i>
                            <span id="uploadButtonText">Cargar CSV</span>
                            <div id="uploadLoadingSpinner" class="loading-spinner ml-2"></div>
                        </button>
                    </form>
                </div>

                <!-- Tabla de aprendices -->
                <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gray-800 text-white text-center py-4">
                        <h2 class="text-xl font-bold">Consulta de Aprendices por Ficha</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 flex items-end gap-4">
                            <div class="flex-1">
                                <label for="fichaSelect" class="block text-gray-700 text-sm font-bold mb-2">Seleccionar
                                    Ficha:</label>
                                <select id="fichaSelect"
                                    class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]">
                                    <option value="">Seleccione una ficha</option>
                                </select>
                            </div>
                            <button onclick="deleteFicha(document.getElementById('fichaSelect').value)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="fas fa-trash"></i>
                                Eliminar Ficha
                            </button>
                        </div>

                        <div class="table-container">
                            <table id="apprenticesTable" class="min-w-full bg-white rounded-lg overflow-hidden">
                                <thead class="bg-gray-800 text-white sticky-header">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Ficha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Reportes</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <!-- Apprentices will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Sistema de Modales
        function showModal(type, title, message, options = {}) {
            const modal = document.getElementById('modalSystem');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');
            const modalHeader = document.getElementById('modalHeader');
            const modalActions = document.getElementById('modalActions');

            if (modal.timeoutId) {
                clearTimeout(modal.timeoutId);
                delete modal.timeoutId;
            }

            switch(type) {
                case 'error':
                    modalIcon.className = 'fas fa-exclamation-triangle text-red-500';
                    modalHeader.className = 'flex justify-between items-center border-b border-red-100 px-5 py-4 bg-red-50';
                    break;
                case 'warning':
                    modalIcon.className = 'fas fa-exclamation-triangle text-orange-500';
                    modalHeader.className = 'flex justify-between items-center border-b border-orange-100 px-5 py-4 bg-orange-50';
                    break;
                case 'success':
                    modalIcon.className = 'fas fa-check-circle text-green-500';
                    modalHeader.className = 'flex justify-between items-center border-b border-green-100 px-5 py-4 bg-green-50';
                    break;
                case 'confirm':
                    modalIcon.className = 'fas fa-question-circle text-blue-500';
                    modalHeader.className = 'flex justify-between items-center border-b border-blue-100 px-5 py-4 bg-blue-50';
                    break;
                default: // info
                    modalIcon.className = 'fas fa-info-circle text-blue-500';
                    modalHeader.className = 'flex justify-between items-center border-b border-blue-100 px-5 py-4 bg-blue-50';
            }

            modalTitle.textContent = title;
            modalContent.innerHTML = message;

            modalActions.innerHTML = '';
            if (options.actions) {
                options.actions.forEach(action => {
                    const button = document.createElement('button');
                    button.textContent = action.text;
                    button.className = action.class || 'text-sm py-2 px-3 text-gray-500 hover:text-gray-600 transition duration-150';
                    button.onclick = () => {
                        if (action.handler) action.handler();
                        if (action.close !== false) hideModal();
                    };
                    modalActions.appendChild(button);
                });
            } else {
                modalActions.innerHTML = `
                    <button onclick="hideModal()" 
                            class="text-sm py-2 px-3 text-gray-500 hover:text-gray-600 transition duration-150">
                        Cerrar
                    </button>
                `;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const duration = options.duration || 6000;
            if (duration > 0 && !options.persistent) {
                modal.timeoutId = setTimeout(() => {
                    hideModal();
                }, Math.max(duration, 1000));
            }
        }

        function hideModal() {
            const modal = document.getElementById('modalSystem');
            if (modal.timeoutId) {
                clearTimeout(modal.timeoutId);
                delete modal.timeoutId;
            }
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function showError(message, title = 'Error', duration = 10000) {
            showModal('error', title, message, { duration: Math.max(duration, 10000) });
        }

        function showWarning(message, title = 'Advertencia', duration = 8000) {
            showModal('warning', title, message, { duration: Math.max(duration, 8000) });
        }

        function showInfo(message, title = 'Información', duration = 7000) {
            showModal('info', title, message, { duration: Math.max(duration, 7000) });
        }

        function showSuccess(message, title = 'Éxito', duration = 1000) {
            showModal('success', title, message, { duration: Math.max(duration, 1000) });
        }

        function showConfirm(message, title = 'Confirmar Acción', onConfirm) {
            showModal('confirm', title, message, {
                persistent: true,
                actions: [
                    {
                        text: 'Aceptar',
                        class: 'text-sm py-2 px-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150',
                        handler: onConfirm,
                        close: true
                    },
                    {
                        text: 'Cancelar',
                        class: 'text-sm py-2 px-3 text-gray-500 hover:text-gray-600 transition duration-150',
                        close: true
                    }
                ]
            });
        }

        // Funcionalidad para agregar más campos de aprendices
        document.getElementById('addMore').addEventListener('click', function () {
            const container = document.getElementById('apprenticesContainer');
            const newEntry = container.children[0].cloneNode(true);
            newEntry.querySelector('input').value = '';
            newEntry.querySelector('.remove-apprentice').style.display = 'block';
            container.appendChild(newEntry);

            newEntry.querySelector('.remove-apprentice').addEventListener('click', function () {
                newEntry.remove();
            });
        });

        // Guardar aprendices
        document.getElementById('apprenticeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const buttonText = document.getElementById('buttonText');
            const spinner = document.getElementById('loadingSpinner');

            // Collect all apprentice names
            const nombres = Array.from(document.getElementsByName('nombres[]'))
                .map(input => input.value.trim())
                .filter(nombre => nombre !== '');
            const ficha = document.getElementById('ficha').value.trim();

            // Validate inputs
            if (ficha === '' || nombres.length === 0) {
                showError('Por favor complete todos los campos requeridos', 'Error de Validación');
                return;
            }

            // Show loading state
            buttonText.textContent = 'GUARDANDO...';
            spinner.style.display = 'block';

            // Send data to server
            fetch('includes/save_apprentices.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ficha: ficha,
                    nombres: nombres
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showSuccess(`Aprendices guardados exitosamente. Se agregaron ${nombres.length} aprendices.`, 'Éxito');
                        // Actualizar la tabla si hay una ficha seleccionada
                        const fichaSelect = document.getElementById('fichaSelect');
                        if (fichaSelect.value) {
                            loadApprenticesByFicha(fichaSelect.value);
                        } else {
                            // Recargar las fichas para incluir la nueva
                            loadFichas();
                        }
                        // Reset form
                        document.getElementById('apprenticeForm').reset();
                        // Remove extra apprentice entries
                        const container = document.getElementById('apprenticesContainer');
                        while (container.children.length > 1) {
                            container.removeChild(container.lastChild);
                        }
                    } else {
                        throw new Error(data.message || 'Error al guardar los aprendices');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error al guardar los aprendices: ' + error.message, 'Error');
                })
                .finally(() => {
                    buttonText.textContent = 'Guardar Aprendices';
                    spinner.style.display = 'none';
                });
        });

        // Eliminar ficha
        function deleteFicha(ficha) {
            if (!ficha) {
                showError('Por favor seleccione una ficha para eliminar', 'Error de Selección');
                return;
            }

            showConfirm(
                '¿Está seguro de eliminar esta ficha completa? Esta acción no se puede deshacer.',
                'Confirmar Eliminación de Ficha',
                () => {
                    fetch('includes/delete_ficha.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ ficha: ficha })
                    })
                    .then(response => {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            return response.text().then(text => {
                                throw new Error('Respuesta no válida del servidor: ' + text);
                            });
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            showSuccess('Ficha eliminada correctamente', 'Éxito');
                            loadFichas();
                            document.querySelector('#apprenticesTable tbody').innerHTML = '';
                        } else {
                            throw new Error(data.message || 'Error al eliminar la ficha');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Error al eliminar la ficha: ' + error.message, 'Error');
                    });
                }
            );
        }

        // Cargar fichas
        function loadFichas() {
            fetch('includes/get_fichas.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('fichaSelect');
                    select.innerHTML = '<option value="">Seleccione una ficha</option>';
                    data.forEach(ficha => {
                        select.innerHTML += `<option value="${ficha}">${ficha}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading fichas:', error);
                    showError('Error al cargar las fichas: ' + error.message, 'Error');
                });
        }

        // Cargar aprendices por ficha
        function loadApprenticesByFicha(ficha) {
            fetch(`includes/get_apprentices_by_ficha.php?ficha=${ficha}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#apprenticesTable tbody');
                    tbody.innerHTML = '';

                    data.forEach(apprentice => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${apprentice.nombre}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${apprentice.ficha}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button onclick="showReports(${apprentice.id})" class="text-blue-600 hover:text-blue-900">
                                        ${apprentice.reportes} <i class="fas fa-eye ml-1"></i>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button onclick="deleteApprentice(${apprentice.id})" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error loading apprentices:', error);
                    showError('Error al cargar los aprendices: ' + error.message, 'Error');
                });
        }

        // Eliminar aprendiz
        function deleteApprentice(id) {
            showConfirm(
                '¿Está seguro de eliminar este aprendiz? Esta acción no se puede deshacer.',
                'Confirmar Eliminación de Aprendiz',
                () => {
                    fetch('includes/delete_apprentice.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess('Aprendiz eliminado correctamente', 'Éxito');
                            const fichaSelect = document.getElementById('fichaSelect');
                            if (fichaSelect.value) {
                                loadApprenticesByFicha(fichaSelect.value);
                            }
                        } else {
                            throw new Error(data.message || 'Error al eliminar el aprendiz');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Error al eliminar el aprendiz: ' + error.message, 'Error');
                    });
                }
            );
        }

        // Cargar archivo CSV
        document.getElementById('excelUploadForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const fileInput = document.getElementById('excelFile');
            const uploadButtonText = document.getElementById('uploadButtonText');
            const uploadSpinner = document.getElementById('uploadLoadingSpinner');

            // Verificar si se seleccionó un archivo
            if (!fileInput.files || fileInput.files.length === 0) {
                showError('Por favor seleccione un archivo', 'Error de Validación');
                return;
            }

            // Verificar la extensión del archivo
            const fileName = fileInput.files[0].name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            if (fileExt !== 'csv') {
                showError('Por favor seleccione un archivo CSV válido (.csv)', 'Error de Validación');
                return;
            }

            // Mostrar estado de carga
            uploadButtonText.textContent = 'CARGANDO...';
            uploadSpinner.style.display = 'block';

            // Crear FormData y enviar
            const formData = new FormData();
            formData.append('excelFile', fileInput.files[0]);

            fetch('includes/upload_excel_apprentices.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess(`Aprendices cargados exitosamente. Se importaron ${data.count} registros.`, 'Éxito');
                        // Actualizar la lista de fichas
                        loadFichas();
                        // Limpiar el formulario
                        document.getElementById('excelUploadForm').reset();
                    } else {
                        throw new Error(data.message || 'Error al cargar el archivo');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error al cargar el archivo: ' + error.message, 'Error');
                })
                .finally(() => {
                    uploadButtonText.textContent = 'Cargar CSV';
                    uploadSpinner.style.display = 'none';
                });
        });

        // Mostrar reportes
        function showReports(apprenticeId) {
            fetch(`includes/get_apprentice_reports.php?id=${apprenticeId}`)
                .then(response => response.json())
                .then(data => {
                    const reportsList = document.getElementById('reportsList');
                    reportsList.dataset.apprenticeId = apprenticeId; // Store apprentice ID
                    reportsList.innerHTML = data.length ? '' : '<p class="text-gray-500">No hay reportes registrados</p>';

                    data.forEach(report => {
                        reportsList.innerHTML += `
                            <div class="border-b border-gray-200 py-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-gray-600">Fecha: ${new Date(report.fecha_reporte).toLocaleString()}</p>
                                        <p class="text-sm mt-1">${report.observaciones}</p>
                                    </div>
                                    <button onclick="deleteReport(${report.id}, ${apprenticeId})" 
                                            class="text-red-600 hover:text-red-900 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });

                    document.getElementById('reportModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error al cargar los reportes: ' + error.message, 'Error');
                });
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
        }

        // Eliminar reporte
        function deleteReport(reportId, apprenticeId) {
            // Cerrar el modal de reportes antes de mostrar la confirmación
            closeReportModal();
            showConfirm(
                '¿Estás seguro de que deseas eliminar este reporte? Esta acción no se puede deshacer.',
                'Confirmar Eliminación de Reporte',
                () => {
                    fetch('includes/delete_report.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: reportId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess('Reporte eliminado correctamente', 'Éxito');
                            // Recargar los reportes
                            showReports(apprenticeId);
                            // Actualizar la tabla si hay una ficha seleccionada
                            const fichaSelect = document.getElementById('fichaSelect');
                            if (fichaSelect.value) {
                                loadApprenticesByFicha(fichaSelect.value);
                            }
                        } else {
                            throw new Error(data.message || 'Error al eliminar el reporte');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Error al eliminar el reporte: ' + error.message, 'Error');
                    });
                }
            );
        }

        // Initialize everything when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadFichas();

            // Add ficha selection event listener
            document.getElementById('fichaSelect').addEventListener('change', function () {
                if (this.value) {
                    loadApprenticesByFicha(this.value);
                } else {
                    document.querySelector('#apprenticesTable tbody').innerHTML = '';
                }
            });
        });
    </script>

    <footer class="bg-[#2D3A36] text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p>© <?= date('Y') ?> SENA - Sistema de Gestión de Inventarios. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>
