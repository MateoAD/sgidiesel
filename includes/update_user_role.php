<?php
session_start();
header('Content-Type: application/json');

try {
    // Verify admin privileges
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
        throw new Exception('Acceso denegado: Se requieren privilegios de administrador', 403);
    }

    // Validate input
    if (!isset($_POST['user_id']) || empty($_POST['user_id']) || !isset($_POST['role'])) {
        throw new Exception('Datos incompletos', 400);
    }

    require 'database.php';
    $db->beginTransaction();
    
    $userId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];
    $adminId = (int)$_SESSION['user_id'];

    // Prevent self-modification
    if ($userId === $adminId) {
        throw new Exception('No puedes modificar tu propio rol', 400);
    }

    // Verify user exists
    $stmt = $db->prepare("SELECT id, usuario, rol FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('Usuario no encontrado', 404);
    }

   // Update role - modified to force 'aprendiz' when revoking admin
   $newRole = ($newRole === 'administrador' || $newRole === 'almacenista') ? $newRole : 'aprendiz';
   $stmt = $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
   $stmt->execute([$newRole, $userId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se pudo actualizar el rol', 500);
    }

    // Register audit
    $detalles = [
        'descripcion' => 'Cambio de rol de usuario',
        'usuario' => $user['usuario'],
        'rol_anterior' => $user['rol'],
        'rol_nuevo' => $newRole
    ];
    
    $stmtAudit = $db->prepare("INSERT INTO auditorias 
        (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion, ip_usuario) 
        VALUES (:usuario_id, 'modificar', 'usuarios', :id, :detalles, NOW(), :ip)");
    
    $stmtAudit->execute([
        ':usuario_id' => $adminId,
        ':id' => $userId,
        ':detalles' => json_encode($detalles),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Rol actualizado correctamente'
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