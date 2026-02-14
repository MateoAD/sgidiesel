<?php
// Conexión a la base de datos
require_once 'includes/database.php';
require_once 'includes/auth_check.php';
if (isset($_SESSION['error_stock'])) {
    echo '<script>console.log("' . $_SESSION['error_stock'] . '");</script>';
    unset($_SESSION['error_stock']);
}

// Manejo de errores mejorado
try {
    // Consulta para obtener préstamos ACTIVOS DE NO CONSUMIBLES (primera tabla)
    $queryActivos = "SELECT 
    p.id AS id_prestamo,
    (
        SELECT GROUP_CONCAT(
            CONCAT(
                UPPER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(a.nombre, ' ', n.n), ' ', -1), 1, 1)),
                LOWER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(a.nombre, ' ', n.n), ' ', -1), 2))
            ) SEPARATOR ' '
        ) 
        FROM (
            SELECT 1 AS n UNION SELECT 2 UNION SELECT 3
        ) n
        WHERE n.n <= LENGTH(a.nombre) - LENGTH(REPLACE(a.nombre, ' ', '')) + 1
    ) AS aprendiz,
    a.id AS id_aprendiz,
    CONCAT(
        UPPER(SUBSTRING(hnc.nombre, 1, 1)),
        LOWER(SUBSTRING(hnc.nombre, 2))
    ) AS herramienta,
    p.herramienta_tipo,
    p.cantidad,
    CONCAT(
        UPPER(SUBSTRING(p.descripcion, 1, 1)),
        LOWER(SUBSTRING(p.descripcion, 2))
    ) AS descripcion,
    CONCAT(
        UPPER(SUBSTRING(p.estado, 1, 1)),
        LOWER(SUBSTRING(p.estado, 2))
    ) AS estado_prestamo,
    p.fecha_prestamo,
    p.fecha_devolucion
  FROM prestamos p
  JOIN aprendices a ON p.id_aprendiz = a.id
  JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
  WHERE p.estado = 'prestado'
  ORDER BY p.fecha_prestamo DESC";

    $stmtActivos = $db->prepare($queryActivos);
    $stmtActivos->execute();
    $prestamosActivos = $stmtActivos->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el HISTORIAL COMPLETO (segunda tabla)
    $queryHistorial = "SELECT 
            p.id AS id_prestamo,
            CONCAT(
                UPPER(SUBSTRING(a.nombre, 1, 1)),
                LOWER(SUBSTRING(a.nombre, 2))
            ) AS aprendiz,
            CASE 
                WHEN p.herramienta_tipo = 'consumible' THEN CONCAT(
                    UPPER(SUBSTRING(hc.nombre, 1, 1)),
                    LOWER(SUBSTRING(hc.nombre, 2))
                )
                ELSE CONCAT(
                    UPPER(SUBSTRING(hnc.nombre, 1, 1)),
                    LOWER(SUBSTRING(hnc.nombre, 2))
                )
            END AS herramienta,
            p.herramienta_tipo,
            p.cantidad,
            CASE
                WHEN p.herramienta_tipo = 'consumible' THEN 'Consumida'
                ELSE CONCAT(
                    UPPER(SUBSTRING(p.estado, 1, 1)),
                    LOWER(SUBSTRING(p.estado, 2))
                )
            END AS estado_prestamo,
            p.fecha_prestamo,
            p.fecha_devolucion
          FROM prestamos p
          JOIN aprendices a ON p.id_aprendiz = a.id
          LEFT JOIN herramientas_consumibles hc ON (p.herramienta_id = hc.id AND p.herramienta_tipo = 'consumible')
          LEFT JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
          ORDER BY p.fecha_prestamo DESC";

    $stmtHistorial = $db->prepare($queryHistorial);
    $stmtHistorial->execute();
    $historialPrestamos = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener aprendices con herramientas NO CONSUMIBLES pendientes
  $queryPendientes = "SELECT 
    a.id, 
    CONCAT(
        UPPER(SUBSTRING(a.nombre, 1, 1)),
        LOWER(SUBSTRING(a.nombre, 2))
    ) AS nombre, 
    COUNT(p.id) AS prestamos_pendientes,
    GROUP_CONCAT(
        CONCAT(
            CASE 
                WHEN p.herramienta_tipo = 'consumible' THEN hc.nombre
                ELSE hnc.nombre
            END,
            ' (cantidad: ', p.cantidad, ')')
        SEPARATOR ', '
    ) as herramientas_pendientes
FROM aprendices a
LEFT JOIN prestamos p ON a.id = p.id_aprendiz
LEFT JOIN herramientas_consumibles hc ON (p.herramienta_id = hc.id AND p.herramienta_tipo = 'consumible')
LEFT JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
WHERE p.estado = 'prestado'
    AND NOT EXISTS (
        SELECT 1 
        FROM reportes r 
        WHERE r.id_aprendiz = a.id 
        AND r.resuelto = 0
    )
