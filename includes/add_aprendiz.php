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
    // Validate input
    if (!isset($_POST['nombre']) || !isset($_POST['ficha'])) {
        throw new Exception('Datos incompletos');
    }
    
    $nombre = trim($_POST['nombre']);
    $ficha = trim($_POST['ficha']);
    
    if (empty($nombre) || empty($ficha)) {
        throw new Exception('Nombre y ficha son obligatorios');
    }
    
    // Check if aprendiz already exists
    $stmt = $db->prepare("SELECT id FROM aprendices WHERE nombre = ? AND ficha = ?");
    $stmt->execute([$nombre, $ficha]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('El aprendiz ya existe en el sistema');
    }
    
    // Insert new aprendiz
    $stmt = $db->prepare("INSERT INTO aprendices (nombre, ficha, activo) VALUES (?, ?, 1)");
    $stmt->execute([$nombre, $ficha]);
    
    // Get the new aprendiz ID for audit purposes
    $aprendizId = $db->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Aprendiz agregado correctamente',
        'aprendiz_id' => $aprendizId
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>