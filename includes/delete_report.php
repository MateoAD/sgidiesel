<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once 'database.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Check if using JSON or form data
    if (isset($input['id'])) {
        $reporteId = (int)$input['id'];
    } else if (isset($_POST['id'])) {
        $reporteId = (int)$_POST['id'];
    } else {
        throw new Exception('ID de reporte inválido');
    }
    
    // Validate ID
    if (!$reporteId) {
        throw new Exception('ID de reporte inválido');
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    // Get report info before deletion (for audit purposes)
    $stmtInfo = $db->prepare("SELECT r.id, r.id_aprendiz, r.observaciones, a.nombre 
                             FROM reportes r 
                             JOIN aprendices a ON r.id_aprendiz = a.id 
                             WHERE r.id = ?");
    $stmtInfo->execute([$reporteId]);
    $reporteInfo = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    
    if (!$reporteInfo) {
        throw new Exception('Reporte no encontrado');
    }
    
    // Delete the report
    $stmt = $db->prepare("DELETE FROM reportes WHERE id = ?");
    $stmt->execute([$reporteId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se pudo eliminar el reporte');
    }
    
    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Reporte eliminado correctamente']);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>