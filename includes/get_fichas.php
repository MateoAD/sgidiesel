<?php
session_start();
require 'database.php';

header('Content-Type: application/json');

try {
    $stmt = $db->query("SELECT DISTINCT ficha FROM aprendices WHERE activo = 1 ORDER BY ficha");
    $fichas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($fichas);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}