GROUP BY a.id, a.nombre
HAVING prestamos_pendientes > 0";

    $stmtPendientes = $db->prepare($queryPendientes);
    $stmtPendientes->execute();
    $aprendicesPendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

    // Variables para mensajes
    $mensajeExito = '';
    $mensajeError = '';

    // Procesar devolución si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['devolver'])) {
            $idPrestamo = filter_input(INPUT_POST, 'id_prestamo', FILTER_VALIDATE_INT);

            if (!$idPrestamo) {
                $mensajeError = "ID de préstamo inválido";
            } else {
                // Iniciar transacción
                $db->beginTransaction();

                try {
                    // Obtener datos del préstamo con más información para la auditoría
                    $stmtPrestamo = $db->prepare("
                        SELECT p.herramienta_id, p.herramienta_tipo, p.cantidad, p.id_aprendiz, 
                               a.nombre as nombre_aprendiz, 
                               CASE 
                                   WHEN p.herramienta_tipo = 'consumible' THEN hc.nombre
                                   ELSE hnc.nombre
                               END as nombre_herramienta
                        FROM prestamos p
                        JOIN aprendices a ON p.id_aprendiz = a.id
                        LEFT JOIN herramientas_consumibles hc ON (p.herramienta_id = hc.id AND p.herramienta_tipo = 'consumible')
                        LEFT JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
                        WHERE p.id = ?");
                    $stmtPrestamo->execute([$idPrestamo]);
                    $prestamoData = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

                    if (!$prestamoData) {
                        throw new Exception("Préstamo no encontrado");
                    }

                    // Determinar el estado final según el tipo de herramienta
                    $estadoFinal = ($prestamoData['herramienta_tipo'] === 'consumible') ? 'consumida' : 'devuelto';

                    // Actualizar estado del préstamo
                    $stmtUpdate = $db->prepare("UPDATE prestamos SET estado = ?, fecha_devolucion = NOW() WHERE id = ?");
                    $stmtUpdate->execute([$estadoFinal, $idPrestamo]);

                    // Solo actualizar stock para herramientas no consumibles
                    if ($prestamoData['herramienta_tipo'] === 'no_consumible') {
                        $updateSql = "UPDATE herramientas_no_consumibles 
                                     SET cantidad = cantidad + ?, 
                                         estado = 'Activa' 
                                     WHERE id = ?";
                        $db->prepare($updateSql)->execute([$prestamoData['cantidad'], $prestamoData['herramienta_id']]);
                    }

                    // Crear registro de auditoría para la devolución
                    $accion = ($prestamoData['herramienta_tipo'] === 'consumible') ? 'consumida' : 'devolucion';
                    $detalles = json_encode([
                        'descripcion' => ($prestamoData['herramienta_tipo'] === 'consumible')
                            ? 'Herramienta consumible registrada como consumida'
                            : 'Devolución de herramienta no consumible',
                        'herramienta' => $prestamoData['nombre_herramienta'],
                        'aprendiz' => $prestamoData['nombre_aprendiz'],
                        'cantidad' => $prestamoData['cantidad'],
                        'tipo' => $prestamoData['herramienta_tipo']
                    ], JSON_UNESCAPED_UNICODE);

                    // Insertar registro de auditoría
                    $stmtAudit = $db->prepare("
                        INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion, ip_usuario) 
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                    $stmtAudit->execute([
                        $userId, // Usar el ID de usuario validado o NULL
                        $accion,
                        'prestamos',
                        $idPrestamo,
                        $detalles,
                        $_SERVER['REMOTE_ADDR'] ?? null
                    ]);

                    $db->commit();
                    $mensajeExito = "Devolución registrada con éxito";
                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=devolucion");
                    exit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $mensajeError = "Error al procesar devolución: " . $e->getMessage();
                    error_log("Error en devolución: " . $e->getMessage());
                }
            }
        }
        // Procesar generación de reporte
        elseif (isset($_POST['generar_reporte'])) {
            $db->beginTransaction();
            $reportesGenerados = 0;

            try {
                // Obtener aprendices con préstamos pendientes
                $stmtPendientes = $db->prepare("
                    SELECT a.id, a.nombre, 
                           COUNT(p.id) as prestamos_pendientes,
                           GROUP_CONCAT(
                               CONCAT(
                                   CASE 
                                       WHEN p.herramienta_tipo = 'consumible' THEN hc.nombre
                                       ELSE hnc.nombre
                                   END,
                                   ' (cantidad: ', p.cantidad, ')'
                               ) SEPARATOR ', '
                           ) as herramientas_pendientes
                    FROM aprendices a
                    LEFT JOIN prestamos p ON a.id = p.id_aprendiz
                    LEFT JOIN herramientas_consumibles hc ON (p.herramienta_id = hc.id AND p.herramienta_tipo = 'consumible')
                    LEFT JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
                    WHERE p.estado = 'prestado'
                    GROUP BY a.id, a.nombre
                    HAVING prestamos_pendientes > 0");
                $stmtPendientes->execute();
                $aprendicesPendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

                if (empty($aprendicesPendientes)) {
                    throw new Exception("No hay aprendices con herramientas pendientes para reportar");
                }

                // Insertar nuevos reportes para cada aprendiz con pendientes
                foreach ($aprendicesPendientes as $aprendiz) {
               $stmtCheck = $db->prepare("SELECT COUNT(*) FROM reportes 
    WHERE id_aprendiz = ? 
    AND resuelto = 0");
$stmtCheck->execute([$aprendiz['id']]);
$existeReporte = $stmtCheck->fetchColumn();

                    if (!$existeReporte) {
                        $stmtInsert = $db->prepare("
                            INSERT INTO reportes 
                            (id_aprendiz, observaciones, fecha_reporte, resuelto) 
                            VALUES (?, ?, NOW(), 0)");
                        $stmtInsert->execute([
                            $aprendiz['id'],
                            "Herramientas pendientes: " . $aprendiz['herramientas_pendientes'] .
                            " | Total préstamos pendientes: " . $aprendiz['prestamos_pendientes']
                        ]);

                        if ($stmtInsert->rowCount() > 0) {
                            $reportesGenerados++;
                        } else {
                            error_log("No se pudo insertar reporte para aprendiz ID: " . $aprendiz['id']);
                        }
                    }
                }

                if ($reportesGenerados === 0) {
                    throw new Exception("Todos los aprendices con pendientes ya tienen reportes recientes no resueltos");
                }

                $db->commit();
                $mensajeExito = "Se generaron $reportesGenerados reportes con éxito";
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=reporte&cantidad=" . $reportesGenerados);
                exit();
            } catch (Exception $e) {
                $db->rollBack();
                $mensajeError = "Error al generar reporte: " . $e->getMessage();
                error_log("Error al generar reporte: " . $e->getMessage());
            }
        }
    }

    // Mostrar mensajes de éxito desde la URL
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'devolucion') {
            $mensajeExito = "Devolución procesada correctamente";
        } elseif ($_GET['success'] === 'reporte') {
            $cantidad = isset($_GET['cantidad']) ? (int) $_GET['cantidad'] : 0;
            $mensajeExito = $cantidad > 0
                ? "Reporte generado exitosamente. Se crearon $cantidad nuevos reportes."
                : "No se crearon nuevos reportes porque ya existen reportes recientes para estos aprendices.";
        }
    }
    $periodoDefault = 'mensual';
    $periodo = $_GET['periodo'] ?? $periodoDefault;

    // Determinar la cláusula WHERE según el período seleccionado
    switch ($periodo) {
        case 'diario':
            $whereClause = "DATE(p.fecha_prestamo) = CURDATE()";
            $titulo = "Estadísticas del día";
            break;
        case 'semanal':
            $whereClause = "YEARWEEK(p.fecha_prestamo, 1) = YEARWEEK(CURDATE(), 1)";
            $titulo = "Estadísticas de la semana";
            break;
        case 'mensual':
            $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE()) AND MONTH(p.fecha_prestamo) = MONTH(CURDATE())";
            $titulo = "Estadísticas del mes";
            break;
        case 'anual':
            $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE())";
            $titulo = "Estadísticas del año";
            break;
        default:
            $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE()) AND MONTH(p.fecha_prestamo) = MONTH(CURDATE())";
            $titulo = "Estadísticas del mes";
            $periodo = 'mensual';
    }

    // Herramientas no consumibles más prestadas
    $queryHerramientasNoConsumibles = "
        SELECT 
            CONCAT(UPPER(SUBSTRING(hnc.nombre, 1, 1)), LOWER(SUBSTRING(hnc.nombre, 2))) AS herramienta,
            COUNT(p.id) AS total_prestamos,
            SUM(p.cantidad) AS total_unidades
        FROM prestamos p
        JOIN herramientas_no_consumibles hnc ON p.herramienta_id = hnc.id
        WHERE p.herramienta_tipo = 'no_consumible'
        AND $whereClause
        GROUP BY p.herramienta_id
        ORDER BY total_prestamos DESC
        LIMIT 10";

    $stmtHerramientasNoConsumibles = $db->prepare($queryHerramientasNoConsumibles);
    $stmtHerramientasNoConsumibles->execute();
    $herramientasNoConsumibles = $stmtHerramientasNoConsumibles->fetchAll(PDO::FETCH_ASSOC);

    // Herramientas consumibles más utilizadas
    $queryHerramientasConsumibles = "
        SELECT 
            CONCAT(UPPER(SUBSTRING(hc.nombre, 1, 1)), LOWER(SUBSTRING(hc.nombre, 2))) AS herramienta,
            COUNT(p.id) AS total_prestamos,
            SUM(p.cantidad) AS total_unidades
        FROM prestamos p
        JOIN herramientas_consumibles hc ON p.herramienta_id = hc.id
        WHERE p.herramienta_tipo = 'consumible'
        AND $whereClause
        GROUP BY p.herramienta_id
        ORDER BY total_unidades DESC
        LIMIT 10";

    $stmtHerramientasConsumibles = $db->prepare($queryHerramientasConsumibles);
    $stmtHerramientasConsumibles->execute();
    $herramientasConsumibles = $stmtHerramientasConsumibles->fetchAll(PDO::FETCH_ASSOC);

    // Aprendices con más préstamos
    $queryAprendices = "
        SELECT 
            CONCAT(UPPER(SUBSTRING(a.nombre, 1, 1)), LOWER(SUBSTRING(a.nombre, 2))) AS aprendiz,
            COUNT(p.id) AS total_prestamos
        FROM prestamos p
        JOIN aprendices a ON p.id_aprendiz = a.id
        WHERE $whereClause
        GROUP BY p.id_aprendiz
        ORDER BY total_prestamos DESC
        LIMIT 10";

    $stmtAprendices = $db->prepare($queryAprendices);
    $stmtAprendices->execute();
    $aprendicesTop = $stmtAprendices->fetchAll(PDO::FETCH_ASSOC);

    // Preparar datos para gráficos en formato JSON
    $datosHerramientasNoConsumibles = [
        'labels' => array_column($herramientasNoConsumibles, 'herramienta'),
        'prestamos' => array_column($herramientasNoConsumibles, 'total_prestamos'),
        'unidades' => array_column($herramientasNoConsumibles, 'total_unidades')
    ];

    $datosHerramientasConsumibles = [
        'labels' => array_column($herramientasConsumibles, 'herramienta'),
        'prestamos' => array_column($herramientasConsumibles, 'total_prestamos'),
        'unidades' => array_column($herramientasConsumibles, 'total_unidades')
    ];

    $datosAprendices = [
        'labels' => array_column($aprendicesTop, 'aprendiz'),
        'prestamos' => array_column($aprendicesTop, 'total_prestamos')
    ];

} catch (PDOException $e) {
    $prestamosActivos = [];
    $historialPrestamos = [];
    $aprendicesPendientes = [];
    $herramientasNoConsumibles = [];
    $herramientasConsumibles = [];
    $aprendicesTop = [];
}

