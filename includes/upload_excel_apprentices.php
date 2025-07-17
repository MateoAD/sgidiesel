<?php
session_start();
require 'database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar si se ha enviado un archivo
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se ha enviado un archivo válido']);
    exit;
}

// Verificar la extensión del archivo
$fileName = $_FILES['excelFile']['name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if ($fileExt !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'El archivo debe ser un CSV (.csv)']);
    exit;
}

try {
    // Crear un objeto temporal para el archivo
    $tempFile = $_FILES['excelFile']['tmp_name'];
    
    // Iniciar transacción
    $db->beginTransaction();
    
    // Preparar la consulta para insertar aprendices
    $stmt = $db->prepare("INSERT INTO aprendices (nombre, ficha, activo) VALUES (?, ?, 1)");
    
    // Contador de registros insertados
    $count = 0;
    
    // Procesar CSV directamente - Especificando punto y coma como separador
    if (($handle = fopen($tempFile, 'r')) !== FALSE) {
        // Leer la primera línea (encabezados) y descartarla
        fgetcsv($handle, 0, ';');
        
        // Procesar las filas
        while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
            // Verificar que la fila tenga datos válidos para nombre y ficha
            if (isset($row[0]) && !empty(trim($row[0])) && isset($row[2]) && !empty(trim($row[2]))) {
                $nombre = trim($row[0]);
                $ficha = trim($row[2]);
                
                // Insertar el aprendiz
                $stmt->execute([$nombre, $ficha]);
                $count++;
            }
        }
        fclose($handle);
    } else {
        throw new Exception('No se pudo abrir el archivo CSV');
    }
    
    // Confirmar la transacción
    $db->commit();
    
    echo json_encode(['success' => true, 'count' => $count, 'message' => 'Aprendices cargados exitosamente']);
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Error en upload_excel_apprentices.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al procesar el archivo: ' . $e->getMessage()
    ]);
}
?>