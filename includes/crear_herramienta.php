<?php
session_start();
header('Content-Type: application/json');
require_once 'database.php';
try {

    
    // Get user ID from session or use default admin user
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; // Default to admin user
    
    // Set user ID for database triggers
    $db->exec("SET @current_user_id = " . $userId);
    
    // Disable audit trigger temporarily to prevent duplicate entries
    $db->exec("SET @disable_audit_trigger = 1");
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['nombre']) || !isset($data['cantidad']) || !isset($data['tipo'])) {
        throw new Exception("Nombre, cantidad y tipo son requeridos");
    }
    
    $nombre = $data['nombre'];
    $cantidad = $data['cantidad'];
    $tipo = $data['tipo'];
    $estado = $data['estado'] ?? '';
    $ubicacion = $data['ubicacion'] ?? null;
    
    // Generate barcode
    $codigo_barras = $tipo === 'no_consumible' ? 
                    'HERR_' . uniqid() : 
                    strval(mt_rand(1000000000, 9999999999));
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Insert based on tool type
        if ($tipo === 'no_consumible') {
            // Insert non-consumable tool
            $stmt = $db->prepare("INSERT INTO herramientas_no_consumibles 
                (nombre, cantidad, estado, codigo_barras, descripcion, ubicacion, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $nombre,
                $cantidad,
                $estado,
                $codigo_barras,
                $data['descripcion'] ?? null,
                $ubicacion,
                $userId
            ]);
            
            $herramienta_id = $db->lastInsertId();
            $tabla = 'herramientas_no_consumibles';
        } else {
            // Insert consumable tool
            $stmt = $db->prepare("INSERT INTO herramientas_consumibles 
                (nombre, cantidad, estado, codigo_barras, descripcion, ubicacion, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $nombre,
                $cantidad,
                $estado ?: 'lleno',
                $codigo_barras,
                $data['descripcion'] ?? null,
                $ubicacion,
                $userId
            ]);
            
            $herramienta_id = $db->lastInsertId();
            $tabla = 'herramientas_consumibles';
        }
        
        // Create audit record manually
        $detalles = json_encode([
            'descripcion' => "Nueva herramienta {$tipo} creada",
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'estado' => $estado
        ]);
        
        $audit_stmt = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, fecha_accion, ip_usuario) 
                                  VALUES (?, 'crear', ?, ?, ?, NOW(), ?)");
        $audit_stmt->execute([
            $userId, 
            $tabla, 
            $herramienta_id, 
            $detalles,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        // Re-enable audit trigger
        $db->exec("SET @disable_audit_trigger = NULL");
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Herramienta creada correctamente', 'id' => $herramienta_id]);
    } catch (Exception $e) {
        $db->rollBack();
        // Re-enable audit trigger even if there's an error
        $db->exec("SET @disable_audit_trigger = NULL");
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>