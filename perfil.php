<?php
require_once 'includes/auth_check.php';

$userId = $_SESSION['user_id'];
$userData = null;
$updateMessage = '';
$updateError = '';

// Verificar si hay mensajes de la actualización
if (isset($_SESSION['profile_update_message'])) {
    $updateMessage = $_SESSION['profile_update_message'];
    unset($_SESSION['profile_update_message']);
}

if (isset($_SESSION['profile_update_error'])) {
    $updateError = $_SESSION['profile_update_error'];
    unset($_SESSION['profile_update_error']);
}

try {
    // Obtener datos del usuario
    $stmt = $db->prepare("SELECT usuario, telefono, rol, fecha_creacion FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Taller Diesel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('./img/fondo_panel.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <header class="bg-[#4A655D] text-white shadow-lg sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">

            <!-- Logo y título -->
            <div class="flex items-center space-x-4">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto">

                <div class="border-l-2 border-white pl-4">
                    <h1 class="text-2xl font-bold">Mi Perfil</h1>
                    <p class="text-sm text-gray-200">Taller Diesel - SENA</p>
                </div>
            </div>

            <!-- Usuario y salir -->
            <div class="flex items-center space-x-4">
                <!-- Botón de volver -->
                <a href="<?php echo ($_SESSION['rol'] === 'administrador') ? 'dashboard.php' : 'user_dashboard.php'; ?>"
                    class="bg-[#05976A] hover:bg-[#4A655D] px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Panel
                </a>

                <!-- Botón de cerrar sesión -->
                <a href="logout.php"
                    class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Salir
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Mensajes de actualización -->
        <?php if (!empty($updateMessage)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?php echo $updateMessage; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($updateError)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?php echo $updateError; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Información del perfil -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle text-[#05976A] mr-3"></i>
                    Información del Perfil
                </h2>

                

                <div class="space-y-4">
                    <div class="flex items-center border-b border-gray-200 pb-3">
                        <span class="font-semibold text-gray-700 w-1/3">Usuario:</span>
                        <span class="text-gray-800"><?php echo htmlspecialchars($userData['usuario'] ?? ''); ?></span>
                    </div>

                    <div class="flex items-center border-b border-gray-200 pb-3">
                        <span class="font-semibold text-gray-700 w-1/3">Teléfono:</span>
                        <span class="text-gray-800"><?php echo htmlspecialchars($userData['telefono'] ?? ''); ?></span>
                    </div>

                    <div class="flex items-center border-b border-gray-200 pb-3">
                        <span class="font-semibold text-gray-700 w-1/3">Rol:</span>
                        <span class="text-gray-800"><?php echo htmlspecialchars($userData['rol'] ?? ''); ?></span>
                    </div>

                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 w-1/3">Fecha de registro:</span>
                        <span
                            class="text-gray-800"><?php echo htmlspecialchars($userData['fecha_creacion'] ?? ''); ?></span>
                    </div>
                </div>
            </div>

            <!-- Formulario de actualización -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-edit text-[#05976A] mr-3"></i>
                    Actualizar Perfil
                </h2>

                <form id="updateProfileForm" action="includes/update_profile.php" method="POST" class="space-y-4">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Nuevo nombre de
                            usuario</label>
                        <input type="text" id="username" name="username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                            value="<?php echo htmlspecialchars($userData['usuario'] ?? ''); ?>">
                        <p class="text-sm text-gray-500 mt-1">Deja en blanco si no deseas cambiar tu nombre de usuario.
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                            Teléfono
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="phone" name="phone" type="tel"
                            value="<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>"
                            placeholder="Número telefónico">
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 font-medium mb-2">Nueva contraseña</label>
                        <input type="password" id="new_password" name="new_password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#05976A]">
                        <p class="text-sm text-gray-500 mt-1">Deja en blanco si no deseas cambiar tu contraseña.</p>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirmar nueva
                            contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#05976A]">
                    </div>

                    <div class="mb-4">
                        <label for="current_password" class="block text-gray-700 font-medium mb-2">Contraseña actual
                            <span class="text-red-500">*</span></label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#05976A]">
                        <p class="text-sm text-gray-500 mt-1">Requerida para confirmar los cambios.</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-[#05976A] hover:bg-[#4A655D] text-white px-6 py-2 rounded-lg shadow-md transition duration-200">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('updateProfileForm');

            form.addEventListener('submit', function (e) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                // Verificar que las contraseñas coincidan si se está cambiando la contraseña
                if (newPassword && newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas nuevas no coinciden');
                    return false;
                }

                // Verificar que al menos un campo esté siendo actualizado
                const username = document.getElementById('username').value;
                const currentUsername = '<?php echo htmlspecialchars($userData['usuario'] ?? ''); ?>';
                const phone = document.getElementById('phone').value;
                const currentPhone = '<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>';

                if (!newPassword && username === currentUsername && phone === currentPhone) {
                    e.preventDefault();
                    alert('No has realizado ningún cambio');
                    return false;
                }
            });
        });
    </script>
    <footer class="bg-[#2D3A36] text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p>© <?= date('Y') ?> SENA - Sistema de Gestión de Inventarios. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>
