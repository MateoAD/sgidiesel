<?php
header('Content-Type: application/json');
require_once 'database.php';

$codigo = $_GET['codigo'] ?? '';

try {
    // Buscar en herramientas consumibles
    $stmt = $db->prepare("SELECT id, nombre, cantidad, foto, 'consumible' as tipo 
                         FROM herramientas_consumibles 
                         WHERE codigo_barras = ? LIMIT 1");
    $stmt->execute([$codigo]);
    $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si no se encuentra, buscar en no consumibles
    if (!$herramienta) {
        $stmt = $db->prepare("SELECT id, nombre, cantidad, foto, 'no_consumible' as tipo 
                             FROM herramientas_no_consumibles 
                             WHERE codigo_barras = ? LIMIT 1");
        $stmt->execute([$codigo]);
        $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$herramienta) {
        http_response_code(404);
        echo json_encode(['error' => 'Herramienta no encontrada']);
        exit;
    }
    
    echo json_encode($herramienta);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
}
?>