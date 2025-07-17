<?php
require_once 'includes/auth_check.php';

// Verify user role
if ($_SESSION['rol'] === 'administrador') {
    header('Location: dashboard.php');
    exit;
}

// Database connection
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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

        .table-wrapper {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Row background colors */
        .row-no-consumible {
            background-color: rgba(205, 226, 253, 0.99);
            transition: all 0.3s ease;
        }

        .row-no-consumible:hover {
            background-color: rgba(101, 165, 243, 0.8);
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

        /* Efecto para el icono */
        .polymorphic-card .rounded-full {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .polymorphic-card:hover .rounded-full {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">

    <header class="bg-[#4A655D] text-white shadow-lg" style="background-color: #4A655D !important;">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="container mx-auto flex items-center px-4 py-3">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto mr-4">
                <div>
                    <h1 class="text-xl font-bold">INVENTARIO</h1>
                </div>
            </div>

            <!-- Dropdown Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="bg-[#05976A] hover:bg-[#4A655D] px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center text-white">
                    <i class="fas fa-bars mr-2"></i>
                    <span>Menú</span>
                </button>

                <!-- Overlay -->
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="open = false"></div>

                <!-- Sidebar Menu con color claro -->
                <div x-show="open"
                    class="fixed top-0 right-0 h-full w-64 bg-gray-50 shadow-xl z-50 transform transition-transform duration-300 ease-in-out"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    <div class="p-4 h-full flex flex-col">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Menú Principal</h2>
                            <button @click="open = false"
                                class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="flex-grow overflow-y-auto space-y-2">
                            <a href="user_dashboard.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-home mr-3 text-[#05976A]"></i> Panel
                            </a>
                            <a href="user_loans.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-hand-holding mr-3 text-[#05976A]"></i> Préstamos
                            </a>

                            <div class="border-t border-gray-200 mt-auto pt-4">
                                <a href="logout.php"
                                    class="block px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition duration-200">
                                    <i class="fas fa-sign-out-alt mr-3"></i> Salir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-4">
                    </i> Herramientas Disponibles
                </h2>

                <!-- Campo de búsqueda -->
                <div class="relative w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Buscar herramienta..."
                        class="border rounded-lg pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-200">
                </div>
                <br>

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
            </div>

            <!-- Tabla de herramientas -->
            <div class="table-wrapper overflow-x-auto overflow-y-auto max-h-[calc(100vh-200px)]">
                <table class="min-w-full overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Descripción
                            </th>
                        </tr>
                    </thead>
                    <tbody id="toolsTableBody" class="divide-y divide-gray-200">
                        <?php
                        try {
                            // Consulta para herramientas consumibles
                            $stmt = $db->query("SELECT 
    CONCAT(
        UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 1, 1)),
        LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 2)),
        IF(LOCATE(' ', nombre) > 0, 
            CONCAT(' ', 
                UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 1, 1)),
                LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 2))
            ),
            ''
        )
    ) as nombre, 
    cantidad, estado, CONCAT(
        UPPER(SUBSTRING(descripcion, 1, 1)),
        LOWER(SUBSTRING(descripcion, 2))
    ) as descripcion, foto, codigo_barras 
FROM herramientas_consumibles");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $rowClass = 'row-consumible-' . strtolower($row['estado']);
                                echo "<tr class='tool-row consumible {$rowClass}'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><img src='" . ($row['foto'] ? 'uploads/herramientas/' . htmlspecialchars($row['foto']) : 'img/logoSena.png') . "' class='w-16 h-16 object-cover rounded'></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['codigo_barras']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['cantidad']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['estado']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['descripcion']) . "</td>";
                                echo "</tr>";
                            }

                            // Consulta para herramientas no consumibles
                            $stmt = $db->query("SELECT 
    CONCAT(
        UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 1, 1)),
        LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', 1), 2)),
        IF(LOCATE(' ', nombre) > 0, 
            CONCAT(' ', 
                UPPER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 1, 1)),
                LOWER(SUBSTRING(SUBSTRING_INDEX(nombre, ' ', -1), 2))
            ),
            ''
        )
    ) as nombre, 
    cantidad, estado, CONCAT(
        UPPER(SUBSTRING(descripcion, 1, 1)),
        LOWER(SUBSTRING(descripcion, 2))
    ) as descripcion, foto, codigo_barras  FROM herramientas_no_consumibles");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr class='tool-row no-consumible row-no-consumible'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><img src='" . ($row['foto'] ? 'uploads/herramientas/' . htmlspecialchars($row['foto']) : 'img/logoSena.png') . "' class='w-16 h-16 object-cover rounded'></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['codigo_barras']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['cantidad']) . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['estado']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['descripcion']) . "</td>";
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.tool-row');
            const cards = document.querySelectorAll('.polymorphic-card');

            // Seleccionar primera tarjeta por defecto y mostrar solo ese tipo
            if (cards.length > 0) {
                cards[0].classList.add('selected');
                const defaultType = cards[0].getAttribute('data-type');
                filterToolsByType(defaultType);
            }

            // Función para filtrar herramientas por tipo
            function filterToolsByType(type) {
                rows.forEach(row => {
                    row.style.display = row.classList.contains(type) ? '' : 'none';
                });
            }

            // Eventos para las tarjetas
            cards.forEach(card => {
                card.addEventListener('click', function () {
                    const type = this.getAttribute('data-type');

                    // Actualizar selección visual
                    cards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    // Aplicar filtro
                    filterToolsByType(type);
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.tool-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                rows.forEach(row => {
                    const toolName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const toolCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase();

                    if (toolName.includes(searchTerm) || toolCode.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>