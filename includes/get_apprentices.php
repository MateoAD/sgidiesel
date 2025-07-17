<?php
session_start();
require 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $stmt = $db->query("SELECT id, nombre, ficha FROM aprendices WHERE activo = 1 ORDER BY ficha, nombre");
    $apprentices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($apprentices);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener los aprendices: ' . $e->getMessage()]);
}