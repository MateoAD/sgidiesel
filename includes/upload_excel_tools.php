<?php
// Iniciar control de salida para evitar HTML no deseado
ob_start();

require_once 'database.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

// Configurar el usuario actual para los triggers
if (isset($_SESSION['user_id'])) {
    $db->exec("SET @current_user_id = " . intval($_SESSION['user_id']));
} else {
    $db->exec("SET @current_user_id = 5"); // Usuario admin por defecto
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoHerramienta = $_POST['tipo_herramienta'] ?? '';

    if (empty($tipoHerramienta) || !in_array($tipoHerramienta, ['consumible', 'no_consumible'])) {
        $response['message'] = 'Tipo de herramienta no válido';
        limpiarSalidaYResponder($response);
    }

    if (isset($_FILES['archivoCSV']) && $_FILES['archivoCSV']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['archivoCSV']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== false) {
            // Saltar la primera fila (cabeceras)
            fgetcsv($handle, 1000, ';');

            $db->beginTransaction();

            try {
                $tabla = ($tipoHerramienta === 'no_consumible') ? 'herramientas_no_consumibles' : 'herramientas_consumibles';

                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    // Filtrar elementos vacíos
                    $data = array_map('trim', $data);
                    $data = array_filter($data, fn($value) => $value !== '');

                    if (count($data) < 2) {
                        continue; // Necesita al menos nombre y cantidad
                    }

                    $nombre = $data[0];
                    $cantidad = (int)$data[1];
                    $codigo_barras = isset($data[2]) && $data[2] !== '' ? $data[2] :
                        ($tipoHerramienta === 'no_consumible' ? 'HERR_' : 'CONS_') . strtoupper(substr(uniqid(), -8));

                    // Estado por defecto según el tipo
                    $estado = null;
                    if ($tipoHerramienta === 'consumible') {
                        $estado = $data[3] ?? 'lleno';
                    } else if ($tipoHerramienta === 'no_consumible') {
                        $estado = 'Activa';
                    }

                    // Insertar en tabla
                    $stmt = $db->prepare("INSERT INTO $tabla (nombre, cantidad, codigo_barras, estado) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nombre, $cantidad, $codigo_barras, $estado]);
                }

                $db->commit();
                $response['success'] = true;
                $response['message'] = 'Herramientas importadas correctamente';
            } catch (Exception $e) {
                $db->rollBack();
                $response['message'] = 'Error al importar herramientas: ' . $e->getMessage();
            }

            fclose($handle);
        } else {
            $response['message'] = 'No se pudo leer el archivo CSV';
        }
    } else {
        $response['message'] = 'No se subió ningún archivo o hubo un error en la subida';
    }
}

limpiarSalidaYResponder($response);

// ---------- FUNCIÓN AUXILIAR ----------
function limpiarSalidaYResponder($response)
{
    ob_end_clean(); // Borra cualquier salida previa (HTML, espacios, etc.)
    echo json_encode($response);
    exit;
}