// Consulta para obtener reservas pendientes
$queryReservas = "SELECT 
   r.id, 
   r.herramienta_id, 
   r.tipo_herramienta, 
   r.descripcion,
   (
       SELECT GROUP_CONCAT(
           CONCAT(
               UPPER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(r.nombre_aprendiz, ' ', n.digit+1), ' ', -1), 1, 1)),
               LOWER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(r.nombre_aprendiz, ' ', n.digit+1), ' ', -1), 2))
           ) SEPARATOR ' '
       ) 
       FROM (
           SELECT 0 AS digit UNION SELECT 1 UNION SELECT 2 UNION SELECT 3
       ) n
       WHERE n.digit < LENGTH(r.nombre_aprendiz) - LENGTH(REPLACE(r.nombre_aprendiz, ' ', '')) + 1
   ) AS nombre_aprendiz,
   r.ficha, 
   r.fecha_reserva,
   r.cantidad,
   CASE 
       WHEN r.tipo_herramienta = 'consumible' THEN CONCAT(
           UPPER(SUBSTRING(hc.nombre, 1, 1)),
           LOWER(SUBSTRING(hc.nombre, 2))
       )
       ELSE CONCAT(
           UPPER(SUBSTRING(hnc.nombre, 1, 1)),
           LOWER(SUBSTRING(hnc.nombre, 2))
       )
   END AS nombre_herramienta
   FROM reservas_herramientas r
   LEFT JOIN herramientas_consumibles hc ON (r.herramienta_id = hc.id AND r.tipo_herramienta = 'consumible')
   LEFT JOIN herramientas_no_consumibles hnc ON (r.herramienta_id = hnc.id AND r.tipo_herramienta = 'no_consumible')
   WHERE r.estado = 'pendiente'";

