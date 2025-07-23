<?php
require_once 'includes/database.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Taller Diesel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('./img/fondo_inventario.png');
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

        .card-option {
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .card-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-option.selected {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }

        /* Efectos mejorados para las tarjetas */
        .polymorphic-card {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 2px solid transparent;
            backdrop-filter: blur(8px);
        }

        .polymorphic-card.selected {
            border-color: currentColor;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background-color: rgba(239, 246, 255, 0.7) !important;
        }

        .polymorphic-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .polymorphic-card .rounded-full {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .polymorphic-card:hover .rounded-full {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .table-wrapper {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Row background colors */
        .row-no-consumible {
            background-color: rgba(191, 219, 254, 0.99);
            transition: all 0.3s ease;
        }

        .row-no-consumible:hover {
            background-color: rgba(147, 197, 253, 0.99);
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.15);
        }

        .row-consumible-lleno {
            background-color: rgba(220, 252, 231, 0.5);
            transition: all 0.3s ease;
        }

        .row-consumible-lleno:hover {
            background-color: rgba(187, 247, 208, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(34, 197, 94, 0.15);
        }

        .row-consumible-medio {
            background-color: rgba(254, 249, 195, 0.5);
            transition: all 0.3s ease;
        }

        .row-consumible-medio:hover {
            background-color: rgba(254, 240, 138, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(234, 179, 8, 0.15);
        }

        .row-consumible-recargar {
            background-color: rgba(254, 226, 226, 0.5);
            transition: all 0.3s ease;
        }

        .row-consumible-recargar:hover {
            background-color: rgba(254, 202, 202, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.15);
        }

        /* Modal styles */
        .modal-content {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #reservaModal {
            z-index: 50;
        }

        .modal-content {
            z-index: 51;
        }

        /* Button styles */
        .polymorphic-button {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 2px solid transparent;
        }

        .polymorphic-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Modal System -->
    <div id="modalSystem" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
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
            <div class="flex items-center">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto mr-4">
                <h1 class="text-xl font-bold">Inventario de Herramientas</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php"
                    class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Volver al Login
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-4">Herramientas Disponibles</h2>
                <div class="flex justify-between items-center mb-4">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Buscar herramienta..."
                            class="border rounded-lg pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-200">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Tarjeta No Consumibles -->
                <div class="polymorphic-card border-blue-200 bg-blue-100 p-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl group"
                    data-type="no-consumible">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="bg-white/90 p-4 rounded-full mb-4 transition-all duration-300 transform group-hover:scale-110 group-hover:shadow-lg">
                            <i class="fas fa-tools text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-blue-800">Herramientas No Consumibles</h3>
                        <p class="text-blue-700/90">Equipos y herramientas de uso permanente</p>
                    </div>
                </div>

                <!-- Tarjeta Consumibles -->
                <div class="polymorphic-card border-green-200 bg-green-100 p-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl group"
                    data-type="consumible">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="bg-white/90 p-4 rounded-full mb-4 transition-all duration-300 transform group-hover:scale-110 group-hover:shadow-lg">
                            <i class="fas fa-box-open text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-green-800">Herramientas Consumibles</h3>
                        <p class="text-green-700/90">Materiales e insumos de un solo uso</p>
                    </div>
                </div>
            </div>

            <div class="table-wrapper overflow-x-auto overflow-y-auto max-h-[calc(100vh-200px)]">
                <table class="min-w-full overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="toolsTableBody" class="divide-y divide-gray-200">
                        <?php
                        try {
                            // Consulta para herramientas consumibles
                            $stmt = $db->query("SELECT id, 
    CONCAT(
        UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 1, 1)), 
        LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 2)),
        IF(
            LOCATE(' ', nombre) > 0,
            CONCAT(' ', 
                UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 1, 1)), 
                LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 2))
            ),
            ''
        )
    ) as nombre,
    cantidad, 
    CONCAT(
        UPPER(SUBSTRING(estado, 1, 1)), 
        LOWER(SUBSTRING(estado, 2))
    ) as estado,
    CONCAT(
        UPPER(SUBSTRING(descripcion, 1, 1)), 
        LOWER(SUBSTRING(descripcion, 2))
    ) as descripcion, 
    foto 
