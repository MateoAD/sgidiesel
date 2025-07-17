<?php
require_once 'database.php';

header('Content-Type: application/json');

// Verificar método POST
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Error al leer los datos de entrada');
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }

    // Validaciones mejoradas
    if (!isset($data['token']) || empty(trim($data['token']))) {
        throw new Exception('Token es requerido');
    }

    if (!isset($data['newPassword']) || empty(trim($data['newPassword']))) {
        throw new Exception('Nueva contraseña es requerida');
    }

    if (strlen($data['newPassword']) < 8) {
        throw new Exception('La contraseña debe tener al menos 8 caracteres');
    }

    // Verificar token
    $query = "SELECT id FROM usuarios WHERE token_recuperacion = ? AND expiracion_token > NOW() LIMIT 1";
    $stmt = $db->prepare($query);
    if (!$stmt->execute([$data['token']])) {
        throw new Exception('Error al verificar el token');
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        throw new Exception('Token inválido o expirado');
    }

    // Actualizar contraseña
    $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);
    $updateQuery = "UPDATE usuarios SET contraseña = ?, token_recuperacion = NULL, expiracion_token = NULL WHERE id = ?";
    
    $db->beginTransaction();
    $updateStmt = $db->prepare($updateQuery);
    
    if (!$updateStmt->execute([$hashedPassword, $user['id']])) {
        $db->rollBack();
        throw new Exception('Error al actualizar la contraseña');
    }

    if ($updateStmt->rowCount() === 0) {
        $db->rollBack();
        throw new Exception('No se encontró el usuario para actualizar');
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('PDOException en reset_password: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor. Detalles: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>