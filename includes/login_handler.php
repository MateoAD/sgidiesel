<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username']) || !isset($data['password'])) {
        throw new Exception('Usuario y contrasena son requeridos');
    }

    $username = trim($data['username']);
    $password = $data['password'];
    $remember = isset($data['remember']) ? (bool) $data['remember'] : false;

    $query = "SELECT id, usuario, `contraseña` AS contrasena, rol, fecha_creacion, activo
              FROM usuarios
              WHERE usuario = ?
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && (int) $user['activo'] === 0) {
        throw new Exception('Este usuario esta inactivo. Contacte al administrador.');
    }

    if (!$user || !password_verify($password, $user['contrasena'])) {
        throw new Exception('Usuario o contrasena incorrectos');
    }

    session_regenerate_id(true);
    $_SESSION = [];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['usuario'];
    $_SESSION['rol'] = $user['rol'];
    $_SESSION['authenticated'] = true;
    $_SESSION['last_activity'] = time();

    // Guardar id_aprendiz cuando existe para flujo de usuario aprendiz.
    $stmtAprendiz = $db->prepare("SELECT id_aprendiz FROM usuarios WHERE id = ?");
    $stmtAprendiz->execute([$user['id']]);
    $aprendiz = $stmtAprendiz->fetch(PDO::FETCH_ASSOC);
    if ($aprendiz && !empty($aprendiz['id_aprendiz'])) {
        $_SESSION['id_aprendiz'] = $aprendiz['id_aprendiz'];
    }

    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30));

        $updateQuery = "UPDATE usuarios
                        SET token_recuperacion = ?, expiracion_token = ?
                        WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$token, $expiry, $user['id']]);

        setcookie('remember_token', $token, time() + (60 * 60 * 24 * 30), '/', '', true, true);
    }

    $redirect = 'dashboard.php';
    if ($user['rol'] === 'aprendiz') {
        $redirect = 'guest_inventory.php';
    } elseif ($user['rol'] === 'aprendiz almacenista') {
        $redirect = 'user_dashboard.php';
    }

    // Registrar auditoria de login sin bloquear el acceso si falla.
    try {
        $auditQuery = "INSERT INTO auditorias (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_usuario)
                       VALUES (?, 'crear', 'usuarios', ?, ?, ?)";
        $auditStmt = $db->prepare($auditQuery);
        $auditStmt->execute([
            $user['id'],
            $user['id'],
            json_encode([
                'descripcion' => 'Inicio de sesion exitoso',
                'usuario' => $user['usuario'],
                'rol' => $user['rol']
            ]),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (PDOException $auditError) {
        error_log('Error registrando auditoria de login: ' . $auditError->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Inicio de sesion exitoso',
        'redirect' => $redirect,
        'rol' => $user['rol']
    ]);
} catch (Exception $e) {
    error_log('Error de login: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log('Error en base de datos: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor. Por favor intente mas tarde.'
    ]);
}
?>
