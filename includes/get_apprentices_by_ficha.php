<?php
session_start();
require 'database.php';

header('Content-Type: application/json');

$ficha = $_GET['ficha'] ?? '';

try {
       $stmt = $db->prepare("
        SELECT 
            a.id,
            CONCAT(
                UPPER(SUBSTRING(SUBSTRING_INDEX(a.nombre, ' ', 1), 1, 1)),
                LOWER(SUBSTRING(SUBSTRING_INDEX(a.nombre, ' ', 1), 2)),
                IF(LOCATE(' ', a.nombre) > 0, 
                    CONCAT(' ', 
                        UPPER(SUBSTRING(SUBSTRING_INDEX(a.nombre, ' ', -1), 1, 1)),
                        LOWER(SUBSTRING(SUBSTRING_INDEX(a.nombre, ' ', -1), 2))
                    ),
                    ''
                )
            ) as nombre,
            a.ficha,
            COUNT(CASE WHEN r.resuelto = 0 THEN 1 END) as reportes
        FROM aprendices a
        LEFT JOIN reportes r ON a.id = r.id_aprendiz
        WHERE a.ficha = ? AND a.activo = 1
        GROUP BY a.id, a.nombre, a.ficha
        ORDER BY a.nombre
    ");
    $stmt->execute([$ficha]);
    echo json_encode($stmt->fetchAll());
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}