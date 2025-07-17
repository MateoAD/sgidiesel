<?php
require_once 'database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validaciones básicas
    if (empty($data['username']) || empty($data['password']) || empty($data['phone']) || empty($data['ficha'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $username = trim($data['username']);
    $password = $data['password'];
    $phone = trim($data['phone']);
    $ficha = trim($data['ficha']);
    $role = 'aprendiz';

    // Verificar si el usuario ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception('El nombre de usuario ya está en uso');
    }

    // Validar formato del teléfono
    if (!preg_match('/^\+[0-9]{11,15}$/', $phone)) {
        throw new Exception('Formato de teléfono inválido');
    }

    // Verificar que la ficha exista en la base de datos
    $stmt = $db->prepare("SELECT COUNT(*) FROM aprendices WHERE ficha = ?");
    $stmt->execute([$ficha]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception('La ficha no coincide con nuestros registros');
    }

    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $stmt = $db->prepare("INSERT INTO usuarios (usuario, contraseña, telefono, rol, fecha_creacion) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$username, $passwordHash, $phone, $role]);
    $userId = $db->lastInsertId();

    // Iniciar sesión automáticamente
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['rol'] = 'aprendiz';

    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente',
        'redirect' => 'user_dashboard.php'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor. Por favor intente más tarde.'
    ]);
}