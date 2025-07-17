<?php
session_start();
require 'database.php';

header('Content-Type: application/json');

$apprentice_id = $_GET['id'] ?? 0;

try {
    $stmt = $db->prepare("
        SELECT id, observaciones, fecha_reporte
        FROM reportes
        WHERE id_aprendiz = ? AND resuelto = 0
        ORDER BY fecha_reporte DESC
    ");
    $stmt->execute([$apprentice_id]);
    echo json_encode($stmt->fetchAll());
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}