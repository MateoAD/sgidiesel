<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Cambiar a 1 para depuración

header('Content-Type: application/json');

session_start();
require 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['ficha']) || !isset($data['nombres']) || empty($data['nombres'])) {
        throw new Exception('Datos incompletos');
    }

    $db->beginTransaction();
    
    $count = 0;
    $stmt = $db->prepare("INSERT INTO aprendices (nombre, ficha, activo) VALUES (?, ?, 1)");
    
    foreach ($data['nombres'] as $nombre) {
        if (!empty(trim($nombre))) {
            $stmt->execute([trim($nombre), trim($data['ficha'])]);
            $count++;
        }
    }

    $db->commit();
    echo json_encode(['success' => true, 'count' => $count]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error en save_apprentices.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al guardar los aprendices: ' . $e->getMessage(),
        'error_details' => $e->getTraceAsString()
    ]);
}