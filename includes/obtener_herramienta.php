<?php
header('Content-Type: application/json');
require 'database.php';
try {


    $id = $_GET['id'] ?? null;
    $tipo = $_GET['tipo'] ?? null;
    $tabla = $tipo === 'no_consumible' ? 'herramientas_no_consumibles' : 'herramientas_consumibles';

    $stmt = $db->prepare("SELECT * FROM $tabla WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$herramienta) {
        throw new Exception('Herramienta no encontrada');
    }
    
    // Añadir URL completa de la foto si existe
    if (!empty($herramienta['foto'])) {
        $herramienta['foto_url'] = 'uploads/herramientas/' . $herramienta['foto'];
    } else {
        $herramienta['foto_url'] = null;
    }

    echo json_encode(['success' => true, ...$herramienta]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>