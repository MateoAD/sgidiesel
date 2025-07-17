<?php
// Configuración inicial
header('Content-Type: application/json');
try {
    // Incluir archivo de conexión usando ruta relativa segura
    require_once __DIR__ . '/database.php';
    
    // Verificar conexión de manera explícita
    if (!isset($db)) {
        throw new Exception("Variable de conexión \$db no definida");
    }
    
    if (!($db instanceof PDO)) {
        throw new Exception("Conexión no es instancia de PDO");
    }
    
    // Consulta segura para obtener herramientas con bajo stock
    $query = "SELECT id, nombre, cantidad, estado, ubicacion 
              FROM herramientas_consumibles 
              WHERE estado = 'recargar' OR estado = 'medio'
              ORDER BY FIELD(estado, 'recargar', 'medio'), cantidad ASC";
    
    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . implode(" ", $db->errorInfo()));
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo()));
    }
    
    $tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Para debug - añadir una consulta que muestre todas las herramientas
    $debugQuery = "SELECT id, nombre, cantidad, estado, ubicacion FROM herramientas_consumibles";
    $debugStmt = $db->prepare($debugQuery);
    $debugStmt->execute();
    $allTools = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Preparar respuesta exitosa
    $response = [
        'success' => true,
        'data' => $tools,
        'count' => count($tools),
        'debug_all_tools' => $allTools, // Solo para depuración
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Enviar respuesta JSON
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(500);
    
    $errorData = [
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Solo incluir traza en entorno de desarrollo
    if (ini_get('display_errors') === '1') {
        $errorData['trace'] = $e->getTrace();
    }
    
    echo json_encode($errorData, JSON_PRETTY_PRINT);
    
    // Registrar error en el log
    error_log("Error en get_low_stock_tools: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
}
exit();
?>