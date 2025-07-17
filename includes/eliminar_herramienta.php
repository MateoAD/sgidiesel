<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

try {
  
    
    // Get user ID from session or use default admin user
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 10; // Default to admin user
    
    // Set user ID for database triggers
    $db->exec("SET @current_user_id = " . $userId);
    
    // Disable audit trigger temporarily to prevent duplicate entries
    $db->exec("SET @disable_audit_trigger = 1");

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $tipo = $data['tipo'] ?? null;
    
    if (!$id || !$tipo) {
        throw new Exception("ID y tipo son requeridos");
    }
    
    $tabla = $tipo === 'no_consumible' ? 'herramientas_no_consumibles' : 'herramientas_consumibles';

    // Get tool information before deleting for audit record
    $stmt = $db->prepare("SELECT nombre, cantidad FROM $tabla WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$herramienta) {
        throw new Exception("Herramienta no encontrada");
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Create audit record manually
        $detalles = json_encode([
            'descripcion' => "Herramienta {$tipo} eliminada",
            'nombre' => $herramienta['nombre'],
            'cantidad' => $herramienta['cantidad']
        ]);
        
        $audit_stmt = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion, ip_usuario) 
                                  VALUES (?, 'eliminar', ?, ?, ?, NOW(), ?)");
        $audit_stmt->execute([
            $userId, 
            $tabla, 
            $id, 
            $detalles,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        // Delete the tool
        $delete_stmt = $db->prepare("DELETE FROM $tabla WHERE id = :id");
        $delete_stmt->bindParam(':id', $id);
        $delete_stmt->execute();
        
        // Re-enable audit trigger
        $db->exec("SET @disable_audit_trigger = NULL");
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Herramienta eliminada correctamente']);
    } catch (Exception $e) {
        $db->rollBack();
        // Re-enable audit trigger even if there's an error
        $db->exec("SET @disable_audit_trigger = NULL");
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>