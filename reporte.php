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
                              CONCAT(
                                  UPPER(SUBSTRING(hnc.nombre, 1, 1)),
                                  LOWER(SUBSTRING(hnc.nombre, 2))
                              ), 
                              ' (Cantidad: ', p.cantidad, ')'
                          ) SEPARATOR ', '
                      ) AS herramientas_pendientes
                    FROM aprendices a
                    JOIN prestamos p ON a.id = p.id_aprendiz
                    JOIN herramientas_no_consumibles hnc ON (p.herramienta_id = hnc.id AND p.herramienta_tipo = 'no_consumible')
                    WHERE p.estado = 'prestado'
                    GROUP BY a.id
                    HAVING COUNT(p.id) > 0";

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
                    // Verificar si ya existe un reporte no resuelto para este aprendiz en los últimos 3 días
                    $stmtCheck = $db->prepare("
                        SELECT COUNT(*) 
                        FROM reportes 
                        WHERE id_aprendiz = ? 
                        AND resuelto = 0
                        AND fecha_reporte >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
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

            // Opcional: Registrar en auditorías
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
    </style>
</head>

<body class="bg-gray-50">


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
        <!-- Mensajes de éxito/error -->
        <?php if (!empty($mensajeExito)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Éxito</p>
                <p><?= htmlspecialchars($mensajeExito) ?></p>
            </div>
        <?php elseif (!empty($mensajeError)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Error</p>
                <p><?= htmlspecialchars($mensajeError) ?></p>
            </div>
        <?php endif; ?>

        <!-- Sección de Préstamos Activos (No Consumibles) -->
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-toolbox mr-2"></i>Préstamos Activos
            </h2>

            <!-- Buscador -->
            <div class="mb-6">
                <input type="text" id="buscar-activos" placeholder="Buscar préstamos activos..."
                    class="px-4 py-2 border rounded-lg w-full max-w-md">
            </div>

            <!-- Tabla de préstamos activos -->
            <div class="table-container mb-4">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
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
                                                onclick="return confirm('¿Confirmar devolución de la herramienta?')">
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
                <button onclick="document.getElementById('modal-reporte').classList.remove('hidden')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium">
                    <i class="fas fa-exclamation-triangle mr-2"></i> GENERAR REPORTE DE DEVOLUCIONES PENDIENTES
                </button>
            </div>
        </div>


        <!-- ... seccion reservas ... -->
        <div class="bg-blue-50 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4"><i class="fas fa-clock mr-2"></i>Reservas Pendientes</h2>
            <div class="table-container">
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
                                <th class="px-6 py-3 text-left">Fecha Reserva</th>
                                <th class="px-6 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="toolsTableBody" class="divide-y divide-gray-200">
                            <?php foreach ($reservasPendientes as $reserva): ?>
                                <tr class="border-b hover:bg-blue-50"> <!-- Fila con hover azul claro -->
                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($reserva['nombre_aprendiz']) ?>
                                        <div class="text-sm text-gray-500">Ficha: <?= htmlspecialchars($reserva['ficha']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($reserva['nombre_herramienta']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($reserva['cantidad']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($reserva['fecha_reserva']) ?></td>
                                    <td class="px-6 py-4">
                                        <form method="post" class="inline" onsubmit="return procesarReserva(event, this);">
                                            <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                            <input type="hidden" name="herramienta_id"
                                                value="<?= $reserva['herramienta_id'] ?>">
                                            <input type="hidden" name="tipo_herramienta"
                                                value="<?= $reserva['tipo_herramienta'] ?>">
                                            <div class="flex items-center space-x-2">
                                                <button type="submit" name="aceptar_reserva"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                                                    <i class="fas fa-check-circle mr-2"></i> Aceptar
                                                </button>
                                                <button type="submit" name="rechazar_reserva"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center"
                                                    onclick="return confirm('¿Está seguro de rechazar esta reserva? Esta acción no se puede deshacer.')">
                                                    <i class="fas fa-times-circle mr-2"></i> Rechazar
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
            class="polymorphic-container bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-history mr-2"></i> Historial Completo de Préstamos
            </h2>

            <!-- Buscador -->
            <div class="mb-6 flex flex-wrap gap-4 items-center">
                <input type="text" id="buscar-historial" placeholder="Buscar en historial..."
                    class="px-4 py-2 border rounded-lg w-full max-w-md">

                <!-- Filtros adicionales -->
                <div class="flex gap-4">
                    <select id="filtro-tipo" class="px-4 py-2 border rounded-lg bg-white">
                        <option value="todos">Todos los tipos</option>
                        <option value="consumible">Consumibles</option>
                        <option value="no_consumible">No Consumibles</option>
                    </select>

                    <select id="filtro-estado" class="px-4 py-2 border rounded-lg bg-white">
                        <option value="todos">Todos los estados</option>
                        <option value="devuelto">Devueltos</option>
                        <option value="prestado">No Devueltos</option>
                        <option value="consumida">Consumidos</option>
                    </select>

                    <button id="reset-filtros" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700">
                        Reiniciar filtros
                    </button>
                </div>
            </div>



            <!-- Tabla de historial -->
            <div class="table-container">
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
                                // Definir la clase de color para la fila según el tipo de herramienta con colores más intensos
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
        <div
            class="polymorphic-container bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Estadísticas y Gráficos</h2>

            <!-- Selector de período -->
            <div class="mb-6 flex flex-wrap items-center">
                <span class="mr-4 font-medium">Período:</span>
                <div class="flex space-x-2">
                    <a href="?periodo=diario"
                        class="px-4 py-2 rounded-lg <?= $periodo === 'diario' ? 'bg-[#2D3A36] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        Diario
                    </a>
                    <a href="?periodo=semanal"
                        class="px-4 py-2 rounded-lg <?= $periodo === 'semanal' ? 'bg-[#2D3A36] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        Semanal
                    </a>
                    <a href="?periodo=mensual"
                        class="px-4 py-2 rounded-lg <?= $periodo === 'mensual' ? 'bg-[#2D3A36] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        Mensual
                    </a>
                    <a href="?periodo=anual"
                        class="px-4 py-2 rounded-lg <?= $periodo === 'anual' ? 'bg-[#2D3A36] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        Anual
                    </a>
                </div>

                <!-- Botón para generar reporte PDF -->
                <a href="generar_reporte_estadistico.php?periodo=<?= $periodo ?>" target="_blank"
                    class="ml-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
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
        </div>

        <!-- Modal para reporte de pendientes -->
        <div id="modal-reporte"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] overflow-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Reporte de Devoluciones Pendientes</h3>
                        <button onclick="document.getElementById('modal-reporte').classList.add('hidden')"
                            class="text-gray-500 hover:text-gray-700">
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

                        <form method="POST">
                            <input type="hidden" name="generar_reporte" value="1">
                            <div class="flex justify-end space-x-4">
                                <button type="button"
                                    onclick="document.getElementById('modal-reporte').classList.add('hidden')"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                    Cancelar
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                    <i class="fas fa-save mr-2"></i> Guardar Reporte en BD
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            No hay aprendices con herramientas pendientes de devolución
                        </div>
                        <div class="flex justify-end">
                            <button onclick="document.getElementById('modal-reporte').classList.add('hidden')"
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

        <!-- Modal de confirmación de reporte generado -->
        <div id="modal-confirmacion"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Reporte Generado</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500" id="modal-confirmacion-mensaje">
                            El reporte se ha guardado correctamente en la base de datos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <button type="button" onclick="cerrarModalConfirmacion()"
                            class="w-full flex justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i> Volver a la página principal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            async function procesarReserva(event, form) {
                event.preventDefault();

                const action = event.submitter.name === 'aceptar_reserva' ? 'aceptar' : 'rechazar';

                if (action === 'rechazar' && !confirm('¿Está seguro de rechazar esta reserva? Esta acción no se puede deshacer.')) {
                    return false;
                }

                try {
                    const formData = new FormData();
                    formData.append('action', action);
                    formData.append('reserva_id', form.querySelector('input[name="reserva_id"]').value);

                    const response = await fetch('includes/procesar_reserva.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.error) {
                        alert(data.message);
                        return false;
                    }

                    // Mostrar mensaje de éxito
                    alert(data.message);

                    // Recargar la página para actualizar la lista de reservas
                    window.location.reload();

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al procesar la reserva');
                }

                return false;
            }

            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', () => {
                    // Desactivar todas las pestañas
                    document.querySelectorAll('.tab-button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });

                    // Activar la pestaña seleccionada
                    button.classList.add('active');
                    document.getElementById(button.dataset.tab).classList.add('active');
                });
            });

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
            // Función para buscar en préstamos activos
            document.getElementById('buscar-activos').addEventListener('input', function () {
                const term = this.value.toLowerCase();
                const contenedor = this.closest('.polymorphic-container');
                const filas = contenedor.querySelectorAll('tbody tr');

                filas.forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });

            // Función para buscar en historial
            document.getElementById('buscar-historial').addEventListener('input', function () {
                const term = this.value.toLowerCase();
                const contenedor = this.closest('.polymorphic-container');
                const filas = contenedor.querySelectorAll('tbody tr');

                filas.forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });

            // Cerrar modal al hacer clic fuera del contenido
            document.getElementById('modal-reporte').addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });

            // Función para cerrar el modal de confirmación
            function cerrarModalConfirmacion() {
                document.getElementById('modal-confirmacion').classList.add('hidden');
                document.getElementById('modal-reporte').classList.add('hidden');
            }

            // Función para aplicar todos los filtros al historial
            function aplicarFiltrosHistorial() {
                const searchTerm = document.getElementById('buscar-historial').value.toLowerCase();
                const tipoFiltro = document.getElementById('filtro-tipo').value;
                const estadoFiltro = document.getElementById('filtro-estado').value;

                const rows = document.querySelectorAll('#tabla-historial tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const esTipoConsumible = row.classList.contains('bg-purple-200');
                    const tipo = esTipoConsumible ? 'consumible' : 'no_consumible';

                    // Mejor detección del estado usando data-attribute
                    const estadoElement = row.querySelector('td:nth-child(5) span');
                    let estado = estadoElement ? estadoElement.getAttribute('data-estado') || estadoElement.textContent.trim().toLowerCase() : '';

                    // Normalización de estados
                    if (estado.includes('devuelto')) estado = 'devuelto';
                    else if (estado.includes('prestado')) estado = 'prestado';
                    else if (estado.includes('consumid')) estado = 'consumida';

                    const cumpleBusqueda = text.includes(searchTerm);
                    const cumpleTipo = tipoFiltro === 'todos' || tipo === tipoFiltro;
                    const cumpleEstado = estadoFiltro === 'todos' || estado === estadoFiltro;

                    row.style.display = (cumpleBusqueda && cumpleTipo && cumpleEstado) ? '' : 'none';
                });
            }
            // Asignar eventos a los filtros
            document.getElementById('buscar-historial').addEventListener('input', aplicarFiltrosHistorial);
            document.getElementById('filtro-tipo').addEventListener('change', aplicarFiltrosHistorial);
            document.getElementById('filtro-estado').addEventListener('change', aplicarFiltrosHistorial);

            // Botón para reiniciar filtros
            document.getElementById('reset-filtros').addEventListener('click', function () {
                document.getElementById('buscar-historial').value = '';
                document.getElementById('filtro-tipo').value = 'todos';
                document.getElementById('filtro-estado').value = 'todos';
                aplicarFiltrosHistorial();
            });
        </script>
    </main>
</body>

</html>