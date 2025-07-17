<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

try {

    // Consulta para herramientas prestadas hoy (solo no consumibles)
    $hoy = date('Y-m-d');
    $queryHerramientasHoy = "SELECT SUM(p.cantidad) as total 
                            FROM prestamos p
                            WHERE p.estado = 'prestado' 
                            AND DATE(p.fecha_prestamo) = :hoy
                            AND p.herramienta_tipo = 'no_consumible'";
    $stmtHerramientasHoy = $db->prepare($queryHerramientasHoy);
    $stmtHerramientasHoy->bindParam(':hoy', $hoy);
    $stmtHerramientasHoy->execute();
    $herramientasHoy = $stmtHerramientasHoy->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Consulta para préstamos activos (solo no consumibles)
    $queryPrestamosActivos = "SELECT COUNT(*) as total 
                             FROM prestamos p
                             WHERE p.estado = 'prestado'
                             AND p.herramienta_tipo = 'no_consumible'";
    $stmtPrestamosActivos = $db->query($queryPrestamosActivos);
    $prestamosActivos = $stmtPrestamosActivos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Consulta corregida para préstamos por devolver (solo no consumibles)
    $queryPorDevolver = "SELECT COUNT(*) as total 
    FROM prestamos p
    WHERE p.estado = 'prestado' 
    AND p.herramienta_tipo = 'no_consumible'";
    $stmtPorDevolver = $db->prepare($queryPorDevolver);
    $stmtPorDevolver->execute();
    $porDevolver = $stmtPorDevolver->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Formatear respuesta
    echo json_encode([
        'success' => true,
        'data' => [
            'herramientas_hoy' => (int)$herramientasHoy,
            'prestamos_activos' => (int)$prestamosActivos,
            'por_devolver' => (int)$porDevolver,
            'fecha_actual' => date('d/m/Y'),
            'hora_actualizacion' => date('H:i')
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>