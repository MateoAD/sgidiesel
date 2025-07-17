<?php
// check_session.php
session_start();

// Verificar si hay una sesión activa
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// Si hay cookie de recordar sesión pero no hay sesión activa
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once 'database.php';
    
    $token = $_COOKIE['remember_token'];
    
    try {
        $query = "SELECT id, usuario FROM usuarios WHERE token_recuperacion = ? AND expiracion_token > NOW() LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['usuario'];
        }
    } catch (PDOException $e) {
        error_log("Error al verificar token de sesión: " . $e->getMessage());
    }
}

// Si después de todo sigue sin haber sesión
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}