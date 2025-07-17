<?php
require 'includes/database.php';
require_once 'includes/auth_check.php';



// Obtener el tipo seleccionado (consumible/no consumible)
$tipoSeleccionado = $_GET['tipo'] ?? 'no_consumible';
$tabla = ($tipoSeleccionado === 'no_consumible') ? 'herramientas_no_consumibles' : 'herramientas_consumibles';

// Actualizar estados de herramientas consumibles si es necesario
if ($tipoSeleccionado === 'consumible') {
    // Get a valid user ID for the audit log
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; // Default to admin user if not set

    // Set the user ID as a session variable for triggers
    $db->exec("SET @current_user_id = " . $userId);

    $updateQuery = "UPDATE herramientas_consumibles 
                   SET estado = CASE 
                       WHEN cantidad <= 20 THEN 'recargar'
                       WHEN cantidad > 20 AND cantidad <= 60 THEN 'medio'
                       WHEN cantidad > 60 THEN 'lleno'
                   END";
    $db->exec($updateQuery);
}
// Configuración de estados según tipo
$estadosConfig = [
    'no_consumible' => [
        'Activa' => ['color' => 'bg-green-100 text-green-800', 'icono' => 'fas fa-check-circle', 'texto' => 'Activa'],
        'Prestada' => ['color' => 'bg-blue-100 text-blue-800', 'icono' => 'fas fa-exchange-alt', 'texto' => 'Prestada']
    ],
    'consumible' => [
        'recargar' => ['color' => 'bg-red-100 text-red-800', 'icono' => 'fas fa-times-circle', 'texto' => 'Recargar'],
        'medio' => ['color' => 'bg-yellow-100 text-yellow-800', 'icono' => 'fas fa-exclamation-circle', 'texto' => 'Medio'],
        'lleno' => ['color' => 'bg-green-100 text-green-800', 'icono' => 'fas fa-check-circle', 'texto' => 'Lleno']
    ]
];

// Consulta para herramientas 
if ($tipoSeleccionado === 'no_consumible') {
    $query = "SELECT h.id, h.nombre, h.cantidad as stock_total, h.estado, h.foto, h.codigo_barras, 
    (SELECT COUNT(*) FROM prestamos p WHERE p.herramienta_id = h.id AND p.herramienta_tipo = 'no_consumible' AND p.estado = 'prestado') as prestadas 
    FROM herramientas_no_consumibles h";
} else {
    $query = "SELECT id, nombre, cantidad as stock_total, estado, foto, codigo_barras FROM herramientas_consumibles";
}