FROM herramientas_consumibles");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $rowClass = 'row-consumible-' . strtolower($row['estado']);
                                echo "<tr class='tool-row consumible {$rowClass}'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><img src='" . ($row['foto'] ? 'uploads/herramientas/' . htmlspecialchars($row['foto']) : 'img/logoSena.png') . "' class='w-16 h-16 object-cover rounded'></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['cantidad']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['estado']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['descripcion']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>";
                                echo "<button onclick='reservarHerramienta(" . $row['id'] . ", \"consumible\")' class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-200'>";
                                echo "<i class='fas fa-calendar-plus mr-1'></i>Pedir Prestamo";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }

                            // Consulta para herramientas no consumibles
                            $stmt = $db->query("SELECT id, 
    CONCAT(
        UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 1, 1)), 
        LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 2)),
        IF(
            LOCATE(' ', nombre) > 0,
            CONCAT(' ', 
                UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 1, 1)), 
                LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 2))
            ),
            ''
        )
    ) as nombre,
    cantidad, 
    CONCAT(
        UPPER(SUBSTRING(estado, 1, 1)), 
        LOWER(SUBSTRING(estado, 2))
    ) as estado,
    CONCAT(
        UPPER(SUBSTRING(descripcion, 1, 1)), 
        LOWER(SUBSTRING(descripcion, 2))
    ) as descripcion, 
    foto 
FROM herramientas_no_consumibles");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr class='tool-row no-consumible row-no-consumible'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><img src='" . ($row['foto'] ? 'uploads/herramientas/' . htmlspecialchars($row['foto']) : 'img/logoSena.png') . "' class='w-16 h-16 object-cover rounded'></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['cantidad']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['estado']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['descripcion']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>";
                                echo "<button onclick='reservarHerramienta(" . $row['id'] . ", \"no_consumible\")' class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-200'>";
                                echo "<i class='fas fa-calendar-plus mr-1'></i>Pedir Prestamo";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' class='px-6 py-4 text-center text-red-500'>Error al cargar las herramientas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de Reserva -->
    <div id="reservaModal" class="fixed inset-0 bg-black bg-opacity-70 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Reservar Herramienta</h3>
            </div>
            
            <form id="reservaForm" class="p-6 space-y-5">
                <input type="hidden" id="herramientaId" name="herramientaId">
                <input type="hidden" id="tipoHerramienta" name="tipoHerramienta">

                <div class="space-y-1">
                    <label for="nombreAprendiz" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <input type="text" id="nombreAprendiz" name="nombreAprendiz" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Ingrese un nombre completo y valido">
                </div>

                <div class="space-y-1">
                    <label for="ficha" class="block text-sm font-medium text-gray-700">ID Ficha</label>
                    <input type="text" id="ficha" name="ficha" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Ingrese una ficha valida">
                </div>

                <div class="space-y-1">
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" required min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ingrese la cantidad a reservar">
                </div>

                <div class="space-y-1">
                    <label for="fechaReserva" class="block text-sm font-medium text-gray-700">Fecha de Reserva</label>
                    <input type="date" id="fechaReserva" name="fechaReserva" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ingrese una fecha">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="cerrarModal()"
                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Confirmar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>

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

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.tool-row');
            const consumibleCard = document.querySelector('.polymorphic-card[data-type="consumible"]');
            const noConsumibleCard = document.querySelector('.polymorphic-card[data-type="no-consumible"]');

            // Función de búsqueda
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Función para mostrar herramientas según el tipo
            function mostrarHerramientas(tipo) {
                rows.forEach(row => {
                    row.style.display = row.classList.contains(tipo) ? '' : 'none';
                });

                // Actualizar estilos de selección
                consumibleCard.classList.toggle('selected', tipo === 'consumible');
                noConsumibleCard.classList.toggle('selected', tipo === 'no-consumible');
            }

            // Asignar eventos a las tarjetas
            consumibleCard.addEventListener('click', () => mostrarHerramientas('consumible'));
            noConsumibleCard.addEventListener('click', () => mostrarHerramientas('no-consumible'));

            // Mostrar herramientas consumibles por defecto
            mostrarHerramientas('consumible');
        });

        function reservarHerramienta(id, tipo) {
            document.getElementById('herramientaId').value = id;
            document.getElementById('tipoHerramienta').value = tipo;
            document.getElementById('reservaModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('reservaModal').style.display = 'none';
            document.getElementById('reservaForm').reset();
        }

        document.getElementById('reservaForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('includes/guardar_reserva.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Reserva solicitada correctamente', 'Éxito');
                        cerrarModal();
                    } else {
                        showError('Error al guardar la reserva: ' + data.message, 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error al procesar la reserva: ' + error.message, 'Error');
                });
        });
    </script>
</body>

</html>
