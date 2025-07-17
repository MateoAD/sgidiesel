<?php
// recovery_handler.php
require_once 'database.php';

header('Content-Type: application/json');

// Obtener los datos del JSON enviado
$data = json_decode(file_get_contents('php://input'), true);

// Validar que los datos necesarios están presentes
if (!isset($data['username']) || !isset($data['phone'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario y número de WhatsApp son requeridos'
    ]);
    exit;
}

$username = trim($data['username']);
$phone = trim($data['phone']);

try {
    // Verificar si el usuario existe y el teléfono coincide
    $query = "SELECT id FROM usuarios WHERE usuario = ? AND telefono = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$username, $phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado o número de WhatsApp no coincide'
        ]);
        exit;
    }

    // Generar token y fecha de expiración
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Actualizar el token en la base de datos
    $updateQuery = "UPDATE usuarios SET token_recuperacion = ?, expiracion_token = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        $token,
        $expiry,
        $user['id']
    ]);

    // Generar mensaje de WhatsApp con el token
    $message = "Tu código de recuperación es: $token\n\n";
    $message .= "Ingresa este código en la página de recuperación para restablecer tu contraseña.\n";
    $message .= "Este código expirará en 1 hora.\n\n";
    $message .= "Haz clic aquí para ir directamente al formulario: ";
    $message .= "http://$_SERVER[HTTP_HOST]/SGSDIESEL/index.php?showTokenModal=true";
   
   // Codificar el mensaje para URL
   $encodedMessage = urlencode($message);
    
    // Codificar el mensaje para URL
    $encodedMessage = urlencode($message);
    
    // Generar enlace directo a WhatsApp
    $whatsappLink = "https://wa.me/$phone?text=$encodedMessage";
    
    echo json_encode([
        'success' => true,
        'message' => 'Se ha generado un enlace de recuperación',
        'whatsapp_link' => $whatsappLink,
        'token' => $token  
    ]);

} catch (PDOException $e) {
    // Registrar el error y devolver mensaje genérico
    error_log("Error en recovery_handler: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor. Por favor intente más tarde.'
    ]);
}
?>