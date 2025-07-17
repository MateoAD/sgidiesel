<?php
session_start();
header('Content-Type: application/json');
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Usuario no autenticado'
    ]);
    exit;
}

$response = ['success' => false, 'message' => '', 'error' => null];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }

    // Validaciones básicas
    $required = ['herramienta_id', 'herramienta_tipo', 'aprendiz_id', 'cantidad'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    $herramienta_id = (int) $data['herramienta_id'];
    $herramienta_tipo = $data['herramienta_tipo'];
    $id_aprendiz = (int) $data['aprendiz_id'];
    $cantidad = (int) $data['cantidad'];
    $descripcion = isset($data['descripcion']) ? trim($data['descripcion']) : null;

    if ($cantidad <= 0) {
        throw new Exception("La cantidad debe ser mayor a 0", 400);
    }

    // Validación de tipo de herramienta
    if (!in_array($herramienta_tipo, ['consumible', 'no_consumible'])) {
        throw new Exception("Tipo de herramienta no válido", 400);
    }

    // Verificar existencia y stock de la herramienta
    if ($herramienta_tipo === 'consumible') {
        $stmt = $db->prepare("SELECT id, nombre, cantidad, estado FROM herramientas_consumibles 
                     WHERE id = ? AND cantidad >= ?");
    } else {
        $stmt = $db->prepare("SELECT id, nombre, cantidad, estado FROM herramientas_no_consumibles 
                             WHERE id = ? AND cantidad >= ? AND estado = 'Activa'");
    }

    $stmt->execute([$herramienta_id, $cantidad]);
    $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$herramienta) {
        // Get current stock for better error message
        if ($herramienta_tipo === 'consumible') {
            $stockStmt = $db->prepare("SELECT cantidad, estado FROM herramientas_consumibles WHERE id = ?");
        } else {
            $stockStmt = $db->prepare("SELECT cantidad, estado FROM herramientas_no_consumibles WHERE id = ?");
        }
        $stockStmt->execute([$herramienta_id]);
        $stockInfo = $stockStmt->fetch(PDO::FETCH_ASSOC);

        if (!$stockInfo) {
            throw new Exception("Herramienta no encontrada", 404);
        } else {
            throw new Exception("Stock insuficiente. Disponible: {$stockInfo['cantidad']}, Estado: {$stockInfo['estado']}", 400);
        }
    }

    // Verificar que el aprendiz existe
    $stmt = $db->prepare("SELECT id FROM aprendices WHERE id = ?");
    $stmt->execute([$id_aprendiz]);
    if (!$stmt->fetch()) {
        throw new Exception("Aprendiz no encontrado", 404);
    }

    // Verificar si el aprendiz tiene reportes pendientes
    $stmt = $db->prepare("SELECT COUNT(*) FROM reportes WHERE id_aprendiz = ? AND resuelto = 0");
    $stmt->execute([$id_aprendiz]);
    $reportesPendientes = $stmt->fetchColumn();

    if ($reportesPendientes > 0) {
        throw new Exception("El aprendiz tiene reportes pendientes. No se puede registrar el préstamo.", 400);
    }

    // Iniciar transacción
    $db->beginTransaction();

    try {
        // Verificar que el usuario existe y usar un ID válido
        $user_stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ?");
        $user_stmt->execute([$_SESSION['user_id']]);
        $usuario_valido = $user_stmt->fetch(PDO::FETCH_ASSOC);

        // Si el usuario no existe, usar un ID de administrador por defecto (5)
        $usuario_id = $usuario_valido ? $_SESSION['user_id'] : 10;

        // Establecer variable global para el trigger
        $db->exec("SET @current_user_id = " . $usuario_id);

        // Registrar préstamo con usuario_id válido
        $stmt = $db->prepare("INSERT INTO prestamos 
                            (id_aprendiz, herramienta_id, herramienta_tipo, cantidad, usuario_id, fecha_prestamo, estado, descripcion) 
                            VALUES (?, ?, ?, ?, ?, NOW(), 'prestado', ?)");
        $stmt->execute([$id_aprendiz, $herramienta_id, $herramienta_tipo, $cantidad, $usuario_id, $descripcion]);

        // Actualizar stock
        if ($herramienta_tipo === 'consumible') {
            $update_sql = "UPDATE herramientas_consumibles 
                          SET cantidad = cantidad - ? 
                          WHERE id = ?";
            $params = [$cantidad, $herramienta_id];
        } else {
            $update_sql = "UPDATE herramientas_no_consumibles 
                          SET cantidad = cantidad - ?,
                              estado = CASE 
                                WHEN (cantidad - ?) = 0 THEN 'Prestado'
                                ELSE estado 
                              END
                          WHERE id = ?";
            $params = [$cantidad, $cantidad, $herramienta_id];
        }

        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute($params);

        $db->commit();

        $response['success'] = true;
        $response['message'] = 'Préstamo registrado exitosamente';
        $response['stock_actual'] = $herramienta['cantidad'] - $cantidad;

    } catch (PDOException $e) {
        $db->rollBack();
        throw new Exception("Error al registrar el préstamo: " . $e->getMessage(), 500);
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    http_response_code($e->getCode() ?: 400);
} catch (PDOException $e) {
    $response['success'] = false;
    $response['error'] = 'Error de base de datos: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;