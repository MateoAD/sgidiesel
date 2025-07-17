<?php
session_start();
header('Content-Type: application/json');

require_once 'database.php';

try {
    // Validate required parameters
    $requiredParams = ['accion', 'tabla_afectada', 'registro_id', 'detalles'];
    foreach ($requiredParams as $param) {
        if (!isset($_POST[$param])) {
            throw new Exception("Parámetro requerido: $param");
        }
    }
    
    // Get user ID from session or use default admin user
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; // Default to admin user
    
    // Set user ID for database triggers
    $db->exec("SET @current_user_id = " . $userId);
    
    // Check if we should disable audit triggers (for operations that handle their own audit)
    if (isset($_POST['disable_triggers']) && $_POST['disable_triggers'] == '1') {
        $db->exec("SET @disable_audit_trigger = 1");
    }
    
    // Prepare data
    $accion = $_POST['accion'];
    $tabla_afectada = $_POST['tabla_afectada'];
    $registro_id = intval($_POST['registro_id']);
    $detalles = $_POST['detalles'];
    $ip_usuario = $_SERVER['REMOTE_ADDR'] ?? null;
    
    // Validate action type
    $valid_actions = ['crear', 'modificar', 'eliminar', 'prestamo', 'devolucion', 'cambio_estado'];
    if (!in_array($accion, $valid_actions)) {
        throw new Exception("Acción no válida: $accion");
    }
    
    // Insert audit record
    $stmt = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_usuario) 
                         VALUES (?, ?, ?, ?, ?, ?)");
    
    $success = $stmt->execute([
        $userId,
        $accion,
        $tabla_afectada,
        $registro_id,
        $detalles,
        $ip_usuario
    ]);
    
    if (!$success) {
        throw new Exception("Error al registrar auditoría");
    }
    
    // Reset the disable trigger flag if it was set
    if (isset($_POST['disable_triggers']) && $_POST['disable_triggers'] == '1') {
        $db->exec("SET @disable_audit_trigger = NULL");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Auditoría registrada correctamente',
        'id' => $db->lastInsertId()
    ]);
    
} catch (Exception $e) {
    // Reset the disable trigger flag in case of error
    if (isset($_POST['disable_triggers']) && $_POST['disable_triggers'] == '1') {
        $db->exec("SET @disable_audit_trigger = NULL");
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>