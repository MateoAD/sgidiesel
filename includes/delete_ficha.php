<?php
// Mostrar todos los errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegurarse de que no haya salida antes de los headers
ob_start();

// Conectar a la base de datos
include 'database.php';

// Obtener los datos enviados por la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que se haya proporcionado la ficha
if (isset($data['ficha'])) {
    $ficha = $data['ficha'];

    try {
        // Actualizar los aprendices con la ficha especificada para marcarlos como inactivos
        $query = "UPDATE aprendices SET activo = 0 WHERE ficha = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$ficha]);

        // Verificar si se actualizó correctamente
        if ($stmt->rowCount() > 0) {
            ob_end_clean(); // Limpiar cualquier salida anterior
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Ficha eliminada correctamente.']);
        } else {
            ob_end_clean(); // Limpiar cualquier salida anterior
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la ficha o la ficha no existe.']);
        }
    } catch (Exception $e) {
        ob_end_clean(); // Limpiar cualquier salida anterior
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    ob_end_clean(); // Limpiar cualquier salida anterior
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Ficha no proporcionada.']);
}
?>