$stmt = $db->query($query);
$herramientas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Taller Diesel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .table-wrapper {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Row background colors */
        .row-no-consumible {
            background-color: rgba(191, 219, 254, 0.99);
            /* bg-blue-200 */
            transition: all 0.3s ease;
        }

        .row-no-consumible:hover {
            background-color: rgba(147, 197, 253, 0.99);
            /* hover:bg-blue-300 */
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
                            <a href="dashboard.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-home mr-3 text-[#05976A]"></i> Panel
                            </a>
                            <a href="prestamos.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-hand-holding mr-3 text-[#05976A]"></i> Préstamos
                            </a>
                            <a href="reporte.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-clipboard-list mr-3 text-[#05976A]"></i> Reportes
                            </a>
                            <a href="aprendicez.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-user-graduate mr-3 text-[#05976A]"></i> Aprendices
                            </a>
                        </div>

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
    <div class="container mx-auto px-4 py-8">
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <!-- Barra de búsqueda y botón -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="relative w-full md:w-1/2">
                    <input type="text" id="buscarHerramienta" placeholder="Buscar Herramienta..."
                        class="w-full px-4 py-2 border rounded-lg pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 hover:shadow-md">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <div class="flex gap-3">
                  <button onclick="window.mostrarModal('modalEditar', 'Agregar Herramienta')"
    class="polymorphic-button w-full md:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
    <i class="fas fa-plus mr-2"></i> Agregar Herramienta
</button>
                    <button onclick="window.mostrarModal('modalQR', 'Generar Código QR')"
                        class="polymorphic-button w-full md:w-auto bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-qrcode mr-2"></i> Generar Código QR
                    </button>
                    <button onclick="mostrarModal('csvModal', 'Subida Masiva')"
                        class="polymorphic-button w-full md:w-auto bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-file-upload mr-2"></i> Subida Masiva
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta No Consumibles -->
                <div class="polymorphic-card <?= $tipoSeleccionado === 'no_consumible' ? 'selected border-blue-600' : 'border-blue-200' ?> bg-blue-100 p-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl group"
                    onclick="cambiarCategoria('no_consumible')">
                    <div class="flex flex-col items-center text-center">
                        <!-- Icono con fondo blanco para contraste -->
                        <div
                            class="bg-white/90 p-4 rounded-full mb-4 transition-all duration-300 transform group-hover:scale-110 group-hover:shadow-lg">
                            <i class="fas fa-tools text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-blue-800">Herramientas No Consumibles</h3>
                        <p class="text-blue-700/90">Equipos y herramientas de uso permanente</p>
                        <?php if ($tipoSeleccionado === 'no_consumible'): ?>
                            <span
                                class="mt-2 px-3 py-1 bg-white text-blue-800 rounded-full text-sm font-medium transition-all duration-300 shadow-md">Seleccionado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tarjeta Consumibles -->
                <div class="polymorphic-card <?= $tipoSeleccionado === 'consumible' ? 'selected border-green-600' : 'border-green-200' ?> bg-green-100 p-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl group"
                    onclick="cambiarCategoria('consumible')">
                    <div class="flex flex-col items-center text-center">
                        <!-- Icono con fondo blanco para contraste -->
                        <div
                            class="bg-white/90 p-4 rounded-full mb-4 transition-all duration-300 transform group-hover:scale-110 group-hover:shadow-lg">
                            <i class="fas fa-box-open text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-green-800">Herramientas Consumibles</h3>
                        <p class="text-green-700/90">Materiales e insumos de un solo uso</p>
                        <?php if ($tipoSeleccionado === 'consumible'): ?>
                            <span
                                class="mt-2 px-3 py-1 bg-white text-green-800 rounded-full text-sm font-medium transition-all duration-300 shadow-md">Seleccionado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <br>
            <br>

            <!-- Tabla de herramientas -->
            <div class="overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Codigo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Stock Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                <?php echo $tipoSeleccionado === 'consumible' ? 'Estado' : 'Disponibilidad'; ?>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tablaHerramientas">
                        <?php
                        // Consulta para obtener herramientas con información de préstamos
                        $query = "SELECT 
                        h.id, 
                        h.nombre, 
                        h.cantidad as stock_total,
                        h.estado,
                        h.foto, 
                        h.codigo_barras,
                        COALESCE(p.cantidad, 0) as prestadas
                        FROM $tabla h
                        LEFT JOIN (
                            SELECT herramienta_id, 
                                    herramienta_tipo, 
                                    SUM(cantidad) as cantidad
                            FROM prestamos 
                            WHERE herramienta_tipo = :tipo 
                                AND estado = 'prestado'
                            GROUP BY herramienta_id, herramienta_tipo
                        ) p ON p.herramienta_id = h.id AND p.herramienta_tipo = :tipo";

                        $stmt = $db->prepare($query);
                        $stmt->bindValue(':tipo', $tipoSeleccionado);
                        $stmt->execute();
                        $herramientas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Configuración de estados
                        $estadosConsumibles = [
                            'lleno' => [
                                'color' => 'bg-green-100 text-green-800',
                                'icono' => 'fas fa-box-full',
                                'texto' => 'Stock lleno'
                            ],
                            'medio' => [
                                'color' => 'bg-yellow-100 text-yellow-800',
                                'icono' => 'fas fa-box-half',
                                'texto' => 'Stock medio'
                            ],
                            'recargar' => [
                                'color' => 'bg-red-100 text-red-800',
                                'icono' => 'fas fa-exclamation-triangle',
                                'texto' => 'Necesita recarga'
                            ]
                        ];

                        $estadoDefault = [
                            'color' => 'bg-gray-100 text-gray-800',
                            'icono' => 'fas fa-question-circle',
                            'texto' => 'Sin estado'
                        ];

                        foreach ($herramientas as $herramienta):
                            $stockTotal = $herramienta['stock_total'];
                            $prestadas = $herramienta['prestadas'] ?? 0;
                            // Asegurarse de que no reste más de lo disponible
                            $disponibles = $stockTotal;
                            $estado = $herramienta['estado'] ?? null;

                            // Determinar configuración de estado
                            $configEstado = ($estado && isset($estadosConsumibles[$estado])) ? $estadosConsumibles[$estado] : $estadoDefault;

                            // Determinar la clase de la fila según el tipo y estado
                            $filaClase = '';
                            if ($tipoSeleccionado === 'no_consumible') {
                                $filaClase = 'row-no-consumible';
                            } else {
                                // Para consumibles, usar una clase según el estado
                                $filaClase = 'row-consumible-' . ($estado ?? 'default');
                            }
                            ?>
                            <tr class="<?= $filaClase ?> hover:shadow-md">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                    <?= htmlspecialchars($herramienta['id'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                    <?= !empty($herramienta['codigo_barras']) ? htmlspecialchars($herramienta['codigo_barras']) : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($herramienta['foto'])): ?>
                                        <?php
                                        // Usar ruta absoluta desde la raíz del servidor web
                                        $rutaImagen = '/SGSDIESEL/uploads/herramientas/' . htmlspecialchars($herramienta['foto']);
                                        ?>
                                        <img src="<?= $rutaImagen ?>" alt="<?= htmlspecialchars($herramienta['nombre']) ?>"
                                            class="h-12 w-12 object-cover rounded-md"
                                            onerror="this.onerror=null; this.src='img/no-image.png'; console.log('Error cargando imagen: <?= $rutaImagen ?>');">
                                    <?php else: ?>
                                        <div class="h-12 w-12 bg-gray-200 rounded-md flex items-center justify-center">
                                            <i class="fas fa-tools text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">
                                    <?= htmlspecialchars($herramienta['nombre'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                    <?= htmlspecialchars($stockTotal) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($tipoSeleccionado === 'consumible'): ?>
                                        <!-- Estado para consumibles -->
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $configEstado['color'] ?>">
                                            <i class="<?= $configEstado['icono'] ?> mr-1"></i> <?= $configEstado['texto'] ?>
                                        </span>
                                    <?php else: ?>
                                        <!-- Disponibilidad para no consumibles -->
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-medium <?= $disponibles > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <i
                                                        class="fas <?= $disponibles > 0 ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                                                    <?= $disponibles ?> disponibles
                                                </span>
                                            </div>
                                            <div class="flex items-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-hand-holding mr-1"></i>
                                                    <?= $prestadas ?> prestadas
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-end items-center space-x-3">
                                    <button
                                        class="btn-editar text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-full transition-colors"
                                        data-id="<?= $herramienta['id'] ?? 0 ?>" data-tipo="<?= $tipoSeleccionado ?>"
                                        onclick="mostrarModalEditar(<?= $herramienta['id'] ?? 0 ?>, '<?= $tipoSeleccionado ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="btn-eliminar text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-full transition-colors"
                                        data-id="<?= $herramienta['id'] ?? 0 ?>" data-tipo="<?= $tipoSeleccionado ?>"
                                        onclick="mostrarModalEliminar(<?= $herramienta['id'] ?? 0 ?>, '<?= $tipoSeleccionado ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button
                                        onclick="abrirModalBaja('<?= $herramienta['id'] ?>', '<?= $tipoSeleccionado ?>', '<?= htmlspecialchars($herramienta['nombre']) ?>')"
                                        class="text-purple-600 hover:text-purple-900 hover:bg-purple-50 p-2 rounded-full transition-colors">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sección de Historial de Bajas -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Historial de Bajas de Herramientas</h2>
                <div class="overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Herramienta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Motivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Lugar
                                    Salida</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Lugar
                                    Entrada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Responsable
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Consulta para obtener el historial de bajas con nombres de herramientas
                            $query = "SELECT b.*, 
                         CASE 
                            WHEN b.tipo_herramienta = 'consumible' THEN hc.nombre
                            ELSE hnc.nombre
                         END as nombre_herramienta
                         FROM bajas_herramientas b
                         LEFT JOIN herramientas_consumibles hc ON b.herramienta_id = hc.id AND b.tipo_herramienta = 'consumible'
                         LEFT JOIN herramientas_no_consumibles hnc ON b.herramienta_id = hnc.id AND b.tipo_herramienta = 'no_consumible'
                         ORDER BY b.fecha DESC";

                            $stmt = $db->query($query);
                            while ($baja = $stmt->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= date('d/m/Y H:i', strtotime($baja['fecha'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l font-medium text-red-500">
                                        <?= htmlspecialchars($baja['nombre_herramienta']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= $baja['tipo_herramienta'] === 'consumible' ? 'Consumible' : 'No Consumible' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= htmlspecialchars($baja['cantidad']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-l text-red-500">
                                        <?= htmlspecialchars($baja['motivo']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= htmlspecialchars($baja['lugar_salida']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= htmlspecialchars($baja['lugar_entrada']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-l text-red-500">
                                        <?= htmlspecialchars($baja['responsable']) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal de Baja de Herramienta -->
            <div id="modalBajaHerramienta"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                <div class="relative top-10 mx-auto p-8 border w-[600px] shadow-xl rounded-lg bg-white">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button onclick="cerrarModalBaja()"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Dar de Baja Herramienta</h3>
                        <p class="mt-2 text-gray-600">Complete los detalles para dar de baja la herramienta seleccionada
                        </p>
                    </div>

                    <form id="formBajaHerramienta" class="space-y-6">
                        <input type="hidden" id="herramientaId">
                        <input type="hidden" id="herramientaTipo">

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de
                                    Herramienta</label>
                                <input type="text" id="nombreHerramienta" readonly
                                    class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cantidad</label>
                                <input type="number" id="cantidadBaja" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    min="1">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo de Salida</label>
                            <textarea id="motivoSalida" required rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Describa el motivo de la baja..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lugar de Salida</label>
                                <input type="text" id="lugarSalida" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ubicación de salida">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lugar de Entrada</label>
                                <input type="text" id="lugarEntrada" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ubicación de entrada">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Responsable</label>
                            <input type="text" id="responsable" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Nombre completo del responsable">
                        </div>

                        <div class="flex justify-end space-x-4 mt-8">
                            <button type="button" onclick="cerrarModalBaja()"
                                class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-red-600 rounded-lg text-white font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Confirmar Baja
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal QR -->
            <div id="modalQR" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-hidden="true">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="modal-content bg-white rounded-lg shadow-lg p-6 z-10 w-full max-w-md mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="modal-title text-lg font-semibold">Generar Código QR</h3>
                        <button type="button" class="text-gray-400 hover:text-white" onclick="cerrarModalQR()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <label for="codigoQR" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" id="codigoQR" name="codigoQR"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="nombreQR" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            archivo</label>
                        <input type="text" id="nombreQR" name="nombreQR"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nombre para el QR">
                    </div>
                    <div class="flex justify-center mb-4" id="qrContainer" style="min-height: 200px;">
                        <!-- QR code will appear here -->
                    </div>
                    <div class="flex justify-center gap-4">
                        <button onclick="generarQR()"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Generar QR
                        </button>
                        <button id="btnDescargarQR" onclick="descargarQR()"
                            class="hidden bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-download mr-2"></i> Descargar QR
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal para subida masiva CSV -->
            <div id="csvModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Subida Masiva de Herramientas</h3>
                        <button onclick="cerrarModalCSV()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

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
                            <a href="PLANTILLAHERRAMIENTAS.xlsx" download
                                class="flex items-center text-blue-700 hover:text-blue-900 font-medium">
                                <i class="fas fa-file-excel mr-2"></i>
                                Descargar plantilla Excel
                                <i class="fas fa-download ml-2"></i>
                            </a>
                            <p class="text-xs text-gray-600 mt-1">Descarga esta plantilla y úsala como base para crear
                                tu archivo CSV.</p>
                        </div>
                    </div>
                    <form id="csvForm" action="includes/upload_excel_tools.php" method="post"
                        enctype="multipart/form-data">
                        <input type="hidden" name="tipo_herramienta" id="tipoHerramientaCSV"
                            value="<?= $tipoSeleccionado ?>">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="archivoCSV">
                                Selecciona archivo CSV:
                            </label>
                            <input type="file" id="archivoCSV" name="archivoCSV" accept=".csv" required
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 flex justify-center items-center">
                            <i class="fas fa-upload mr-2"></i> Subir Archivo
                        </button>
                    </form>
                </div>
            </div>

            <!-- modal agregar herramienta -->
            <div id="modalAgregarHerramienta" class="fixed inset-0 z-50 flex items-center justify-center hidden"
                aria-hidden="true">
                <div class="absolute inset-0 bg-black opacity-50"></div>
                <div class="modal-content bg-white rounded-lg shadow-lg p-6 z-10 w-full max-w-md mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="modal-title text-lg font-semibold">Agregar Herramienta</h3>
                        <button type="button" class="text-gray-400 hover:text-white" onclick="cerrarModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="formAgregarHerramienta" method="post" action="includes/agregar_herramienta.php"
                        enctype="multipart/form-data">
                        <input type="hidden" id="tipo" name="tipo" value="<?= $tipoSeleccionado ?>">

                        <div class="mb-4">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" id="nombre" name="nombre"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion"
                                class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="ubicacion"
                                class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <input type="text" id="ubicacion" name="ubicacion"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Nuevo campo para la foto -->
                        <div class="mb-4">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto de la
                                Herramienta</label>
                            <input type="file" id="foto" name="foto" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-white">Formatos permitidos: JPG, PNG, GIF (máx. 2MB)</p>
                        </div>

                        <?php if ($tipoSeleccionado === 'consumible'): ?>
                            <div class="mb-4">
                                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select id="estado" name="estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="lleno">Lleno</option>
                                    <option value="medio">Medio</option>
                                    <option value="recargar">Recargar</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-end mt-6">
                            <button type="button"
                                class="mr-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                onclick="cerrarModal()">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición -->
            <div id="modalEditar" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Fondo oscuro con efecto blur -->
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm"></div>
                    </div>

                    <!-- Contenido del modal -->
                    <div
                        class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                        <!-- Encabezado con gradiente -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-white/20 p-2 rounded-lg">
                                    <i class="fas fa-edit text-white text-lg"></i>
                                </div>
                                <h3 class="ml-3 text-xl font-semibold text-white" id="modalEditarTitulo">
                                    Editar Herramienta
                                </h3>
                            </div>
                        </div>

                        <!-- Cuerpo del modal -->
                        <div class="bg-white px-6 py-5">
                            <form id="formEditar" action="includes/actualizar_herramienta.php"
                                enctype="multipart/form-data">
                                <input type="hidden" id="editId" name="id">
                                <input type="hidden" id="editTipo" name="tipo">

                                <div class="space-y-5">
                                    <!-- Campo Nombre -->
                                    <div>
                                        <label for="editNombre"
                                            class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                        <input type="text" id="editNombre" name="nombre"
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Campo Cantidad -->
                                    <div>
                                        <label for="editCantidad"
                                            class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                        <input type="number" id="editCantidad" name="cantidad" min="1"
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Campo Descripción -->
                                    <div>
                                        <label for="editDescripcion"
                                            class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                        <textarea id="editDescripcion" name="descripcion" rows="3"
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <!-- Campo Ubicación -->
                                    <div>
                                        <label for="editUbicacion"
                                            class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                                        <input type="text" id="editUbicacion" name="ubicacion"
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Campo Foto -->
                                    <div>
                                        <label for="editFoto" class="block text-sm font-medium text-gray-700 mb-1">Foto
                                            de la Herramienta</label>
                                        <div class="flex items-center space-x-4">
                                            <div id="fotoActualContainer"
                                                class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                                <img id="fotoActual" src="" alt="Foto actual"
                                                    class="max-w-full max-h-full object-contain hidden">
                                                <span id="sinFoto" class="text-gray-400 text-sm text-center">Sin
                                                    foto</span>
                                            </div>
                                            <div class="flex-1">
                                                <input type="file" id="editFoto" name="foto" accept="image/*"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <p class="mt-1 text-xs text-white">Formatos permitidos: JPG, PNG, GIF
                                                    (máx. 2MB)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campo Estado (solo para consumibles) -->
                                    <div id="editEstadoContainer" class="hidden">
                                        <label for="editEstado"
                                            class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                        <select id="editEstado" name="estado"
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="lleno">Lleno</option>
                                            <option value="medio">Medio</option>
                                            <option value="recargar">Recargar</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
                                        onclick="cerrarModalEditar()">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Eliminación -->
            <div id="modalEliminar" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Fondo oscuro -->
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <!-- Contenido del modal -->
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalEliminarTitulo">
                                        Confirmar Eliminación
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-white" id="modalEliminarMensaje">
                                            ¿Estás seguro de que deseas eliminar esta herramienta? Esta acción no se
                                            puede deshacer.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" onclick="confirmarEliminacion()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Eliminar
                            </button>
                            <button type="button" onclick="cerrarModal('modalEliminar')"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

</body>
<script>
    // Variables globales
    let herramientaActual = null;
    let tipoActual = '';
    

    // Utilitarios
    const $ = id => document.getElementById(id);
    const $$ = selector => document.querySelectorAll(selector);

    // =============================================
    // FUNCIONES DE MODALES (AHORA EN ÁMBITO GLOBAL)
    // =============================================

    // Función para mostrar modal de agregar
    function mostrarModalAgregar() {
        // Resetear el formulario
        $('formEditar').reset();

        // Resetear la vista previa de la foto
        const fotoPreview = $('fotoActual');
        const sinFoto = $('sinFoto');
        if (fotoPreview) fotoPreview.classList.add('hidden');
        if (sinFoto) sinFoto.classList.remove('hidden');

        // Configurar el modal para agregar
        $('modalEditarTitulo').textContent = 'Agregar Herramienta';
        $('editId').value = '';
        $('editTipo').value = tipoActual;

        // Configurar opciones de estado según el tipo
        const estadoSelect = $('editEstado');
        const estadoContainer = $('editEstadoContainer');
        const cantidadContainer = $('editCantidadContainer');

        if (tipoActual === 'no_consumible') {
            if (estadoContainer) estadoContainer.style.display = 'none';
            if (cantidadContainer) cantidadContainer.style.display = 'block';
        } else {
            if (estadoContainer) {
                estadoContainer.style.display = 'block';
                if (estadoSelect) {
                    estadoSelect.innerHTML = `
                    <option value="lleno">Stock lleno</option>
                    <option value="medio">Stock medio</option>
                    <option value="recargar">Necesita recarga</option>
                `;
                }
            }
            if (cantidadContainer) cantidadContainer.style.display = 'block';
        }

        // Set default values
        $('editUbicacion').value = 'Taller';
        $('editCantidad').value = '1';

        // Agregar evento para vista previa de foto
        const inputFoto = $('editFoto');
        if (inputFoto) {
            inputFoto.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (fotoPreview) {
                            fotoPreview.src = e.target.result;
                            fotoPreview.classList.remove('hidden');
                            if (sinFoto) sinFoto.classList.add('hidden');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Mostrar el modal centrado
        const modal = $('modalEditar');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }

    // Función para mostrar modal de QR
    function mostrarModalQR() {
        const modal = $('modalQR');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
        $('codigoQR').value = '';
        $('qrContainer').innerHTML = '';
        $('btnDescargarQR').classList.add('hidden');
    }

    // Función para mostrar modal de CSV
    function mostrarModalCSV() {
        const modal = $('csvModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
        }
    }

    // Función para cambiar entre categorías
    function cambiarCategoria(tipo) {
        window.location.href = `?tipo=${tipo}`;
    }

    // Función para mostrar modal de editar
    async function mostrarModalEditar(id, tipo) {
        try {
            const response = await fetch(`includes/obtener_herramienta.php?id=${id}&tipo=${tipo}`);

            if (!response.ok) {
                throw new Error('Error al obtener los datos');
            }

            const data = await response.json();

            if (!data || data.error) {
                throw new Error(data?.message || 'Herramienta no encontrada');
            }

            // Llenar el formulario de edición
            $('modalEditarTitulo').textContent = 'Editar Herramienta';
            $('editId').value = data.id;
            $('editTipo').value = tipo;
            $('editNombre').value = data.nombre || '';
            $('editCantidad').value = data.cantidad || 0;
            $('editDescripcion').value = data.descripcion || '';

            // Manejar la visualización de la foto
            const fotoActual = $('fotoActual');
            const sinFoto = $('sinFoto');
            if (data.foto) {
                fotoActual.src = `/SGSDIESEL/uploads/herramientas/${data.foto}`;
                fotoActual.classList.remove('hidden');
                sinFoto.classList.add('hidden');
            } else {
                fotoActual.classList.add('hidden');
                sinFoto.classList.remove('hidden');
            }

            // Si existe el campo de estado y es consumible, establecer el valor
            const estadoSelect = $('editEstado');
            if (estadoSelect && tipo === 'consumible' && data.estado) {
                estadoSelect.value = data.estado;
            }

            // Mostrar el modal centrado
            const modal = $('modalEditar');
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        }
    }

    // Función para mostrar modal de confirmación de eliminación
    function mostrarModalEliminar(id, tipo) {
        herramientaActual = { id, tipo };
        $('modalEliminarTitulo').textContent =
            `Eliminar ${tipo === 'no_consumible' ? 'Herramienta' : 'Material'}`;
        $('modalEliminarMensaje').textContent =
            `¿Estás seguro de que deseas eliminar esta ${tipo === 'no_consumible' ? 'herramienta' : 'material'}? Esta acción no se puede deshacer.`;

        const modal = $('modalEliminar');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }

    // Función unificada para cerrar modales
    function cerrarModal(modalId) {
        const modal = $(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'items-center', 'justify-center');
        }
    }

    // Funciones específicas para cada modal
    function cerrarModalAgregar() {
        cerrarModal('addToolModal');
        $('addToolForm').reset();
    }

    function cerrarModalEditar() {
        cerrarModal('modalEditar');
    }

    function cerrarModalEliminar() {
        cerrarModal('modalEliminar');
    }

    function cerrarModalCSV() {
        cerrarModal('csvModal');
    }

    function cerrarModalQR() {
        cerrarModal('modalQR');
    }

    function cerrarModalBaja() {
        cerrarModal('modalBajaHerramienta');
        $('formBajaHerramienta').reset();
    }

    // Función para guardar herramienta (agregar o editar)
    async function guardarEdicion() {
        const form = $('formEditar');
        const formData = new FormData(form);
        const id = formData.get('id');
        const url = id ? 'includes/actualizar_herramienta.php' : 'includes/agregar_herramienta.php';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();

            if (data.success) {
                alert(id ? 'Cambios guardados correctamente' : 'Herramienta agregada correctamente');
                cerrarModal('modalEditar');
                location.reload();
            } else {
                throw new Error(data.message || 'Error al guardar los cambios');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // Funciones para el modal QR
    function generarQR() {
        const codigo = $('codigoQR').value.trim();
        if (!codigo) {
            alert('Por favor ingrese un código');
            return;
        }

        const qrContainer = $('qrContainer');
        qrContainer.innerHTML = '';

        try {
            new QRCode(qrContainer, {
                text: codigo,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            $('btnDescargarQR').classList.remove('hidden');
        } catch (error) {
            console.error('Error generando QR:', error);
            alert('Error al generar el QR');
        }
    }

    function descargarQR() {
        const canvas = document.querySelector('#qrContainer canvas');
        if (!canvas) {
            alert('Primero genere un QR');
            return;
        }

        const nombre = $('nombreQR').value.trim() ||
            'codigo_qr_' + $('codigoQR').value;

        const enlace = document.createElement('a');
        enlace.href = canvas.toDataURL('image/png');
        enlace.download = nombre + '.png';
        enlace.click();
    }

    // Función para eliminar herramienta
    async function confirmarEliminacion() {
        if (!herramientaActual) return;

        const { id, tipo } = herramientaActual;

        try {
            const response = await fetch('includes/eliminar_herramienta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id, tipo })
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`El servidor respondió con: ${text}`);
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Error al eliminar');
            }

            alert('Herramienta eliminada correctamente');
            cerrarModal('modalEliminar');
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert(`Error al eliminar: ${error.message}`);
        }
    }

    // Función para buscar herramientas
    function buscarHerramientas(termino) {
        const filas = $$('#tablaHerramientas tr');
        termino = termino.toLowerCase();

        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            fila.style.display = textoFila.includes(termino) ? '' : 'none';
        });
    }

    // Función para mostrar modal de baja
    function abrirModalBaja(id, tipo, nombre) {
        $('herramientaId').value = id;
        $('herramientaTipo').value = tipo;
        $('nombreHerramienta').value = nombre;

        const modal = $('modalBajaHerramienta');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }

    // Función general para mostrar cualquier modal
    function mostrarModal(id, titulo) {
        if (!id || id === 'modalEditar') {
            mostrarModalAgregar();
            return;
        }

        const modal = $(id);
        if (modal) {
            if (titulo) {
                const modalTitle = modal.querySelector('.modal-title');
                if (modalTitle) {
                    modalTitle.textContent = titulo;
                }
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
            modal.setAttribute('aria-hidden', 'false');
        }
    }

    // Configurar eventos cuando el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener el tipo actual de la URL
        const urlParams = new URLSearchParams(window.location.search);
        tipoActual = urlParams.get('tipo') || 'no_consumible';



        // Asignar eventos a todos los botones de cierre de modal
        $$('.modal-close').forEach(button => {
            button.addEventListener('click', function () {
                const modal = this.closest('.modal');
                if (modal) {
                    cerrarModal(modal.id);
                }
            });
        });

        // Cerrar modales al hacer clic fuera del contenido
        $$('.modal').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    cerrarModal(this.id);
                }
            });
        });

        // Configurar formularios para cerrar modales después de enviar
        const configureForm = (formId, modalId) => {
            const form = $(formId);
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cerrarModal(modalId);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al procesar la solicitud');
                        });
                });
            }
        };

        // Configurar formularios
        configureForm('addToolForm', 'addToolModal');

        // Campo de búsqueda
        const campoBusqueda = $('buscarHerramienta');
        if (campoBusqueda) {
            campoBusqueda.addEventListener('input', function (e) {
                buscarHerramientas(e.target.value);
            });
        }

        // Prevenir envío del formulario de edición
        const formEditar = $('formEditar');
        if (formEditar) {
            formEditar.addEventListener('submit', function (e) {
                e.preventDefault();
                guardarEdicion();
            });
        }

        // Manejo del formulario de subida masiva CSV
        const csvForm = $('csvForm');
        if (csvForm) {
            csvForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(csvForm);

                fetch(csvForm.action, {
                    method: 'POST',
                    body: formData
                })
                    .then(async (response) => {
                        const text = await response.text();
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                alert("Archivo subido exitosamente");
                                window.location.reload();
                            } else {
                                alert("Error del servidor: " + (data.message || "Error desconocido"));
                            }
                        } catch (err) {
                            console.error("La respuesta no fue JSON:", text);
                            alert("El servidor devolvió una respuesta no válida. Verifica errores en el servidor.");
                        }
                    })
                    .catch(error => {
                        console.error("Error en fetch:", error);
                        alert("Error inesperado: " + error.message);
                    });
            });
        }

        // Configurar formulario de baja
        const formBaja = $('formBajaHerramienta');
        if (formBaja) {
            formBaja.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = {
                    herramienta_id: $('herramientaId').value,
                    tipo: $('herramientaTipo').value,
                    cantidad: parseInt($('cantidadBaja').value),
                    motivo: $('motivoSalida').value,
                    lugar_salida: $('lugarSalida').value,
                    lugar_entrada: $('lugarEntrada').value,
                    responsable: $('responsable').value
                };

                try {
                    const response = await fetch('includes/procesar_baja.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Baja procesada correctamente');
                        cerrarModalBaja();
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al procesar la baja:', error);
                    alert('Error al procesar la baja');
                }
            });
        }

        // Configurar eventos de escape para cerrar modales
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                // Cerrar todos los modales visibles
                $$('.modal').forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        cerrarModal(modal.id);
                    }
                });
            }
        });
    });

    function mostrarModal(id, titulo) {
    const modal = document.getElementById(id);
    if (!modal) {
        console.error(`Modal con ID '${id}' no encontrado`);
        return;
    }

    // Limpiar cualquier contenido previo si es necesario
    if (id === 'modalQR') {
        document.getElementById('codigoQR').value = '';
        document.getElementById('qrContainer').innerHTML = '';
        document.getElementById('btnDescargarQR').classList.add('hidden');
    } else if (id === 'csvModal') {
        const csvForm = document.getElementById('csvForm');
        if (csvForm) csvForm.reset();
    } else if (id === 'modalEditar') {
        document.getElementById('formEditar').reset();
        const fotoPreview = document.getElementById('fotoActual');
        const sinFoto = document.getElementById('sinFoto');
        if (fotoPreview) fotoPreview.classList.add('hidden');
        if (sinFoto) sinFoto.classList.remove('hidden');
    }

    // Actualizar el título si existe
    if (titulo) {
        const modalTitle = modal.querySelector('.modal-title');
        if (modalTitle) modalTitle.textContent = titulo;
    }

    // Mostrar el modal
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
    modal.setAttribute('aria-hidden', 'false');
}
</script>

</html>