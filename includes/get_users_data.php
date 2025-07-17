<?php
session_start();
header('Content-Type: application/json');

try {
    // Verify session and database connection
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Acceso no autorizado', 401);
    }

    require 'database.php';

    // Check if current user is admin
    $stmt = $db->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_role = $stmt->fetchColumn();

    if ($user_role !== 'administrador') {
        throw new Exception('Acceso no autorizado: Se requieren privilegios de administrador', 403);
    }

    // Get all users
   $stmt = $db->query("SELECT id, 
   CONCAT(
       UPPER(SUBSTRING(SUBSTRING_INDEX(usuario, ' ', 1), 1, 1)), 
       LOWER(SUBSTRING(SUBSTRING_INDEX(usuario, ' ', 1), 2)),
       IF(LOCATE(' ', usuario) > 0, 
           CONCAT(' ', 
               UPPER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING(usuario, LOCATE(' ', usuario) + 1), ' ', 1), 1, 1)),
               LOWER(SUBSTRING(SUBSTRING_INDEX(SUBSTRING(usuario, LOCATE(' ', usuario) + 1), ' ', 1), 2))
           ),
           ''
       )
   ) as usuario, 
   rol, telefono 
   FROM usuarios WHERE activo = 1 ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getFile().':'.$e->getLine()
    ]);
}
?>