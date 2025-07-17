<?php
session_start();
header('Content-Type: application/json');

try {
    require_once 'database.php';
    
    // Get search parameters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    // Base query
    $sql = "SELECT a.*, u.usuario as nombre_usuario,
            CASE 
                WHEN a.tabla_afectada = 'herramientas_consumibles' THEN 'Herramientas Consumibles'
                WHEN a.tabla_afectada = 'herramientas_no_consumibles' THEN 'Herramientas No Consumibles'
                WHEN a.tabla_afectada = 'prestamos' THEN 'Préstamos'
                WHEN a.tabla_afectada = 'usuarios' THEN 'Usuarios'
                ELSE a.tabla_afectada
            END as tabla_afectada_formateada,
            DATE_FORMAT(a.fecha_accion, '%d/%m/%Y %H:%i') as fecha_formateada
            FROM auditorias a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE 1=1";
    
    $params = [];
    
    // Add search filter if provided
    if (!empty($search)) {
        $sql .= " AND (a.detalles LIKE ? OR u.usuario LIKE ? OR a.tabla_afectada LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Add action filter if provided
    if (!empty($action)) {
        $sql .= " AND a.accion = ?";
        $params[] = $action;
    }
    
    // Order by most recent first and limit to 5 records
    $sql .= " ORDER BY a.fecha_accion DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $audits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process details for each audit record
    foreach ($audits as &$audit) {
        if (isset($audit['detalles'])) {
            $details = json_decode($audit['detalles'], true);
            
            // Format user registration details
            if ($audit['accion'] === 'crear' && $audit['tabla_afectada'] === 'usuarios') {
                $audit['detalles_formateados'] = [
                    'descripcion' => 'Nuevo usuario registrado',
                    'usuario' => $details['usuario'] ?? 'N/A',
                    'rol' => $details['rol'] ?? 'N/A'
                ];
            } else {
                $audit['detalles_formateados'] = $details;
            }
        }
    }
    
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