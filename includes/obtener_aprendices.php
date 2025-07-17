<?php
header('Content-Type: application/json');
require_once 'database.php';

try {
    $stmt = $db->query("SELECT id, nombre, ficha FROM aprendices WHERE activo = 1 ORDER BY nombre");
    $aprendices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($aprendices);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener aprendices: ' . $e->getMessage()]);
}
?>