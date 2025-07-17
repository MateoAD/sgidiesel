<?php
require_once 'includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Instructor - Taller Diesel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos para el dropdown de notificaciones */
        #notification-dropdown {
            transform-origin: top right;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #notification-btn:hover .fa-bell {
            animation: ring 0.5s ease;
        }

        @keyframes ring {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            50% {
                transform: rotate(-15deg);
            }

            75% {
                transform: rotate(10deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        #notification-count {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        body {
            background-image: url('./img/fondo_panel.png');
            /* Reemplaza con la ruta correcta de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;

            background-image: url('./img/fondo_panel.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            /* Color blanco con 70% de opacidad */
            z-index: -1;
        }

        /* Animaciones y efectos */
        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
        }

        .card-hover:hover {
            transform: translateY(-8px) rotateX(5deg);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .neumorphic {
            background: #f0f0f0;
            box-shadow: 8px 8px 16px #d9d9d9,
                -8px -8px 16px #ffffff;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .neumorphic:hover {
            box-shadow: 12px 12px 24px #d9d9d9,
                -12px -12px 24px #ffffff;
        }

        .polymorphic-card {
            clip-path: polygon(0% 0%,
                    100% 0%,
                    100% 80%,
                    80% 100%,
                    0% 100%);
            transition: clip-path 0.5s ease;
        }

        .polymorphic-card:hover {
            clip-path: polygon(0% 0%,
                    100% 0%,
                    100% 90%,
                    90% 100%,
                    0% 100%);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .glow {
            transition: box-shadow 0.3s ease;
        }

        .glow:hover {
            box-shadow: 0 0 15px rgba(0, 180, 0, 0.5);
        }

        /* Colores personalizados según la imagen */
        .bg-inventario {
            background-color: #4A655D;
        }

        .bg-prestamo {
            background-color: #00AF00;
        }

        .bg-ver-prestamos {
            background-color: #05976A;
        }

        .bg-aprendices {
            background-color: #3B82F6;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Header reorganizado -->
    <header class="bg-[#4A655D] text-white shadow-lg sticky top-0 z-10" style="background-color: #4A655D !important;">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">

            <!-- Logo y texto institucional -->
            <div class="flex items-center space-x-4">
                <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto">
                <div class="border-l-2 border-white pl-4">
                    <h1 class="text-xl font-bold">SENA</h1>
                </div>
            </div>

            <!-- Usuario y acciones -->
            <div class="flex items-center space-x-6">

                <!-- Perfil de usuario con nombre -->
                <div class="flex items-center bg-[#3A9171] px-4 py-2 rounded-full shadow-md">
                    <i class="fas fa-user-circle text-2xl mr-2 text-white"></i>
                    <span class="font-medium text-white whitespace-nowrap">
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
                        class="ml-2 bg-white bg-opacity-20 hover:bg-opacity-30 p-1 rounded-full transition-all"
                        title="Mi Perfil">
                        <i class="fas fa-cog text-white"></i>
                    </a>
                </div>

                <!-- Notificaciones -->
                <div class="relative group">
                    <button id="notification-btn"
                        class="relative p-2 rounded-full hover:bg-white hover:bg-opacity-20 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notification-count"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
                            0
                        </span>
                    </button>

                    <!-- Dropdown -->
                    <div id="notification-dropdown"
                        class="hidden absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-50 py-1">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800">Herramientas por agotarse</h3>
                        </div>
                        <div id="notification-content" class="max-h-60 overflow-y-auto">
                            <div class="px-4 py-3 text-sm text-gray-700">Cargando...</div>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200 text-right">
                            <a href="inventario.php" class="text-xs text-blue-600 hover:underline">Ver inventario
                                completo</a>
                        </div>
                    </div>
                </div>

                <!-- Botón de salir -->
                <a href="logout.php"
                    class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                </a>

            </div>
        </div>
    </header>



    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <!-- Panel Principal -->
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><i class="fas fa-chalkboard-teacher mr-2"></i>PANEL DEL
                INSTRUCTOR</h1>
            <p class="text-gray-600 mb-6">Bienvenido al sistema de gestión de inventario del Taller Diesel. Seleccione
                una opción para continuar.</p>

            <!-- Opciones principales con colores de la imagen -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <!-- Ver Inventario -->
                <div
                    class="bg-inventario text-white p-6 rounded-xl cursor-pointer card-hover transform hover:-rotate-1">
                    <a href="inventario.php">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-white bg-opacity-20 p-4 rounded-full mb-4 shadow-inner">
                                <i class="fas fa-tools text-white text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Ver Inventario</h3>
                            <p class="text-gray-100">Consultar herramientas y materiales</p>
                        </div>
                    </a>
                </div>

                <!-- Agregar Préstamo -->
                <div class="bg-prestamo text-white p-6 rounded-xl cursor-pointer card-hover transform hover:rotate-1">
                    <a href="prestamos.php">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-white bg-opacity-20 p-4 rounded-full mb-4 shadow-inner">
                                <i class="fas fa-hand-holding text-white text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Agregar Préstamo</h3>
                            <p class="text-gray-100">Registrar nuevo préstamo de herramientas</p>
                        </div>
                    </a>
                </div>

                <!-- Ver Préstamos -->
                <div
                    class="bg-ver-prestamos text-white p-6 rounded-xl cursor-pointer card-hover transform hover:-rotate-1">
                    <a href="reporte.php">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-white bg-opacity-20 p-4 rounded-full mb-4 shadow-inner">
                                <i class="fas fa-clipboard-list text-white text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Ver Préstamos</h3>
                            <p class="text-gray-100">Consultar préstamos activos</p>
                        </div>
                    </a>
                </div>

                <!-- Gestión de Aprendices -->
                <div class="bg-aprendices text-white p-6 rounded-xl cursor-pointer card-hover transform hover:rotate-1">
                    <a href="aprendicez.php">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-white bg-opacity-20 p-4 rounded-full mb-4 shadow-inner">
                                <i class="fas fa-user-graduate text-white text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Gestión de Aprendices</h3>
                            <p class="text-gray-100">Administrar aprendices y reportes</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 transition-all duration-500 hover:shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                <i class="fas fa-clock mr-2"></i> ACTIVIDAD RECIENTE
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Herramientas Prestadas -->
                <div id="herramientas-prestadas"
                    class="polymorphic-card bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-20">
                        <i class="fas fa-tools text-blue-500 text-8xl"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-5xl font-bold text-blue-600 mb-2 valor">0</p>
                        <p class="text-gray-700 font-medium">Herramientas Prestadas</p>
                        <div class="mt-4">
                            <span
                                class="inline-block bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-arrow-up mr-1"></i> <span id="herramientas-hoy">0</span> hoy
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Préstamos Activos -->
                <div id="prestamos-activos"
                    class="polymorphic-card bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-20">
                        <i class="fas fa-clipboard-check text-green-500 text-8xl"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-5xl font-bold text-green-600 mb-2 valor">0</p>
                        <p class="text-gray-700 font-medium">Préstamos Activos</p>
                        <div class="mt-4">
                            <span
                                class="inline-block bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock mr-1"></i> <span id="por-devolver">0</span> por devolver
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Fecha Actual -->
                <div id="fecha-actual"
                    class="polymorphic-card bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-20">
                        <i class="far fa-calendar-alt text-purple-500 text-8xl"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-5xl font-bold text-purple-600 mb-2 valor"><?= date('d/m/Y') ?></p>
                        <p class="text-gray-700 font-medium">Fecha Actual</p>
                        <div class="mt-4">
                            <span
                                class="inline-block bg-purple-200 text-purple-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-sync-alt mr-1"></i> Actualizado: <span
                                    id="hora-actualizacion"><?= date('H:i') ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Usuarios -->
        <div class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                <i class="fas fa-users mr-2"></i>GESTIÓN DE USUARIOS
            </h2>
            <div class="relative flex gap-4 items-center">
                <input type="text" id="searchUsers"
                    class="w-full px-4 py-2 pl-10 pr-8 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Buscar usuarios...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <select id="user-role-filter"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="aprendiz">Aprendiz</option>
                    <option value="almacenista">Almacenista</option>
                </select>
            </div>
            <br>
            <br>
            <div class="overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white sticky-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Rol
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-content" class="divide-y divide-gray-200">
                        <!-- Aquí se inserta el contenido dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL ROL -->
        <div id="permisosModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Asignar permisos</h3>
                    <button onclick="cerrarModalPermisos()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <button onclick="asignarRol('administrador')"
                        class="w-full flex items-center p-4 bg-purple-100 hover:bg-purple-200 rounded-lg transition">
                        <div
                            class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center text-white mr-4">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-purple-800">Hacer Administrador</h4>
                            <p class="text-sm text-purple-600">Acceso completo al sistema</p>
                        </div>
                    </button>

                    <button onclick="asignarRol('almacenista')"
                        class="w-full flex items-center p-4 bg-blue-100 hover:bg-blue-200 rounded-lg transition">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mr-4">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-blue-800">Hacer Almacenista</h4>
                            <p class="text-sm text-blue-600">Acceso a inventario y préstamos</p>
                        </div>
                    </button>

                    <button onclick="asignarRol('aprendiz')"
                        class="w-full flex items-center p-4 bg-green-100 hover:bg-green-200 rounded-lg transition">
                        <div
                            class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white mr-4">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold text-green-800">Hacer Aprendiz</h4>
                            <p class="text-sm text-green-600">Acceso básico al sistema</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Auditoría de Préstamos -->
        <div id="audit-table-container"
            class="polymorphic-container bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-2xl shadow-lg mb-8 overflow-hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
                <i class="fas fa-history mr-2"></i>REGISTRO DE AUDITORÍA
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="relative">
                    <input type="text" id="audit-search"
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Buscar en auditoría...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <div class="relative">
                    <select id="audit-action-filter"
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las acciones</option>
                        <option value="crear">Creaciones</option>
                        <option value="modificar">Modificaciones</option>
                        <option value="eliminar">Eliminaciones</option>
                        <option value="prestamo">Préstamos</option>
                        <option value="devolucion">Devoluciones</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white sticky-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Acción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Tabla Afectada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Detalles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="auditoria-content" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Cargando registros...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            function filterUsers() {
                const searchInput = document.getElementById('searchUsers');
                const filter = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('#usuarios-content tr');

                rows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    row.style.display = textContent.includes(filter) ? '' : 'none';
                });
            }

            async function cargarUsuarios() {
                const usuariosContent = document.getElementById('usuarios-content');

                try {
                    const response = await fetch('includes/get_users_data.php');

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Respuesta no es JSON: ${text.substring(0, 100)}...`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Error al cargar usuarios');
                    }

                    // Update your UI with the users data
                    let html = '';
                    data.users.forEach(user => {
                        const rowColor = user.rol === 'administrador'
                            ? 'bg-purple-50 hover:bg-purple-100'
                            : user.rol === 'aprendiz'
                                ? 'bg-green-50 hover:bg-green-100'
                                : 'bg-blue-50 hover:bg-blue-100';
                        const badgeColor = user.rol === 'administrador'
                            ? 'bg-purple-100 text-purple-800'
                            : user.rol === 'aprendiz'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-blue-100 text-blue-800';

                        html += `
                <tr class="${rowColor} transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.usuario}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeColor}">
                            ${user.rol}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.telefono || 'No registrado'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-4">
                           ${user.rol === 'administrador' ? `
    <button onclick="cambiarRol(${user.id}, 'usuario')" 
            class="text-purple-600 hover:text-purple-900 flex items-center mr-4 rounded-lg px-3 py-1.5 bg-purple-100 hover:bg-purple-200">
        <i class="fas fa-user-times mr-2"></i>
        Revocar admin
    </button>
` : `
   <button onclick="mostrarModalPermisos(${user.id})" 
            class="text-blue-600 hover:text-blue-900 flex items-center mr-4 rounded-lg px-3 py-1.5 bg-blue-100 hover:bg-blue-200">
        <i class="fas fa-user-shield mr-2"></i>
        Dar permisos
    </button>
`}
<button onclick="confirmDeactivate(${user.id})" 
        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md">
    <i class="fas fa-user-slash"></i> Desactivar
</button>
                        </div>
                    </td>
                </tr>
            `;
                    });

                    usuariosContent.innerHTML = html || `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No hay usuarios registrados
                </td>
            </tr>
        `;

                    // Vincular búsqueda
                    const searchInput = document.getElementById('searchUsers');
                    const roleFilter = document.getElementById('user-role-filter');
                    if (searchInput) {
                        searchInput.removeEventListener('input', filterUsers);
                        searchInput.addEventListener('input', filterUsers);
                    }
                    if (roleFilter) {
                        roleFilter.removeEventListener('change', filterUsers);
                        roleFilter.addEventListener('change', filterUsers);
                    }

                } catch (error) {
                    console.error('Error al cargar usuarios:', error);
                    usuariosContent.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-red-500">
                    Error al cargar los usuarios: ${error.message}
                </td>
            </tr>
        `;
                }
            }

            //FUNCIONES M0DAL ROL
            let usuarioSeleccionadoId = null;

            function mostrarModalPermisos(userId) {
                usuarioSeleccionadoId = userId;
                document.getElementById('permisosModal').style.display = 'flex';
            }

            function cerrarModalPermisos() {
                document.getElementById('permisosModal').style.display = 'none';
            }

            function asignarRol(rol) {
                cerrarModalPermisos();
                cambiarRol(usuarioSeleccionadoId, rol);
            }

            function filterUsers() {
                const searchInput = document.getElementById('searchUsers');
                const filter = searchInput.value.toLowerCase();
                const roleFilter = document.getElementById('user-role-filter').value;
                const rows = document.querySelectorAll('#usuarios-content tr');

                rows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    const matchesText = textContent.includes(filter);
                    const matchesRole = !roleFilter || textContent.includes(roleFilter.toLowerCase());
                    row.style.display = (matchesText && matchesRole) ? '' : 'none';
                });
            }

            function confirmDeactivate(userId) {
                if (confirm('¿Está seguro de desactivar este usuario?')) {
                    fetch('includes/delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${userId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }

            async function cambiarRol(userId, newRole) {
                try {
                    const action = newRole === 'administrador' ? 'dar' : 'revocar';
                    if (!confirm(`¿Está seguro de ${action} privilegios de administrador a este usuario?`)) return;

                    const formData = new FormData();
                    formData.append('user_id', userId);
                    formData.append('role', newRole);

                    const response = await fetch('includes/update_user_role.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Error al actualizar rol');
                    }

                    alert(`Rol ${action === 'dar' ? 'otorgado' : 'revocado'} correctamente`);
                    cargarUsuarios();

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al actualizar el rol del usuario');
                }
            }

            // Add to your DOMContentLoaded event listener
            document.addEventListener('DOMContentLoaded', function () {
                // ... existing code ...
                cargarUsuarios();
            });

            /**
             * Carga los registros de auditoría desde el backend y los muestra en la tabla de auditoría.
             * Interactúa con el endpoint 'includes/get_audit_data.php'.
             */

            async function cargarAuditoria() {
                const auditContent = document.getElementById('auditoria-content');

                try {
                    // Get search parameters
                    const searchTerm = document.getElementById('audit-search')?.value || '';
                    const actionFilter = document.getElementById('audit-action-filter')?.value || '';

                    // Build URL with parameters
                    let url = 'includes/get_audit_data.php?';
                    if (searchTerm) url += `search=${encodeURIComponent(searchTerm)}&`;
                    if (actionFilter) url += `action=${encodeURIComponent(actionFilter)}`;

                    // Make request to backend
                    const response = await fetch(url);

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Respuesta no es JSON: ${text.substring(0, 100)}...`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Error al cargar datos de auditoría');
                    }

                    // Generate HTML for audit table
                    let html = '';
                    data.audits.forEach(audit => {


                        // Process audit details
                        let detalles = audit.detalles_formateados;
                        try {
                            if (typeof detalles === 'string') {
                                detalles = JSON.parse(detalles);
                            }

                            // Format details based on affected table
                            if (audit.accion === 'crear' && audit.tabla_afectada.includes('usuarios')) {
                                detalles = `
                        <div class="flex flex-col">
                            <span class="font-medium">${detalles.descripcion || 'Nuevo usuario registrado'}</span>
                            ${detalles.usuario ? `<span class="text-xs">Usuario: ${detalles.usuario}</span>` : ''}
                            ${detalles.rol ? `<span class="text-xs">Rol: ${detalles.rol}</span>` : ''}
                        </div>
                    `;
                            } else if (audit.tabla_afectada.includes('herramientas')) {
                                // Improved for tool creation
                                if (audit.accion === 'crear') {
                                    detalles = `
                            <div class="flex flex-col">
                                <span class="font-medium">${detalles.descripcion || 'Herramienta creada'}</span>
                                ${detalles.nombre ? `<span class="text-xs">Nombre: ${detalles.nombre}</span>` : ''}
                                ${detalles.cantidad ? `<span class="text-xs">Cantidad: ${detalles.cantidad}</span>` : ''}
                                ${detalles.ubicacion ? `<span class="text-xs">Ubicación: ${detalles.ubicacion}</span>` : ''}
                                ${detalles.estado ? `<span class="text-xs">Estado: ${detalles.estado}</span>` : ''}
                            </div>
                        `;
                                } else {
                                    detalles = `
                            <div class="flex flex-col">
                                <span class="font-medium">${detalles.descripcion || 'Acción sobre herramienta'}</span>
                                ${detalles.nombre ? `<span class="text-xs">Nombre: ${detalles.nombre}</span>` : ''}
                                ${detalles.cantidad_nueva ? `
                                    <span class="text-xs">Cantidad: ${detalles.cantidad_anterior} → ${detalles.cantidad_nueva}</span>
                                    <span class="text-xs">Estado: ${detalles.estado_anterior} → ${detalles.estado_nuevo}</span>
                                ` : detalles.cantidad ? `<span class="text-xs">Cantidad: ${detalles.cantidad}</span>` : ''}
                            </div>
                        `;
                                }
                            } else {
                                detalles = `
                        <div class="flex flex-col">
                            <span class="font-medium">${detalles.descripcion || 'Acción del sistema'}</span>
                            ${detalles.usuario ? `<span class="text-xs">Usuario: ${detalles.usuario}</span>` : ''}
                            ${detalles.rol ? `<span class="text-xs">Rol: ${detalles.rol}</span>` : ''}
                        </div>
                    `;
                            }
                        } catch (e) {
                            detalles = `<div class="text-sm">${audit.detalles_formateados || 'Sin detalles'}</div>`;
                        }

                        // Action style configuration
                        const actionConfig = {
                            'crear': { icon: 'fa-plus-circle', bgColor: 'bg-green-50 hover:bg-green-100', textColor: 'text-green-800', borderColor: 'border-green-200', text: 'Creación' },
                            'modificar': { icon: 'fa-edit', bgColor: 'bg-blue-50 hover:bg-blue-100', textColor: 'text-blue-800', borderColor: 'border-blue-200', text: 'Modificación' },
                            'eliminar': { icon: 'fa-trash-alt', bgColor: 'bg-red-50 hover:bg-red-100', textColor: 'text-red-800', borderColor: 'border-red-200', text: 'Eliminación' },
                            'prestamo': { icon: 'fa-hand-holding', bgColor: 'bg-indigo-50 hover:bg-indigo-100', textColor: 'text-indigo-800', borderColor: 'border-indigo-200', text: 'Préstamo' },
                            'devolucion': { icon: 'fa-undo', bgColor: 'bg-yellow-50 hover:bg-yellow-100', textColor: 'text-yellow-800', borderColor: 'border-yellow-200', text: 'Devolución' },
                            'default': { icon: 'fa-info-circle', bgColor: 'bg-gray-50 hover:bg-gray-100', textColor: 'text-gray-800', borderColor: 'border-gray-200', text: 'Acción' }
                        };

                        const config = actionConfig[audit.accion] || actionConfig.default;

                        // Generate HTML for row
                        html += `
                <tr class="${config.bgColor} border-b ${config.borderColor} transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle ${config.textColor} text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium ${config.textColor}">${audit.nombre_usuario || 'Sistema'}</div>
                                <div class="text-xs ${config.textColor} opacity-70">ID: ${audit.usuario_id || '0'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${config.bgColor} ${config.textColor} border ${config.borderColor}">
                            <i class="fas ${config.icon} mr-2"></i>
                            ${config.text}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded ${config.bgColor} ${config.textColor} border ${config.borderColor}">
                            ${audit.tabla_afectada_formateada || 'Sistema'}
                        </span>
                    </td>
                    <td class="px-6 py-4 ${config.textColor}">
                        ${detalles}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${config.textColor}">
                        <div class="flex items-center">
                            <i class="far fa-clock mr-2 opacity-70"></i>
                            ${audit.fecha_formateada}
                        </div>
                    </td>
                </tr>
            `;
                    });

                    // Update table content
                    auditContent.innerHTML = html || `
            <tr class="bg-gray-50">
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No se encontraron registros con los filtros aplicados
                </td>
            </tr>
        `;

                } catch (error) {
                    console.error('Error al cargar auditoría:', error);
                    auditContent.innerHTML = `
            <tr class="bg-red-50">
                <td colspan="5" class="px-6 py-4 text-center text-red-500">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error al cargar los registros de auditoría
                    </div>
                    <div class="text-xs mt-1">${error.message}</div>
                </td>
            </tr>
        `;
                }
            }
            // Función para mostrar errores

            function mostrarError() {
                document.querySelectorAll('.valor').forEach(el => {
                    if (el.textContent === '0') el.textContent = '--';
                });
                document.getElementById('hora-actualizacion').textContent = 'Error';
            }

            // Llamar a la función cuando la página se carga
            document.addEventListener('DOMContentLoaded', function () {
                console.log("Página cargada, iniciando carga de datos...");
                cargarDatosActividad();
                cargarNotificaciones();
                cargarUsuarios();
                cargarAuditoria(); // Add this line to load audit data

                // Actualizar cada 30 segundos
                setInterval(cargarDatosActividad, 30000);
                setInterval(cargarNotificaciones, 30000);
            });

            document.getElementById('audit-search').addEventListener('input', function () {
                cargarAuditoria();
            });

            document.getElementById('audit-action-filter').addEventListener('change', function () {
                cargarAuditoria();
            });
            // Función mejorada para cargar datos de actividad
            async function cargarDatosActividad() {
                try {
                    console.log("Cargando datos de actividad...");

                    // Verifica la ruta correcta (ajusta según tu estructura de archivos)
                    const response = await fetch('includes/get_activity_data.php', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        },
                        cache: 'no-cache'
                    });

                    console.log("Respuesta recibida, status:", response.status);

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error("Error en respuesta:", errorText);
                        throw new Error(`Error HTTP: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log("Datos parseados:", data);

                    if (!data || !data.success) {
                        throw new Error(data.message || 'Respuesta no válida del servidor');
                    }

                    // Actualizar la interfaz de usuario
                    const updateField = (selector, value) => {
                        const element = document.querySelector(selector);
                        if (element) element.textContent = value !== undefined ? value : '--';
                    };

                    updateField('#herramientas-prestadas .valor', data.data.herramientas_hoy);
                    updateField('#herramientas-hoy', data.data.herramientas_hoy);
                    updateField('#prestamos-activos .valor', data.data.prestamos_activos);
                    updateField('#por-devolver', data.data.por_devolver);
                    updateField('#hora-actualizacion', data.data.hora_actualizacion);

                } catch (error) {
                    console.error('Error al cargar datos:', error);
                    mostrarError();

                    // Mostrar notificación más detallada (opcional)
                    const notification = document.createElement('div');
                    notification.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg';
                    notification.textContent = 'Error al cargar actividad. Ver consola para detalles.';
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                }
            }


            // Función para cargar las herramientas por agotarse
            async function cargarNotificaciones() {
                const notificationContent = document.getElementById('notification-content');
                const notificationCount = document.getElementById('notification-count');

                try {
                    notificationContent.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Cargando...</div>';

                    const response = await fetch('includes/get_low_stock_tools.php');

                    // Verifica si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Respuesta no es JSON: ${text.substring(0, 100)}...`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Error en la respuesta del servidor');
                    }

                    if (data.data.length > 0) {
                        // Actualizar contador
                        notificationCount.textContent = data.data.length;
                        notificationCount.classList.remove('hidden');


                        // Construir contenido de notificaciones
                        let html = '';
                        data.data.forEach(tool => {
                            const estadoClass = tool.estado === 'recargar' ? 'text-red-600' : 'text-yellow-600';
                            const estadoText = tool.estado === 'recargar' ? '<i class="fas fa-exclamation-circle mr-1"></i>Agotándose' : '<i class="fas fa-exclamation-triangle mr-1"></i>Medio';

                            html += `
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-800">${tool.nombre}</span>
                            <span class="text-xs ${estadoClass}">${estadoText}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Cantidad: ${tool.cantidad} | ${tool.ubicacion || 'Sin ubicación'}
                        </div>
                    </div>
                `;
                        });

                        notificationContent.innerHTML = html;
                    } else {
                        notificationCount.classList.add('hidden');
                        notificationContent.innerHTML =
                            '<div class="px-4 py-3 text-sm text-gray-700">No hay herramientas por agotarse</div>';
                    }
                } catch (error) {
                    console.error('Error al cargar notificaciones:', error);
                    notificationCount.classList.add('hidden');
                    notificationContent.innerHTML = `
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="text-red-600 font-medium">Error al cargar notificaciones</div>
                <div class="text-xs text-gray-500 mt-1">${error.message}</div>
            </div>
        `;
                }
            }
            // Mostrar/ocultar dropdown
            document.getElementById('notification-btn').addEventListener('click', function (e) {
                e.stopPropagation();
                const dropdown = document.getElementById('notification-dropdown');
                dropdown.classList.toggle('hidden');

                // Cargar notificaciones solo cuando se abre el dropdown
                if (!dropdown.classList.contains('hidden')) {
                    cargarNotificaciones();
                }
            });

            // Llamar a la función cuando la página se carga
            document.addEventListener('DOMContentLoaded', function () {
                console.log("Página cargada, iniciando carga de datos...");
                cargarDatosActividad();
                cargarNotificaciones();

                // Actualizar cada 30 segundos
                setInterval(cargarDatosActividad, 30000);
                setInterval(cargarNotificaciones, 30000);
            });

            async function eliminarUsuario(userId) {
                try {
                    if (!confirm('¿Está seguro de que desea eliminar este usuario?')) return;

                    const response = await fetch('includes/delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${userId}`
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Error al eliminar usuario');
                    }

                    alert(data.message);
                    cargarUsuarios();

                } catch (error) {
                    console.error('Error:', error);
                    alert(`Error: ${error.message}`);
                }
            }

        </script>
</body>

</html>