$stmtReservas = $db->prepare($queryReservas);
$stmtReservas->execute();
$reservasPendientes = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['rechazar_reserva'])) {
    $idReserva = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT);

    if ($idReserva) {
        $db->beginTransaction();

        try {
            // Eliminar la reserva de la tabla
            $stmtDelete = $db->prepare("DELETE FROM reservas_herramientas WHERE id = ?");
            $stmtDelete->execute([$idReserva]);

            //Registrar en auditorías
            $stmtAudit = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmtAudit->execute([
                $userId,
                'rechazo_reserva',
                'reservas_herramientas',
                $idReserva,
                json_encode(['accion' => 'Reserva rechazada y eliminada'])
            ]);

            $db->commit();
            $_SESSION['success'] = 'Reserva rechazada y eliminada correctamente';
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Error al rechazar la reserva: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'ID de reserva inválido';
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
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

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            margin-bottom: 20px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-button {
            padding: 10px 15px;
            background-color: #f3f4f6;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-button.active {
            background-color: #4A655D;
            color: white;
        }

        #modalSystem {
            z-index: 60; /* Incrementado para asegurar que esté por encima de modal-reporte */
        }
    </style>
</head>

<body class="bg-gray-50">

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
                    <h1 class="text-xl font-bold">REPORTES</h1>
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
                            <a href="inventario.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-box mr-3 text-[#05976A]"></i>
                                Inventario
                            </a>
                            <a href="prestamos.php"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-200 hover:text-[#4A655D] rounded-lg transition duration-200">
                                <i class="fas fa-hand-holding mr-3 text-[#05976A]"></i> Prestamos
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

    <main class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Sección de Préstamos Activos (No Consumibles) -->
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-toolbox mr-2"></i>Préstamos Activos
            </h2>

            <!-- Buscador -->
            <div class="mb-6">
                <input type="text" id="buscar-activos" placeholder="Buscar préstamos activos..."
                    class="px-4 py-2 border rounded-lg w-full max-w-md">
            </div>

            <!-- Tabla de préstamos activos -->
            <div class="table-container mb-4">
                <table id="tabla-activos" class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aprendiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Herramienta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Descripción
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha Préstamo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($prestamosActivos)): ?>
                            <?php foreach ($prestamosActivos as $prestamo): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($prestamo['aprendiz'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($prestamo['herramienta'] ?? 'N/A') ?>
                                        <span
                                            class="text-xs text-gray-500 block">(<?= htmlspecialchars($prestamo['herramienta_tipo'] ?? '') ?>)</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($prestamo['cantidad'] ?? '0') ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($prestamo['descripcion'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $estado = $prestamo['estado_prestamo'] ?? 'desconocido';
                                        $clases = [
                                            'prestado' => 'bg-yellow-100 text-yellow-800',
                                            'devuelto' => 'bg-green-100 text-green-800',
                                            'consumida' => 'bg-purple-100 text-purple-800',
                                            'default' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $clase = $clases[$estado] ?? $clases['default'];
                                        ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>">
                                            <?= htmlspecialchars($estado) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= date('d/m/Y H:i', strtotime($prestamo['fecha_prestamo'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="id_prestamo" value="<?= $prestamo['id_prestamo'] ?>">
                                            <button type="submit" name="devolver"
                                                class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm"
                                                onclick="return confirmarDevolucion(<?= $prestamo['id_prestamo'] ?>)">
                                                <i class="fas fa-undo-alt mr-1"></i> Devolver
                                            </button>
                                        </form>
                                        <?php
                                        // Verificar si existe un reporte asociado al préstamo o al aprendiz
                                        $stmtReporte = $db->prepare(
                                            "SELECT COUNT(*) FROM reportes r 
                                            LEFT JOIN prestamos p ON r.id_prestamo = p.id 
                                            LEFT JOIN aprendices a ON p.id_aprendiz = a.id 
                                            WHERE r.id_prestamo = ? OR 
                                            (p.id_aprendiz IN (SELECT DISTINCT p2.id_aprendiz FROM prestamos p2 
                                                               INNER JOIN reportes r2 ON p2.id = r2.id_prestamo))"
                                        );
                                        $stmt = $db->prepare("
                                            SELECT COUNT(*) as tiene_reporte 
                                            FROM reportes r 
                                            INNER JOIN prestamos p ON p.id_aprendiz = r.id_aprendiz 
                                            WHERE p.id = ? AND r.resuelto = 0
                                        ");
                                        $stmt->execute([$prestamo['id_prestamo']]);
                                        $tieneReporte = $stmt->fetchColumn() > 0;

                                        if ($tieneReporte): ?>
                                            <div
                                                class="mt-2 inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Reportado
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron préstamos activos
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Botón de reporte -->
            <div class="w-full">
                <button onclick="mostrarModalReporte()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium">
                    <i class="fas fa-exclamation-triangle mr-2"></i> GENERAR REPORTE DE DEVOLUCIONES PENDIENTES
                </button>
            </div>
        </div>

        <!-- Sección de Reservas Pendientes -->
        <div class="bg-blue-50 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4"><i class="fas fa-clock mr-2"></i>Reservas Pendientes</h2>
            <div class="table-container overflow-x-auto">
                <?php if (empty($reservasPendientes)): ?>
                    <div class="text-center py-8">
                        <h3 class="mt-2 text-lg font-medium text-gray-900">No hay reservas pendientes</h3>
                        <p class="mt-1 text-gray-500">Actualmente no hay solicitudes de reserva por aprobar.</p>
                    </div>
                <?php else: ?>
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                      <thead class="bg-gray-800 text-white sticky top-0 z-10">
    <tr>
        <th class="px-6 py-3 text-left">Aprendiz</th>
        <th class="px-6 py-3 text-left">Herramienta</th>
        <th class="px-6 py-3 text-left">Cantidad</th>
        <th class="px-6 py-3 text-left">Descripción</th>
        <th class="px-6 py-3 text-left">Fecha Reserva</th>
        <th class="px-6 py-3 text-left">Acciones</th>
    </tr>
</thead>
                        <tbody id="toolsTableBody" class="divide-y divide-gray-200">
    <?php foreach ($reservasPendientes as $reserva): ?>
        <tr class="border-b hover:bg-blue-50">
            <td class="px-6 py-4">
                <?= htmlspecialchars($reserva['nombre_aprendiz']) ?>
                <div class="text-sm text-gray-500">Ficha: <?= htmlspecialchars($reserva['ficha']) ?></div>
            </td>
            <td class="px-6 py-4"><?= htmlspecialchars($reserva['nombre_herramienta']) ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($reserva['cantidad']) ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($reserva['descripcion']) ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($reserva['fecha_reserva']) ?></td>
                                    <td class="px-6 py-4">
                                        <form method="post" class="inline" onsubmit="return procesarReserva(event, this, <?= $reserva['id'] ?>, '<?= $reserva['tipo_herramienta'] ?>');">
                                            <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                            <input type="hidden" name="herramienta_id"
                                                value="<?= $reserva['herramienta_id'] ?>">
                                            <input type="hidden" name="tipo_herramienta"
                                                value="<?= $reserva['tipo_herramienta'] ?>">
                                            <div class="flex items-center space-x-2">
                                                <button type="submit" name="aceptar_reserva"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                                                    <i class="fas fa-check-circle mr-2"></i> 
                                                </button>
                                                <button type="submit" name="rechazar_reserva"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center"
                                                    onclick="return confirmarRechazoReserva(<?= $reserva['id'] ?>)">
                                                    <i class="fas fa-times-circle mr-2"></i> 
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de Historial Completo -->
        <div
            class="polymorphic-container bg-gradient-to-r from-purple-50 to-blue-50 p-4 sm:p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-history mr-2"></i> Historial Completo de Préstamos
            </h2>

            <!-- Buscador -->
            <div class="mb-6 flex flex-col md:flex-row gap-4 items-stretch md:items-center">
                <input type="text" id="buscar-historial" placeholder="Buscar en historial..."
                    class="px-4 py-2 border rounded-lg w-full md:max-w-md">

                <!-- Filtros adicionales -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full md:w-auto">
                    <select id="filtro-tipo" class="px-4 py-2 border rounded-lg bg-white w-full sm:w-auto">
                        <option value="todos">Todos los tipos</option>
                        <option value="consumible">Consumibles</option>
                        <option value="no_consumible">No Consumibles</option>
                    </select>

                    <select id="filtro-estado" class="px-4 py-2 border rounded-lg bg-white w-full sm:w-auto">
                        <option value="todos">Todos los estados</option>
                        <option value="devuelto">Devueltos</option>
                        <option value="prestado">No Devueltos</option>
                        <option value="consumida">Consumidos</option>
                    </select>

                    <button id="reset-filtros" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 w-full sm:w-auto whitespace-nowrap">
                        Reiniciar filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de historial -->
            <div class="table-container overflow-x-auto">
                <table id="tabla-historial" class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aprendiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Herramienta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cant.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha Préstamo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha
                                Devolución</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($historialPrestamos)): ?>
                            <?php foreach ($historialPrestamos as $prestamo): ?>
                                <?php
                                $rowColorClass = $prestamo['herramienta_tipo'] === 'consumible' ? 'bg-purple-200' : 'bg-blue-200';
                                $hoverClass = $prestamo['herramienta_tipo'] === 'consumible' ? 'hover:bg-purple-300' : 'hover:bg-blue-300';
                                ?>
                                <tr class="<?= $rowColorClass ?> <?= $hoverClass ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($prestamo['aprendiz'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($prestamo['herramienta'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $prestamo['herramienta_tipo'] === 'consumible' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= htmlspecialchars($prestamo['herramienta_tipo'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= htmlspecialchars($prestamo['cantidad'] ?? '0') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $estado = $prestamo['estado_prestamo'] ?? 'desconocido';
                                        $clases = [
                                            'prestado' => 'bg-yellow-100 text-yellow-800',
                                            'devuelto' => 'bg-green-100 text-green-800',
                                            'consumida' => 'bg-purple-100 text-purple-800',
                                            'default' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $clase = $clases[$estado] ?? $clases['default'];
                                        ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>"
                                            data-estado="<?= $estado ?>">
                                            <?= htmlspecialchars($estado) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= date('d/m/Y H:i', strtotime($prestamo['fecha_prestamo'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        if ($prestamo['herramienta_tipo'] === 'consumible') {
                                            echo date('d/m/Y H:i', strtotime($prestamo['fecha_prestamo']));
                                        } else {
                                            echo $prestamo['fecha_devolucion'] ? date('d/m/Y H:i', strtotime($prestamo['fecha_devolucion'])) : 'Pendiente';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron registros de préstamos
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nueva sección de Estadísticas y Gráficos -->
        <div id="stats-section"
            class="polymorphic-container bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Estadísticas y Gráficos</h2>

            <!-- Selector de período -->
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:flex-wrap md:items-center" id="periodo-selector">
                <span class="mr-4 font-medium">Período:</span>
                <div class="flex flex-wrap gap-2">
                    <a href="?periodo=diario" data-periodo="diario"
                        class="periodo-link px-4 py-2 rounded-lg border font-medium transition-colors duration-200 <?= $periodo === 'diario' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' ?>">
                        Diario
                    </a>
                    <a href="?periodo=semanal" data-periodo="semanal"
                        class="periodo-link px-4 py-2 rounded-lg border font-medium transition-colors duration-200 <?= $periodo === 'semanal' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' ?>">
                        Semanal
                    </a>
                    <a href="?periodo=mensual" data-periodo="mensual"
                        class="periodo-link px-4 py-2 rounded-lg border font-medium transition-colors duration-200 <?= $periodo === 'mensual' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' ?>">
                        Mensual
                    </a>
                    <a href="?periodo=anual" data-periodo="anual"
                        class="periodo-link px-4 py-2 rounded-lg border font-medium transition-colors duration-200 <?= $periodo === 'anual' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' ?>">
                        Anual
                    </a>
                </div>

                <!-- Botón para generar reporte PDF -->
                <a href="generar_reporte_estadistico.php?periodo=<?= $periodo ?>" target="_blank"
                    class="w-full md:w-auto md:ml-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2"></i> Generar PDF
                </a>
            </div>

            <h3 class="text-xl font-semibold text-center mb-4"><?= $titulo ?></h3>

            <!-- Pestañas para los diferentes gráficos -->
            <div class="mb-4 border-b border-gray-200">
                <div class="flex flex-wrap">
                    <button class="tab-button active" data-tab="tab-no-consumibles">
                        Herramientas No Consumibles
                    </button>
                    <button class="tab-button" data-tab="tab-consumibles">
                        Herramientas Consumibles
                    </button>
                    <button class="tab-button" data-tab="tab-aprendices">
                        Aprendices
                    </button>
                </div>
            </div>

            <!-- Contenido de las pestañas -->
            <div class="tab-content active" id="tab-no-consumibles">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Gráfico de barras para herramientas no consumibles -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Herramientas No Consumibles Más Prestadas</h4>
                        <div class="chart-container">
                            <canvas id="chartNoConsumibles"></canvas>
                        </div>
                    </div>

                    <!-- Tabla de herramientas no consumibles -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Detalle de Préstamos</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Herramienta</th>
                                        <th class="px-4 py-2 text-left">Total Préstamos</th>
                                        <th class="px-4 py-2 text-left">Total Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($herramientasNoConsumibles)): ?>
                                        <?php foreach ($herramientasNoConsumibles as $herramienta): ?>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['herramienta']) ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['total_prestamos']) ?>
                                                </td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['total_unidades']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">No hay datos
                                                disponibles para este período</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-consumibles">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Gráfico de barras para herramientas consumibles -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Herramientas Consumibles Más Utilizadas</h4>
                        <div class="chart-container">
                            <canvas id="chartConsumibles"></canvas>
                        </div>
                    </div>

                    <!-- Tabla de herramientas consumibles -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Detalle de Consumo</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Herramienta</th>
                                        <th class="px-4 py-2 text-left">Total Solicitudes</th>
                                        <th class="px-4 py-2 text-left">Total Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($herramientasConsumibles)): ?>
                                        <?php foreach ($herramientasConsumibles as $herramienta): ?>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['herramienta']) ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['total_prestamos']) ?>
                                                </td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($herramienta['total_unidades']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">No hay datos
                                                disponibles para este período</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-aprendices">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Gráfico de barras para aprendices -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Aprendices con Más Préstamos</h4>
                        <div class="chart-container">
                            <canvas id="chartAprendices"></canvas>
                        </div>
                    </div>

                    <!-- Tabla de aprendices -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium mb-2">Detalle por Aprendiz</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Aprendiz</th>
                                        <th class="px-4 py-2 text-left">Total Préstamos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($aprendicesTop)): ?>
                                        <?php foreach ($aprendicesTop as $aprendiz): ?>
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-4 py-2"><?= htmlspecialchars($aprendiz['aprendiz']) ?></td>
                                                <td class="px-4 py-2"><?= htmlspecialchars($aprendiz['total_prestamos']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="px-4 py-2 text-center text-gray-500">No hay datos
                                                disponibles para este período</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script type="application/json" id="stats-data-json"><?= json_encode([
                    'noConsumibles' => $datosHerramientasNoConsumibles,
                    'consumibles' => $datosHerramientasConsumibles,
                    'aprendices' => $datosAprendices
                ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
        </div>

        <!-- Modal para reporte de pendientes -->
        <div id="modal-reporte"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] overflow-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Reporte de Devoluciones Pendientes</h3>
                        <button onclick="hideModalReporte()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <?php if (!empty($aprendicesPendientes)): ?>
                        <div class="mb-4 overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Aprendiz</th>
                                        <th class="px-4 py-2 text-left">Herramientas Pendientes</th>
                                        <th class="px-4 py-2 text-left">Préstamos Pendientes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($aprendicesPendientes as $aprendiz): ?>
                                        <tr>
                                            <td class="px-4 py-2"><?= htmlspecialchars($aprendiz['nombre']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($aprendiz['herramientas_pendientes']) ?>
                                            </td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($aprendiz['prestamos_pendientes']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form id="form-reporte" method="POST">
                            <input type="hidden" name="generar_reporte" value="1">
                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="hideModalReporte()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                    Cancelar
                                </button>
                                <button type="button" onclick="confirmarGenerarReporte()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                    <i class="fas fa-save mr-2"></i> Guardar Reporte en BD
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            No hay aprendices con herramientas pendientes de devolución
                        </div>
                        <div class="flex justify-end">
                            <button onclick="hideModalReporte()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                Cerrar
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal Error Stock -->
        <div id="modal-error-stock"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                <h3 class="text-xl font-bold mb-4">Error de Stock</h3>
                <p id="stock-error-message"></p>
                <button onclick="document.getElementById('modal-error-stock').classList.add('hidden')"
                    class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Cerrar
                </button>
            </div>
        </div>

        <script>
            // Utilitarios
            const $ = id => document.getElementById(id);
            const $$ = selector => document.querySelectorAll(selector);

            // Sistema de Modales mejorado
            function showModal(type, title, message, options = {}) {
                const modal = $('modalSystem');
                const modalIcon = $('modalIcon');
                const modalTitle = $('modalTitle');
                const modalContent = $('modalContent');
                const modalHeader = $('modalHeader');
                const modalActions = $('modalActions');

                console.log(`Mostrando modal: ${type}, título: ${title}, duración: ${options.duration || 6000}ms`);

                if (modal.timeoutId) {
                    console.log('Limpiando temporizador previo:', modal.timeoutId);
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
                    console.log(`Configurando temporizador para cerrar en ${duration}ms`);
                    modal.timeoutId = setTimeout(() => {
                        console.log('Cerrando modal automáticamente');
                        hideModal();
                    }, Math.max(duration, 1000));
                }
            }

            function hideModal() {
                const modal = $('modalSystem');
                if (modal.timeoutId) {
                    console.log('Limpiando temporizador:', modal.timeoutId);
                    clearTimeout(modal.timeoutId);
                    delete modal.timeoutId;
                }
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                console.log('Modal cerrado');
            }

            // Funciones de conveniencia con tiempos mínimos garantizados
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

            // Función para modales persistentes
            function showPersistentModal(type, title, message) {
                showModal(type, title, message, { persistent: true });
            }

            // Función para confirmaciones personalizadas
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

            // Funciones específicas para el modal de reporte
            function mostrarModalReporte() {
                <?php if (empty($aprendicesPendientes)): ?>
                    showInfo('No hay aprendices con herramientas pendientes de devolución', 'Sin Pendientes', 7000);
                <?php else: ?>
                    $('modal-reporte').classList.remove('hidden');
                <?php endif; ?>
            }

            function hideModalReporte() {
                $('modal-reporte').classList.add('hidden');
            }

            function confirmarGenerarReporte() {
                // Cerrar el modal de reporte antes de mostrar el modal de confirmación
                hideModalReporte();
                showConfirm(
                    '¿Está seguro de que desea generar el reporte de devoluciones pendientes?',
                    'Confirmar Reporte',
                    () => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.innerHTML = `
                            <input type="hidden" name="generar_reporte" value="1">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                );
            }

            // Funciones para confirmaciones de devolución y rechazo de reserva
            function confirmarDevolucion(idPrestamo) {
                showConfirm(
                    '¿Confirmar devolución de la herramienta?',
                    'Confirmar Devolución',
                    () => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.innerHTML = `
                            <input type="hidden" name="id_prestamo" value="${idPrestamo}">
                            <input type="hidden" name="devolver" value="1">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                );
                return false;
            }

            function confirmarRechazoReserva(idReserva) {
                showConfirm(
                    '¿Está seguro de rechazar esta reserva? Esta acción no se puede deshacer.',
                    'Confirmar Rechazo de Reserva',
                    () => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.innerHTML = `
                            <input type="hidden" name="reserva_id" value="${idReserva}">
                            <input type="hidden" name="rechazar_reserva" value="1">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                );
                return false;
            }

            // Procesar reservas con modales
            async function procesarReserva(event, form, reservaId, tipoHerramienta) {
                event.preventDefault();

                const action = event.submitter.name === 'aceptar_reserva' ? 'aceptar' : 'rechazar';

                if (action === 'rechazar') {
                    return confirmarRechazoReserva(reservaId);
                }

                try {
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('reserva_id', reservaId);

                    const response = await fetch('includes/procesar_reserva.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.error) {
                        showError(data.message, 'Error', 10000);
                        return false;
                    }

                    showSuccess(data.message, 'Éxito', 1000);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                } catch (error) {
                    console.error('Error:', error);
                    showError('Error al procesar la reserva: ' + error.message, 'Error', 10000);
                }

                return false;
            }

            // Configuración de colores
            const colors = {
                primary: '#4A655D',
                secondary: '#56B847',
                tertiary: '#3B82F6',
                quaternary: '#EF4444'
            };

            // Datos para los gráficos
            const datosNoConsumibles = <?= json_encode($datosHerramientasNoConsumibles) ?>;
            const datosConsumibles = <?= json_encode($datosHerramientasConsumibles) ?>;
            const datosAprendices = <?= json_encode($datosAprendices) ?>;

            // Gráfico de herramientas no consumibles
            if (datosNoConsumibles.labels.length > 0) {
                const ctxNoConsumibles = document.getElementById('chartNoConsumibles').getContext('2d');
                new Chart(ctxNoConsumibles, {
                    type: 'bar',
                    data: {
                        labels: datosNoConsumibles.labels,
                        datasets: [{
                            label: 'Total Préstamos',
                            data: datosNoConsumibles.prestamos,
                            backgroundColor: colors.primary,
                            borderColor: colors.primary,
                            borderWidth: 1
                        }, {
                            label: 'Total Unidades',
                            data: datosNoConsumibles.unidades,
                            backgroundColor: colors.secondary,
                            borderColor: colors.secondary,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de herramientas consumibles
            if (datosConsumibles.labels.length > 0) {
                const ctxConsumibles = document.getElementById('chartConsumibles').getContext('2d');
                new Chart(ctxConsumibles, {
                    type: 'bar',
                    data: {
                        labels: datosConsumibles.labels,
                        datasets: [{
                            label: 'Total Solicitudes',
                            data: datosConsumibles.prestamos,
                            backgroundColor: colors.tertiary,
                            borderColor: colors.tertiary,
                            borderWidth: 1
                        }, {
                            label: 'Total Unidades',
                            data: datosConsumibles.unidades,
                            backgroundColor: colors.quaternary,
                            borderColor: colors.quaternary,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de aprendices
            if (datosAprendices.labels.length > 0) {
                const ctxAprendices = document.getElementById('chartAprendices').getContext('2d');
                new Chart(ctxAprendices, {
                    type: 'bar',
                    data: {
                        labels: datosAprendices.labels,
                        datasets: [{
                            label: 'Total Préstamos',
                            data: datosAprendices.prestamos,
                            backgroundColor: colors.secondary,
                            borderColor: colors.secondary,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Funcionalidad para búsqueda en tablas
            let dynamicStatsCharts = [];
            let periodFetchController = null;

            function destroyDynamicStatsCharts() {
                dynamicStatsCharts.forEach(chart => chart.destroy());
                dynamicStatsCharts = [];
            }

            function getStatsSectionData(section) {
                const dataNode = section.querySelector('#stats-data-json');
                if (!dataNode) return null;

                try {
                    return JSON.parse(dataNode.textContent);
                } catch (error) {
                    console.error('No se pudo parsear stats-data-json:', error);
                    return null;
                }
            }

            function renderBarChart(canvasId, labels, datasets) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !Array.isArray(labels) || labels.length === 0) return;

                const chart = new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: { labels, datasets },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });

                dynamicStatsCharts.push(chart);
            }

            function setStatsTabActive(button) {
                const statsSection = button.closest('#stats-section');
                if (!statsSection) return;

                statsSection.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                statsSection.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                button.classList.add('active');
                const targetTab = statsSection.querySelector(`#${button.dataset.tab}`);
                if (targetTab) {
                    targetTab.classList.add('active');
                }
            }

            function renderStatsAfterPeriodChange(statsSection, preferredTabId = null) {
                const statsData = getStatsSectionData(statsSection);
                if (!statsData) return;

                destroyDynamicStatsCharts();

                renderBarChart('chartNoConsumibles', statsData.noConsumibles?.labels || [], [
                    {
                        label: 'Total Prestamos',
                        data: statsData.noConsumibles?.prestamos || [],
                        backgroundColor: colors.primary,
                        borderColor: colors.primary,
                        borderWidth: 1
                    },
                    {
                        label: 'Total Unidades',
                        data: statsData.noConsumibles?.unidades || [],
                        backgroundColor: colors.secondary,
                        borderColor: colors.secondary,
                        borderWidth: 1
                    }
                ]);

                renderBarChart('chartConsumibles', statsData.consumibles?.labels || [], [
                    {
                        label: 'Total Solicitudes',
                        data: statsData.consumibles?.prestamos || [],
                        backgroundColor: colors.tertiary,
                        borderColor: colors.tertiary,
                        borderWidth: 1
                    },
                    {
                        label: 'Total Unidades',
                        data: statsData.consumibles?.unidades || [],
                        backgroundColor: colors.quaternary,
                        borderColor: colors.quaternary,
                        borderWidth: 1
                    }
                ]);

                renderBarChart('chartAprendices', statsData.aprendices?.labels || [], [
                    {
                        label: 'Total Prestamos',
                        data: statsData.aprendices?.prestamos || [],
                        backgroundColor: colors.secondary,
                        borderColor: colors.secondary,
                        borderWidth: 1
                    }
                ]);

                if (preferredTabId) {
                    const preferredButton = statsSection.querySelector(`.tab-button[data-tab="${preferredTabId}"]`);
                    if (preferredButton) {
                        setStatsTabActive(preferredButton);
                    }
                }
            }

            async function actualizarTemporalidad(url, pushState = true) {
                const currentStatsSection = document.getElementById('stats-section');
                if (!currentStatsSection) return;

                const activeTabId = currentStatsSection.querySelector('.tab-button.active')?.dataset.tab || null;
                const currentScrollY = window.scrollY;

                if (periodFetchController) {
                    periodFetchController.abort();
                }
                periodFetchController = new AbortController();

                currentStatsSection.classList.add('opacity-60', 'pointer-events-none');

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: periodFetchController.signal
                    });

                    if (!response.ok) {
                        throw new Error('No se pudo actualizar la temporalidad.');
                    }

                    const html = await response.text();
                    const parsedDoc = new DOMParser().parseFromString(html, 'text/html');
                    const newStatsSection = parsedDoc.getElementById('stats-section');

                    if (!newStatsSection) {
                        throw new Error('No se encontro la seccion de estadisticas en la respuesta.');
                    }

                    currentStatsSection.replaceWith(newStatsSection);
                    renderStatsAfterPeriodChange(newStatsSection, activeTabId);

                    if (pushState) {
                        const nextUrl = new URL(url, window.location.origin);
                        history.pushState(
                            { periodUrl: `${nextUrl.pathname}${nextUrl.search}` },
                            '',
                            `${nextUrl.pathname}${nextUrl.search}`
                        );
                    }

                    window.scrollTo({ top: currentScrollY, behavior: 'auto' });
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    console.error(error);
                    window.location.href = url;
                } finally {
                    const statsSection = document.getElementById('stats-section');
                    if (statsSection) {
                        statsSection.classList.remove('opacity-60', 'pointer-events-none');
                    }
                }
            }

            document.addEventListener('click', function (event) {
                const periodLink = event.target.closest('#stats-section .periodo-link');
                if (periodLink) {
                    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                        return;
                    }

                    event.preventDefault();
                    actualizarTemporalidad(periodLink.href, true);
                    return;
                }

                const tabButton = event.target.closest('#stats-section .tab-button');
                if (tabButton) {
                    setStatsTabActive(tabButton);
                }
            });

            window.addEventListener('popstate', function () {
                const popstateUrl = `${window.location.pathname}${window.location.search}`;
                actualizarTemporalidad(popstateUrl, false);
            });

            document.getElementById('buscar-activos').addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tabla-activos tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            document.getElementById('buscar-historial').addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-historial tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

            // Filtros para el historial
           function aplicarFiltros() {
    const tipoFiltro = document.getElementById('filtro-tipo').value;
    const estadoFiltro = document.getElementById('filtro-estado').value;
    const searchTerm = document.getElementById('buscar-historial').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla-historial tbody tr');

    rows.forEach(row => {
        const tipoHerramienta = row.querySelector('td:nth-child(3) span').textContent.toLowerCase().trim();
        const estado = row.querySelector('td:nth-child(5) span').dataset.estado.toLowerCase();
        const text = row.textContent.toLowerCase();

        const cumpleBusqueda = text.includes(searchTerm);
        const cumpleTipo = tipoFiltro === 'todos' || tipoHerramienta === tipoFiltro;
        const cumpleEstado = estadoFiltro === 'todos' || estado === estadoFiltro;

        row.style.display = cumpleBusqueda && cumpleTipo && cumpleEstado ? '' : 'none';
    });
}

            // Event listeners para los filtros
            document.getElementById('filtro-tipo').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro-estado').addEventListener('change', aplicarFiltros);
            document.getElementById('buscar-historial').addEventListener('input', aplicarFiltros);

            // Reiniciar filtros
            document.getElementById('reset-filtros').addEventListener('click', function () {
                document.getElementById('buscar-historial').value = '';
                document.getElementById('filtro-tipo').value = 'todos';
                document.getElementById('filtro-estado').value = 'todos';
                const rows = document.querySelectorAll('#tabla-historial tbody tr');
                rows.forEach(row => {
                    row.style.display = '';
                });
            });

            // Mostrar mensaje de éxito o error si está en la sesión
            <?php if (isset($_SESSION['success'])): ?>
                showSuccess('<?php echo htmlspecialchars($_SESSION['success']); ?>', 'Éxito', 7000);
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                showError('<?php echo htmlspecialchars($_SESSION['error']); ?>', 'Error', 10000);
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            // Mostrar mensaje de error de stock si está presente
            <?php if (isset($_SESSION['error_stock'])): ?>
                showError('<?php echo htmlspecialchars($_SESSION['error_stock']); ?>', 'Error de Stock', 10000);
                <?php unset($_SESSION['error_stock']); ?>
            <?php endif; ?>
        </script>
    </main>

    <footer class="text-white py-4" style="background-color: #2D3A36 !important;">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> SENA - Sistema de Gestión de Inventarios. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
