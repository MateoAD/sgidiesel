<?php
session_start();
header('Content-Type: application/json');

try {
    require_once 'database.php';

    $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
    $action = isset($_GET['action']) ? trim((string) $_GET['action']) : '';

    $sql = "SELECT a.*,
                   u.usuario AS nombre_usuario,
                   u.rol AS rol_usuario,
                   CASE
                       WHEN a.tabla_afectada = 'herramientas_consumibles' THEN 'Herramientas Consumibles'
                       WHEN a.tabla_afectada = 'herramientas_no_consumibles' THEN 'Herramientas No Consumibles'
                       WHEN a.tabla_afectada = 'prestamos' THEN 'Prestamos'
                       WHEN a.tabla_afectada = 'usuarios' THEN 'Usuarios'
                       ELSE a.tabla_afectada
                   END AS tabla_afectada_formateada,
                   DATE_FORMAT(a.fecha_accion, '%d/%m/%Y %H:%i') AS fecha_formateada
            FROM auditorias a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE 1=1";

    $params = [];

    if ($search !== '') {
        $sql .= " AND (a.detalles LIKE ? OR u.usuario LIKE ? OR a.tabla_afectada LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    if ($action !== '') {
        $sql .= " AND a.accion = ?";
        $params[] = $action;
    }

    $sql .= " ORDER BY a.fecha_accion DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $audits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($audits as &$audit) {
        $details = [];

        if (isset($audit['detalles']) && $audit['detalles'] !== null && $audit['detalles'] !== '') {
            $decoded = json_decode($audit['detalles'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $details = $decoded;
            }
        }

        $descripcion = isset($details['descripcion']) ? (string) $details['descripcion'] : '';
        $isLoginEvent = $audit['accion'] === 'crear'
            && $audit['tabla_afectada'] === 'usuarios'
            && stripos($descripcion, 'inicio de sesi') !== false;

        if ($isLoginEvent) {
            $audit['detalles_formateados'] = [
                'descripcion' => 'Inicio de sesion exitoso',
                'usuario' => $details['usuario'] ?? ($audit['nombre_usuario'] ?? 'N/A'),
                'rol' => $details['rol'] ?? ($audit['rol_usuario'] ?? null),
                'es_inicio_sesion' => true
            ];
        } elseif ($audit['accion'] === 'crear' && $audit['tabla_afectada'] === 'usuarios') {
            $audit['detalles_formateados'] = [
                'descripcion' => 'Nuevo usuario registrado',
                'usuario' => $details['usuario'] ?? ($audit['nombre_usuario'] ?? 'N/A'),
                'rol' => $details['rol'] ?? ($audit['rol_usuario'] ?? null)
            ];
        } else {
            $audit['detalles_formateados'] = $details;
        }
    }
    unset($audit);

    echo json_encode([
        'success' => true,
        'audits' => $audits
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
