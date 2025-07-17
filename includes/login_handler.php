<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');



try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar que los datos necesarios están presentes
    if (!isset($data['username']) || !isset($data['password'])) {
        throw new Exception('Usuario y contraseña son requeridos');
    }

    $username = trim($data['username']);
    $password = $data['password'];
    $remember = isset($data['remember']) ? (bool)$data['remember'] : false;

    $query = "SELECT id, usuario, contraseña, rol, fecha_creacion, activo 
    FROM usuarios 
    WHERE usuario = ? 
    LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario está inactivo
if ($user && $user['activo'] == 0) {
throw new Exception('Este usuario está inactivo. Contacte al administrador.');
}

    // Verificar si el usuario existe y la contraseña es correcta
    if (!$user || !password_verify($password, $user['contraseña'])) {
        throw new Exception('Usuario o contraseña incorrectos');
    }

    // Configurar la sesión
    session_regenerate_id(true);
  $_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['usuario'];
$_SESSION['rol'] = $user['rol'];

// Obtener el id_aprendiz asociado al usuario
$stmt = $db->prepare("SELECT id_aprendiz FROM usuarios WHERE id = ?");
$stmt->execute([$user['id']]);
$aprendiz = $stmt->fetch();
if ($aprendiz && $aprendiz['id_aprendiz']) {
    $_SESSION['id_aprendiz'] = $aprendiz['id_aprendiz'];
}
    $_SESSION['authenticated'] = true;
    $_SESSION['last_activity'] = time();

    // Limpiar sesiones anteriores
    session_regenerate_id(true);
    $_SESSION = array();

    // Configurar la sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['usuario'];
    $_SESSION['rol'] = $user['rol'];
    $_SESSION['last_activity'] = time();

    // Manejar "Recordar sesión"
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 días

        $updateQuery = "UPDATE usuarios SET 
                       token_recuperacion = ?, 
                       expiracion_token = ?
                       WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([
            $token,
            $expiry,
            $user['id']
        ]);

        setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/', '', true, true);
    }

    // Determinar la redirección según el rol
   $redirect = 'dashboard.php'; // valor por defecto para administrador
if ($user['rol'] === 'aprendiz') {
    $redirect = 'guest_inventory.php';
} elseif ($user['rol'] === 'aprendiz almacenista') {
    $redirect = 'user_dashboard.php';
}

    echo json_encode([
        'success' => true,
        'message' => 'Inicio de sesión exitoso',
        'redirect' => $redirect,
        'rol' => $user['rol']
    ]);

} catch (Exception $e) {
    error_log("Error de login: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Error en base de datos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor. Por favor intente más tarde.'
    ]);
}

// Registrar el inicio de sesión en auditoría
$auditQuery = "INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_usuario) 
VALUES (?, 'crear', 'usuarios', ?, ?, ?)";
$auditStmt = $db->prepare($auditQuery);
$auditStmt->execute([
$user['id'],
$user['id'],
json_encode([
'descripcion' => 'Inicio de sesión exitoso',
'usuario' => $user['usuario']
]),
$_SERVER['REMOTE_ADDR']
]);
?>