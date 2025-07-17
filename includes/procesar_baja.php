<?php
require_once 'database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['herramienta_id'], $data['tipo'], $data['cantidad'], $data['motivo'], 
                  $data['lugar_salida'], $data['lugar_entrada'], $data['responsable'])) {
            throw new Exception('Datos incompletos');
        }

        $tabla = $data['tipo'] === 'consumible' ? 'herramientas_consumibles' : 'herramientas_no_consumibles';

        // Verificar stock disponible
        $stmt = $db->prepare("SELECT cantidad FROM $tabla WHERE id = ?");
        $stmt->execute([$data['herramienta_id']]);
        $herramienta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$herramienta || $herramienta['cantidad'] < $data['cantidad']) {
            throw new Exception('Stock insuficiente');
        }

        // Iniciar transacción
        $db->beginTransaction();

        // Actualizar cantidad
        $stmt = $db->prepare("UPDATE $tabla SET cantidad = cantidad - ? WHERE id = ?");
        $stmt->execute([$data['cantidad'], $data['herramienta_id']]);

        // Registrar la baja
        $stmt = $db->prepare("INSERT INTO bajas_herramientas (herramienta_id, tipo_herramienta, cantidad, 
                             motivo, lugar_salida, lugar_entrada, responsable, fecha) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $data['herramienta_id'],
            $data['tipo'],
            $data['cantidad'],
            $data['motivo'],
            $data['lugar_salida'],
            $data['lugar_entrada'],
            $data['responsable']
        ]);

        $db->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollBack();
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}