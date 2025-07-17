<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

try {
   
    
    // Obtener ID de usuario
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5;
    
    // Establecer variables de sesión para triggers
    $db->exec("SET @current_user_id = " . $userId);
    $db->exec("SET @disable_audit_trigger = 1"); // Deshabilitar triggers de auditoría
    
    // Validar parámetros
    $id = $_POST['id'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    
    if (!$id || !$tipo) {
        throw new Exception("ID y tipo de herramienta son requeridos");
    }

    $tabla = $tipo === 'no_consumible' ? 'herramientas_no_consumibles' : 'herramientas_consumibles';

    // Obtener datos actuales
    $stmt = $db->prepare("SELECT * FROM $tabla WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $herramientaAnterior = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$herramientaAnterior) {
        throw new Exception("Herramienta no encontrada");
    }

    // Verificar si ya tiene código de barras, si no, generar uno
    if (empty($herramientaAnterior['codigo_barras'])) {
        $prefijo = $tipo === 'no_consumible' ? 'HERR_' : 'CONS_';
        $herramientaAnterior['codigo_barras'] = $prefijo . strtoupper(substr(uniqid(), -8));
    }

    // Iniciar transacción
    $db->beginTransaction();
    
    try {
        // Preparar campos
        $campos = [
            'nombre' => $_POST['nombre'] ?? $herramientaAnterior['nombre'],
            'cantidad' => $_POST['cantidad'] ?? $herramientaAnterior['cantidad'],
            'descripcion' => $_POST['descripcion'] ?? $herramientaAnterior['descripcion'],
            'ubicacion' => $_POST['ubicacion'] ?? $herramientaAnterior['ubicacion'],
            'codigo_barras' => $herramientaAnterior['codigo_barras'] // Mantener el código existente o usar el nuevo generado
        ];

        if ($tipo === 'consumible') {
            $campos['estado'] = $_POST['estado'] ?? $herramientaAnterior['estado'];
        }
        
        // Procesar la foto si se ha subido una nueva
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
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
                // Eliminar foto anterior si existe
                if (!empty($herramientaAnterior['foto']) && file_exists($uploadDir . $herramientaAnterior['foto'])) {
                    unlink($uploadDir . $herramientaAnterior['foto']);
                }
                
                $campos['foto'] = $nombreFoto;
            } else {
                throw new Exception("Error al subir la imagen");
            }
        }

        // Identificar cambios
        $sets = [];
        $changedFields = [];
        
        foreach ($campos as $key => $value) {
            // Asegurarse de que el campo 'foto' siempre se incluya si se ha subido una nueva imagen
            if ($key === 'foto' && isset($campos['foto'])) {
                $sets[] = "$key = :$key";
                $changedFields[$key] = [
                    'anterior' => $herramientaAnterior[$key] ?? null,
                    'nuevo' => $value
                ];
                continue;
            }
            
            if (!isset($herramientaAnterior[$key])) continue;
            
            $original = is_numeric($herramientaAnterior[$key]) 
                ? floatval($herramientaAnterior[$key]) 
                : $herramientaAnterior[$key];
                
            $nuevo = is_numeric($value) ? floatval($value) : $value;
            
            if ($original != $nuevo) {
                $sets[] = "$key = :$key";
                $changedFields[$key] = [
                    'anterior' => $herramientaAnterior[$key],
                    'nuevo' => $value
                ];
            }
        }
        
        if (empty($sets)) {
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'No se detectaron cambios']);
            exit;
        }

        // Actualizar herramienta (el trigger registrará la auditoría)
        $query = "UPDATE $tabla SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $db->prepare($query);
        
        // Asegurarse de que todos los campos en $sets tengan sus valores vinculados
        foreach ($sets as $set) {
            $key = explode(' = ', $set)[0];
            $stmt->bindValue(":$key", $campos[$key]);
        }
        
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $db->commit();
        
        // Rehabilitar triggers después de la actualización
        $db->exec("SET @disable_audit_trigger = NULL");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Herramienta actualizada correctamente'
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        // Asegurarse de rehabilitar los triggers en caso de error
        $db->exec("SET @disable_audit_trigger = NULL");
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>