<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

try {
   
    
    // Get user ID from session or use default
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; // Default to admin user
    
    // Set user ID for database triggers
    $db->exec("SET @current_user_id = " . $userId);
    
    // Disable audit triggers to prevent multiple records
    $db->exec("SET @disable_audit_trigger = 1");

    // Get form data
    $tipo = $_POST['tipo'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $cantidad = $_POST['cantidad'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? 'Taller';
    $estado = $_POST['estado'] ?? 'lleno';

    // Validate required fields
    if (!$tipo || !$nombre) {
        throw new Exception("Tipo y nombre son campos requeridos");
    }

    // Determine which table to use
    $tabla = $tipo === 'no_consumible' ? 'herramientas_no_consumibles' : 'herramientas_consumibles';
    
    // Generate unique code
    $prefijo = $tipo === 'no_consumible' ? 'HERR_' : 'CONS_';
    $codigo_barras = $prefijo . strtoupper(substr(uniqid(), -8));
    
    // Begin transaction
    $db->beginTransaction();
    
    // Procesar la foto si se ha subido
    $nombreFoto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Crear directorio si no existe
        $uploadDir = '../uploads/herramientas/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generar nombre único para la foto
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreFoto = uniqid('herr_') . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreFoto;
        
        // Mover el archivo subido
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            throw new Exception("Error al subir la imagen");
        }
    }
    
    try {
        // Prepare the SQL query - INCLUDE usuario_id, codigo_barras and foto in both tables
        $campos = ['nombre', 'cantidad', 'descripcion', 'ubicacion', 'usuario_id', 'codigo_barras'];
        $valores = [':nombre', ':cantidad', ':descripcion', ':ubicacion', ':usuario_id', ':codigo_barras'];
        
        // Add foto field if a photo was uploaded
        if ($nombreFoto) {
            $campos[] = 'foto';
            $valores[] = ':foto';
        }
        
        // Add estado field for consumibles
        if ($tipo === 'consumible') {
            $campos[] = 'estado';
            $valores[] = ':estado';
        }
        
        $sql = "INSERT INTO $tabla (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")";
        $stmt = $db->prepare($sql);
        
        // Bind parameters
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':cantidad', $cantidad);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':ubicacion', $ubicacion);
        $stmt->bindValue(':usuario_id', $userId); // Add the user ID
        $stmt->bindValue(':codigo_barras', $codigo_barras); // Add the unique code
        
        // Bind foto if exists
        if ($nombreFoto) {
            $stmt->bindValue(':foto', $nombreFoto);
        }
        
        if ($tipo === 'consumible') {
            $stmt->bindValue(':estado', $estado);
        }
        
        // Execute the query
        $stmt->execute();
        $id = $db->lastInsertId();
        
        // Create audit record
        $detalles = [
            'descripcion' => "Nueva herramienta " . ($tipo === 'no_consumible' ? 'no consumible' : 'consumible') . " creada",
            'nombre' => $nombre,
            'cantidad' => $cantidad
        ];
        
        if ($tipo === 'consumible') {
            $detalles['estado'] = $estado;
        }
        
        $detallesJson = json_encode($detalles);
        
        $auditStmt = $db->prepare("INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_usuario) 
                                  VALUES (?, 'crear', ?, ?, ?, ?)");
        $auditStmt->execute([
            $userId, 
            $tabla, 
            $id, 
            $detallesJson,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        // Re-enable audit trigger
        $db->exec("SET @disable_audit_trigger = NULL");
        
        $db->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Herramienta agregada correctamente',
            'id' => $id,
            'tipo' => $tipo,
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'codigo_barras' => $codigo_barras,
            'foto' => $nombreFoto
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    // Re-enable audit trigger
    $db->exec("SET @disable_audit_trigger = NULL");
    
    // Log error
    error_log("Error al agregar herramienta: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Error al agregar la herramienta: ' . $e->getMessage()
    ]);
}
?>