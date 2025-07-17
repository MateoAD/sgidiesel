<?php
require_once 'database.php';

$response = ['success' => false, 'message' => ''];

try {
    $tabla = ($_POST['tipoHerramienta'] === 'consumible') 
        ? 'herramientas_consumibles' 
        : 'herramientas_no_consumibles';
    
    // Verificar que la herramienta existe y tiene suficiente cantidad
    $stmt = $db->prepare("SELECT id, cantidad FROM {$tabla} WHERE id = ?");
    $stmt->execute([$_POST['herramientaId']]);
    $herramienta = $stmt->fetch();
    
    if (!$herramienta) {
        throw new Exception('La herramienta no existe');
    }

    if ($herramienta['cantidad'] < $_POST['cantidad']) {
        throw new Exception('No hay suficiente cantidad disponible');
    }
    
    // Verificar que el aprendiz existe y obtener su nombre
    $stmt = $db->prepare("SELECT nombre FROM aprendices WHERE nombre = ?");
    $stmt->execute([$_POST['nombreAprendiz']]);
    $aprendiz = $stmt->fetch();
    
    if (!$aprendiz) {
        throw new Exception('El aprendiz no existe en la base de datos');
    }
    
    $nombre_aprendiz = $aprendiz['nombre'];
    
    // Verificar que la ficha existe en la tabla aprendices
    $stmt = $db->prepare("SELECT ficha FROM aprendices WHERE nombre = ? AND ficha = ?");
    $stmt->execute([$nombre_aprendiz, $_POST['ficha']]);
    if (!$stmt->fetch()) {
        throw new Exception('La ficha no existe o no coincide con el aprendiz');
    }
    
    // Insertar la reserva con el nombre del aprendiz
    $stmt = $db->prepare("INSERT INTO reservas_herramientas 
                          (herramienta_id, tipo_herramienta, nombre_aprendiz, ficha, cantidad, fecha_reserva, estado)
                          VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");
    
    $stmt->execute([
        $_POST['herramientaId'],
        $_POST['tipoHerramienta'],
        $nombre_aprendiz,
        $_POST['ficha'],
        $_POST['cantidad'],
        $_POST['fechaReserva']
    ]);
    
    $response['success'] = true;
    $response['message'] = 'Reserva creada exitosamente';
} catch(PDOException $e) {
    $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
} catch(Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);