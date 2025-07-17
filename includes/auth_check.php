<?php
if (!ob_get_level()) ob_start();
session_start();

// Debug session information (puedes quitar esto en producción)
error_log("Session check - User ID: " . ($_SESSION['user_id'] ?? 'not set') . 
          ", Role: " . ($_SESSION['rol'] ?? 'not set'));

// Validar sesión
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: index.php');
    exit;
}

// Obtener página actual
$current_page = basename($_SERVER['PHP_SELF']);

try {
    require_once 'database.php';

    // Consultar usuario actual
    $query = "SELECT id, usuario, rol FROM usuarios WHERE id = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // El usuario ya no existe
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        header('Location: index.php');
        exit;
    }

    // Prevenir secuestro de sesión
    if ($user['rol'] !== $_SESSION['rol']) {
        $_SESSION['rol'] = $user['rol'];
    }

    // Control de acceso basado en rol
    if (isset($_SESSION['rol'])) {
        $currentPage = basename($_SERVER['PHP_SELF']);
        
        // Aprendiz - solo puede acceder a guest_inventory y perfil
        if ($_SESSION['rol'] === 'aprendiz') {
            $allowedPages = ['guest_inventory.php', 'perfil.php'];
            if (!in_array($currentPage, $allowedPages)) {
                header('Location: guest_inventory.php');
                exit();
            }
        }
        
        // Aprendiz almacenista - puede acceder a user_dashboard, user_inventory, user_loans y perfil
        elseif ($_SESSION['rol'] === 'almacenista') {
            $allowedPages = ['user_dashboard.php', 'user_inventory.php', 'user_loans.php', 'perfil.php'];
            if (!in_array($currentPage, $allowedPages)) {
                header('Location: user_dashboard.php');
                exit();
            }
        }
        
        // Admin - acceso completo a todos los módulos
        elseif ($_SESSION['rol'] === 'administrador') {
            // No se aplican restricciones
        }
        
        // Rol no reconocido - redirigir a página segura
        else {
            header('Location: index.php');
            exit();
        }
    }

    // Expiración de sesión por inactividad (30 minutos)
    $timeout = 1800; // 30 minutos en segundos
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_destroy();
        header('Location: index.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();

    // Variables globales de JS
    echo "<script>
        const userRole = '" . $_SESSION['rol'] . "';
        const userId = '" . $_SESSION['user_id'] . "';
    </script>";

} catch (PDOException $e) {
    error_log("Auth Check Error: " . $e->getMessage());
    header('Location: index.php?error=system');
    exit;
}
?>
