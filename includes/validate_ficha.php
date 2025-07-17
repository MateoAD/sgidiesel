<?php
require_once 'database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (empty($data['ficha'])) {
        throw new Exception('La ficha es requerida');
    }

    $ficha = trim($data['ficha']);

    // Verificar si la ficha existe en la base de datos
    $stmt = $db->prepare("SELECT COUNT(*) FROM aprendices WHERE ficha = ?");
    $stmt->execute([$ficha]);
    
    echo json_encode([
        'valid' => $stmt->fetchColumn() > 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'valid' => false,
        'message' => $e->getMessage()
    ]);
}