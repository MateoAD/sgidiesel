<?php
require_once 'includes/auth_check.php';

if ($_SESSION['rol'] === 'administrador') {
    header('Location: dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userData = null;
$prestamosCount = 0;

try {
    // Obtener datos del usuario
    $stmt = $db->prepare("SELECT usuario, telefono FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta directa usando el nuevo campo usuario_id
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM prestamos WHERE usuario_id = ?");
    $stmt->execute([$userId]);
    $prestamosCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Taller Diesel</title>
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

        .icon-container {
            transition: all 0.3s ease;
        }

        .card-hover:hover .icon-container {
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <header class="bg-[#4A655D] text-white shadow-lg sticky top-0 z-10">
        <div class="container mx-auto px-3 sm:px-4 md:px-6 py-3 md:py-4 flex flex-col gap-3 md:flex-row md:justify-between md:items-center">

            <!-- Logo y título -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-12 sm:h-14 md:h-16 w-auto">

                <div class="border-l-2 border-white pl-3 sm:pl-4 min-w-0">
                    <h1 class="text-base sm:text-xl md:text-2xl font-bold leading-tight">Panel de Almacenista</h1>
                    <p class="text-xs sm:text-sm text-gray-200">Taller Diesel - SENA</p>
                </div>
            </div>


            <!-- Usuario y acciones -->
            <div class="flex items-center w-full md:w-auto gap-2 sm:gap-3 md:gap-6 justify-between md:justify-end">

                <!-- Perfil de usuario con nombre -->
                <div class="flex items-center bg-[#3A9171] px-3 sm:px-4 py-2 rounded-full shadow-md min-w-0 flex-1 md:flex-none">
                    <i class="fas fa-user-circle text-xl sm:text-2xl mr-2 text-white"></i>
                    <span class="font-medium text-white whitespace-nowrap truncate max-w-[110px] sm:max-w-[180px] md:max-w-none">
                        <?php
                        require_once 'includes/database.php';
                        $userId = $_SESSION['user_id'];
                        $stmt = $db->prepare("SELECT usuario FROM usuarios WHERE id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch();
                        echo $user['usuario'] ?? 'Usuario';
                        ?>
                    </span>
                    <a href="perfil.php"
                        class="ml-2 bg-white bg-opacity-20 hover:bg-opacity-30 p-1 rounded-full transition-all flex-shrink-0"
                        title="Mi Perfil">
                        <i class="fas fa-cog text-white"></i>
                    </a>
                </div>

                <!-- Botón de cerrar sesión -->
                <a href="logout.php"
                    class="bg-red-500 hover:bg-red-600 px-3 sm:px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center flex-shrink-0">
                    <i class="fas fa-sign-out-alt sm:mr-2"></i><span class="hidden sm:inline">Salir</span>
                </a>
            </div>

        </div>
    </header>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">
                <i class="fas fa-tools mr-2"></i>Gestión de Herramientas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Ver Inventario -->
                <a href="user_inventory.php"
                    class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 card-hover border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-500 rounded-full p-4 mr-6 icon-container shadow-md">
                            <i class="fas fa-boxes text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Ver Inventario</h2>
                            <p class="text-gray-600">Consulta las herramientas disponibles en el taller</p>
                        </div>
                    </div>
                    <div class="mt-6 text-right text-blue-500">
                        <span class="text-sm font-medium">Acceder <i class="fas fa-arrow-right ml-1"></i></span>
                    </div>
                </a>

                <!-- Gestionar Préstamos -->
                <a href="user_loans.php"
                    class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 card-hover border border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-green-500 rounded-full p-4 mr-6 icon-container shadow-md">
                            <i class="fas fa-hand-holding text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Gestionar Préstamos</h2>
                            <p class="text-gray-600">Solicita y gestiona préstamos de herramientas</p>
                        </div>
                    </div>
                    <div class="mt-6 text-right text-green-500">
                        <span class="text-sm font-medium">Acceder <i class="fas fa-arrow-right ml-1"></i></span>
                    </div>
                </a>
            </div>

            <!-- Quick Stats -->
            <div class="mt-10 bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
                    <i class="fas fa-user-circle mr-2"></i>Información del Usuario
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="p-3">
                        <i class="fas fa-user text-blue-400 text-xl mb-2"></i>
                        <h4 class="text-sm font-medium text-gray-500">Usuario</h4>
                        <p class="text-xl font-bold text-gray-800">
                            <?php echo htmlspecialchars($userData['usuario'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3">
                        <i class="fas fa-phone text-green-400 text-xl mb-2"></i>
                        <h4 class="text-sm font-medium text-gray-500">Teléfono</h4>
                        <p class="text-xl font-bold text-gray-800">
                            <?php echo htmlspecialchars($userData['telefono'] ?? '-'); ?></p>
                    </div>
                    <div class="p-3">
                        <i class="fas fa-clipboard-list text-purple-400 text-xl mb-2"></i>
                        <h4 class="text-sm font-medium text-gray-500">Total Préstamos</h4>
                        <p class="text-xl font-bold text-gray-800"><?php echo $prestamosCount; ?></p>
                    </div>
                    <div class="p-3">
                        <i class="fas fa-calendar-check text-amber-400 text-xl mb-2"></i>
                        <h4 class="text-sm font-medium text-gray-500">Miembro desde</h4>
                        <p class="text-xl font-bold text-gray-800"><?php echo date('M Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-[#2D3A36] text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p>© <?= date('Y') ?> SENA - Sistema de Gestión de Inventarios. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>
