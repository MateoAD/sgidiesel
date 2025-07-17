<?php
session_start();
header('Content-Type: application/json');

try {
    // Verify admin privileges
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
        throw new Exception('Acceso denegado: Se requieren privilegios de administrador', 403);
    }

    // Validate input
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        throw new Exception('ID de usuario no proporcionado', 400);
    }

    require 'database.php';
    $db->beginTransaction();
    
    $userId = (int)$_POST['user_id'];
    $adminId = (int)$_SESSION['user_id'];

    // Prevent self-deletion
    if ($userId === $adminId) {
        throw new Exception('No puedes eliminar tu propio usuario', 400);
    }

    // Verify user exists
    $stmt = $db->prepare("SELECT id, usuario, rol FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('Usuario no encontrado', 404);
    }

    // Register audit
    $detalles = [
        'descripcion' => 'Desactivación de usuario',
        'usuario' => $user['usuario'],
        'rol' => $user['rol']
    ];
    
    $stmtAudit = $db->prepare("INSERT INTO auditorias 
        (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion, ip_usuario) 
        VALUES (:usuario_id, 'eliminar', 'usuarios', :id, :detalles, NOW(), :ip)");
    
    $stmtAudit->execute([
        ':usuario_id' => $adminId,
        ':id' => $userId,
        ':detalles' => json_encode($detalles),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);

    // Update related records in other tables to set usuario_id to NULL
    $tables = [
        'auditorias',
        'herramientas_consumibles',
        'herramientas_no_consumibles',
        'prestamos'
    ];

    foreach ($tables as $table) {
        try {
            $stmt = $db->prepare("UPDATE $table SET usuario_id = NULL WHERE usuario_id = ?");
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            throw new Exception("Error updating $table: " . $e->getMessage());
        }
    }

  // En lugar de eliminar, actualizar el estado a inactivo
  $stmt = $db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
  $stmt->execute([$userId]);
  
  if ($stmt->rowCount() === 0) {
      throw new Exception('Usuario no encontrado', 404);
  }


    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Usuario eliminado correctamente'
    ]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getFile().':'.$e->getLine()
    ]);
}
?>