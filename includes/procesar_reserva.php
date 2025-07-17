<?php
require_once 'database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_POST['action']) || !isset($_POST['reserva_id'])) {
    echo json_encode(['error' => true, 'message' => 'Parámetros inválidos']);
    exit;
}

$action = $_POST['action'];
$idReserva = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'] ?? null;

if (!$idReserva || !$userId) {
    echo json_encode(['error' => true, 'message' => 'Datos inválidos']);
    exit;
}

$db->beginTransaction();

try {
    if ($action === 'aceptar') {
        // Obtener datos de la reserva
        $stmtReserva = $db->prepare("SELECT * FROM reservas_herramientas WHERE id = ?");
        $stmtReserva->execute([$idReserva]);
        $reserva = $stmtReserva->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            throw new Exception("Reserva no encontrada");
        }

        // Verificar stock disponible
        if ($reserva['tipo_herramienta'] === 'consumible') {
            $stmtStock = $db->prepare("SELECT cantidad FROM herramientas_consumibles WHERE id = ?");
        } else {
            $stmtStock = $db->prepare("SELECT cantidad FROM herramientas_no_consumibles WHERE id = ?");
        }
        $stmtStock->execute([$reserva['herramienta_id']]);
        $stockActual = $stmtStock->fetchColumn();

        if ($stockActual < $reserva['cantidad']) {
            throw new Exception("Stock insuficiente. Disponible: $stockActual, Solicitado: {$reserva['cantidad']}");
        }

       // Buscar o crear aprendiz
       $stmtAprendiz = $db->prepare("SELECT id FROM aprendices WHERE nombre = ? AND ficha = ?");
        $stmtAprendiz->execute([$reserva['nombre_aprendiz'], $reserva['ficha']]);
        $idAprendiz = $stmtAprendiz->fetchColumn();

        if (!$idAprendiz) {
            // Si no existe el aprendiz, lo creamos
            $stmtInsertAprendiz = $db->prepare("INSERT INTO aprendices (nombre, ficha) VALUES (?, ?)");
            $stmtInsertAprendiz->execute([$reserva['nombre_aprendiz'], $reserva['ficha']]);
            $idAprendiz = $db->lastInsertId();
        }
        // Actualizar stock
        if ($reserva['tipo_herramienta'] === 'consumible') {
            $updateStock = $db->prepare("UPDATE herramientas_consumibles SET cantidad = cantidad - ? WHERE id = ?");
        } else {
            $updateStock = $db->prepare("UPDATE herramientas_no_consumibles SET cantidad = cantidad - ? WHERE id = ?");
        }
        $updateStock->execute([$reserva['cantidad'], $reserva['herramienta_id']]);

        // Crear préstamo
        $stmtPrestamo = $db->prepare("INSERT INTO prestamos (id_aprendiz, usuario_id, herramienta_id, herramienta_tipo, cantidad, estado, fecha_prestamo) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmtPrestamo->execute([
            $idAprendiz,
            $userId,
            $reserva['herramienta_id'],
            $reserva['tipo_herramienta'],
            $reserva['cantidad'],
            $reserva['tipo_herramienta'] === 'consumible' ? 'consumida' : 'prestado'
        ]);

        // Actualizar estado de la reserva
        $stmtUpdateReserva = $db->prepare("UPDATE reservas_herramientas SET estado = 'aprobada' WHERE id = ?");
        $stmtUpdateReserva->execute([$idReserva]);

        // Registrar en auditorías para aceptación
        $stmtAudit = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmtAudit->execute([
            $userId,
            'aceptar_reserva',
            'reservas_herramientas',
            $idReserva,
            json_encode(['accion' => 'Reserva aceptada y préstamo creado'])
        ]);

        $db->commit();
        echo json_encode(['error' => false, 'message' => 'Reserva aceptada y préstamo creado con éxito']);

    } elseif ($action === 'rechazar') {
        // Eliminar la reserva
         $stmtUpdateReserva = $db->prepare("UPDATE reservas_herramientas SET estado = 'rechazada' WHERE id = ?");
        $stmtUpdateReserva->execute([$idReserva]);

        // Registrar en auditorías
        $stmtAudit = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmtAudit->execute([
            $userId,
            'rechazo_reserva',
            'reservas_herramientas',
            $idReserva,
            json_encode(['accion' => 'Reserva rechazada y eliminada'])
        ]);

        $db->commit();
        echo json_encode(['error' => false, 'message' => 'Reserva rechazada y eliminada correctamente']);
    }

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}