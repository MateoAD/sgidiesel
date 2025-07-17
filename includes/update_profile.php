<?php
session_start();
require_once 'database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar si se envió el formulario por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../perfil.php');
    exit;
}

try {
    // Obtener datos del formulario
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $newPhone = trim($_POST['phone'] ?? ''); 
    // Verificar que la contraseña actual es correcta
    $stmt = $db->prepare("SELECT usuario, contraseña, telefono FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['contraseña'])) {
        $_SESSION['profile_update_error'] = 'La contraseña actual es incorrecta';
        header('Location: ../perfil.php');
        exit;
    }
    
    // Verificar si hay cambios para realizar
    $updates = [];
    $params = [];
    
    // Actualizar nombre de usuario si se proporcionó uno nuevo
    if (!empty($newUsername) && $newUsername !== $user['usuario']) {
        // Verificar si el nuevo nombre de usuario ya existe
        $checkStmt = $db->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id != ?");
        $checkStmt->execute([$newUsername, $userId]);
        if ($checkStmt->fetch()) {
            $_SESSION['profile_update_error'] = 'El nombre de usuario ya está en uso';
            header('Location: ../perfil.php');
            exit;
        }
        
        $updates[] = "usuario = ?";
        $params[] = $newUsername;
    }
    
    // Actualizar teléfono si se proporcionó uno nuevo
    if (!empty($newPhone) && $newPhone !== ($user['telefono'] ?? '')) {
        // Validación más flexible del teléfono
        if (!preg_match('/^[0-9\s\+\(\)\-]{8,20}$/', $newPhone)) {
            $_SESSION['profile_update_error'] = 'El teléfono debe contener entre 8 y 20 dígitos';
            header('Location: ../perfil.php');
            exit;
        }
        
        $updates[] = "telefono = ?";
        $params[] = $newPhone;
        
        // Registrar cambio para auditoría
        $auditData['detalles']['telefono_anterior'] = $user['telefono'] ?? '';
        $auditData['detalles']['telefono_nuevo'] = $newPhone;
    }
    // Actualizar contraseña si se proporcionó una nueva
    if (!empty($newPassword)) {
        // Verificar que las contraseñas coincidan
        if ($newPassword !== $confirmPassword) {
            $_SESSION['profile_update_error'] = 'Las contraseñas nuevas no coinciden';
            header('Location: ../perfil.php');
            exit;
        }
        
        $updates[] = "contraseña = ?";
        $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
    }
    
    // Si no hay actualizaciones, redirigir
    if (empty($updates)) {
        $_SESSION['profile_update_error'] = 'No se realizaron cambios';
        header('Location: ../perfil.php');
        exit;
    }
    
    // Construir y ejecutar la consulta de actualización
    $query = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $userId;
    
    $updateStmt = $db->prepare($query);
    $updateStmt->execute($params);
    
    // Actualizar la sesión si se cambió el nombre de usuario
    if (!empty($newUsername) && $newUsername !== $user['usuario']) {
        $_SESSION['username'] = $newUsername;
    }
    
    // Registrar la actividad en el log de auditoría
    $auditData = [
        'descripcion' => 'Actualización de perfil',
        'detalles' => []
    ];
    
    if (!empty($newUsername) && $newUsername !== $user['usuario']) {
        $auditData['detalles']['usuario_anterior'] = $user['usuario'];
        $auditData['detalles']['usuario_nuevo'] = $newUsername;
    }
    
    if (!empty($newPassword)) {
        $auditData['detalles']['cambio_contraseña'] = true;
    }
    
    // Registrar en la auditoría si existe la función
    if (file_exists('register_audit.php')) {
        // Crear los datos para la auditoría
        $auditPostData = [
            'accion' => 'modificar',
            'tabla_afectada' => 'usuarios',
            'registro_id' => $userId,
            'detalles' => json_encode($auditData)
        ];
        
        // Inicializar cURL
        $ch = curl_init();
        
        // Configurar la solicitud cURL
        curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/SGSDIESEL/includes/register_audit.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $auditPostData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Ejecutar la solicitud
        $response = curl_exec($ch);
        
        // Cerrar la sesión cURL
        curl_close($ch);
    }
    
    $_SESSION['profile_update_message'] = 'Perfil actualizado correctamente';
} catch (PDOException $e) {
    
}

header('Location: ../perfil.php');
exit;