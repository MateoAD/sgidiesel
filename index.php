<?php
session_start();
require_once 'includes/database.php';

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
    header('Location: ' . ($_SESSION['rol'] === 'administrador' ? 'dashboard.php' : 'user_dashboard.php'));
    exit;
}

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
    // Verificar si el usuario sigue activo
    $checkActive = $db->prepare("SELECT activo FROM usuarios WHERE id = ?");
    $checkActive->execute([$_SESSION['user_id']]);
    $activeStatus = $checkActive->fetchColumn();
    
    if ($activeStatus == 0) {
        session_destroy();
        header('Location: index.php?error=inactive');
        exit;
    }
    
    header('Location: ' . ($_SESSION['rol'] === 'administrador' ? 'dashboard.php' : 'user_dashboard.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGS de Taller Diesel - Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="./img/favicon-16x16.png">
    <style>
        body {
            background-image: url('./img/fondo_registro.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            z-index: -1;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Animaciones para los formularios */
        .form-container {
            transition: all 0.5s ease;
        }

        .form-hidden {
            opacity: 0;
            height: 0;
            overflow: hidden;
            transform: translateX(-20px);
        }

        .form-visible {
            opacity: 1;
            height: auto;
            transform: translateX(0);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <header class="bg-[#4A655D] text-white shadow-md">
        <div class="container mx-auto flex items-center px-4 py-3">
            <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto mr-4">
            <div>
                <h1 class="text-xl font-bold">SENA</h1>
                <p class="text-sm">Servicio Nacional de Aprendizaje</p>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-8">
        <div class="w-full max-w-md mx-4">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-[#56B847] text-white text-center py-4">
                    <h1 class="text-2xl font-bold">SGS DE TALLER DIESEL</h1>
                    <p class="text-sm mt-1">Sistema de Gestión de Taller</p>
                </div>

                <!-- Contenedor de formularios -->
                <div class="relative overflow-hidden">
                    <!-- Formulario de Login -->
                    <div id="loginFormContainer" class="form-container form-visible">
                        <form class="p-6" id="loginForm">
                            <div id="errorMessage" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                            </div>

                            <div class="mb-4">
                                <label for="username"
                                    class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-user"></i></span>
                                    <input type="text" id="username" name="username" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Ingrese su nombre completo" autocomplete="username">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password"
                                    class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password" name="password" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Ingrese su contraseña" autocomplete="current-password">
                                    <button type="button" id="togglePassword" class="px-3 text-gray-500">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4 flex justify-between items-center">
                                <div class="flex items-center">
                                    <input type="checkbox" id="remember" name="remember" class="mr-2">
                                    <label for="remember" class="text-sm text-gray-600">Recordar sesión</label>
                                </div>
                                <a href="#" id="forgotPassword" class="text-[#00AF00] hover:underline text-sm">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                           
                            <button type="submit" id="loginButton"
                                class="w-full bg-[#00AF00] text-white font-bold py-2 px-4 rounded hover:bg-[#4A655D] transition duration-300 flex justify-center items-center">
                                <span id="buttonText">INGRESAR</span>
                                <div id="loadingSpinner" class="loading-spinner ml-2"></div>
                            </button>

                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600">¿No tienes una cuenta?
                                    <a href="#" id="showRegisterForm"
                                        class="text-[#00AF00] font-medium hover:underline">Regístrate aquí</a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Formulario de Registro -->
                    <div id="registerFormContainer" class="form-container form-hidden">
                        <form class="p-6" id="registerForm">
                            <div id="registerErrorMessage"
                                class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded text-sm"></div>
                            <div id="registerSuccessMessage"
                                class="hidden mb-4 p-3 bg-green-100 text-green-700 rounded text-sm"></div>

                            <h2 class="text-xl font-bold text-[#05976A] mb-4 text-center">Crear nueva cuenta</h2>

                            <div class="mb-4">
                                <label for="registerUsername"
                                    class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-user"></i></span>
                                    <input type="text" id="registerUsername" name="username" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Ingrese su nombre completo" autocomplete="username">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de usuario:</label>
                                <div class="flex flex-col gap-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="role" value="aprendiz" class="form-radio text-[#05976A]" checked onchange="document.getElementById('fichaContainer').classList.remove('hidden');">
                                        <span class="ml-2">Aprendiz</span>
                                    </label>
                                </div>
                            </div>


                            <!-- Campo de Ficha (inicialmente oculto) -->
                            <div id="fichaContainer" class="mb-4">
                                <label for="registerFicha" class="block text-gray-700 text-sm font-bold mb-2">Número de Ficha:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-id-card"></i></span>
                                    <input type="text" id="registerFicha" name="ficha" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Ingrese un número de ficha valido">
                                </div>
                                <p id="fichaError" class="text-red-500 text-xs italic hidden">La ficha ingresada no es válida</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="registerPassword"
                                    class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="registerPassword" name="password" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Crea una contraseña segura" autocomplete="new-password">
                                    <button type="button" id="toggleRegisterPassword" class="px-3 text-gray-500">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">La contraseña debe tener al menos 8 caracteres</p>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword"
                                    class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="confirmPassword" name="confirmPassword" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Repite tu contraseña" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="registerPhone" class="block text-gray-700 text-sm font-bold mb-2">Número de
                                    WhatsApp:</label>
                                <div class="flex items-center border rounded shadow-sm">
                                    <span class="px-3 text-gray-500"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" id="registerPhone" name="phone" required
                                        class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                                        placeholder="Ej: +573001234567">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Formato: +573001234567 (incluyendo código de país)
                                </p>
                            </div>

                            <button type="submit" id="registerButton"
                                class="w-full bg-[#00AF00] text-white font-bold py-2 px-4 rounded hover:bg-[#4A655D] transition duration-300 flex justify-center items-center">
                                <span id="registerButtonText">REGISTRARSE</span>
                                <div id="registerLoadingSpinner" class="loading-spinner ml-2"></div>
                            </button>

                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600">¿Ya tienes una cuenta?
                                    <a href="#" id="showLoginForm"
                                        class="text-[#00AF00] font-medium hover:underline">Inicia sesión aquí</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para recuperación de contraseña -->
    <div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-[#05976A]">Recuperar Contraseña</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="recoveryForm">
                <div id="recoveryMessage" class="hidden mb-4 p-3 rounded text-sm"></div>
                <div class="mb-4">
                    <label for="recoveryUsername" class="block text-gray-700 text-sm font-bold mb-2">Usuario:</label>
                    <div class="flex items-center border rounded shadow-sm">
                        <span class="px-3 text-gray-500"><i class="fas fa-user"></i></span>
                        <input type="text" id="recoveryUsername" required
                            class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                            placeholder="Ingrese su usuario registrado">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="recoveryPhone" class="block text-gray-700 text-sm font-bold mb-2">Número de
                        WhatsApp:</label>
                    <div class="flex items-center border rounded shadow-sm">
                        <span class="px-3 text-gray-500"><i class="fab fa-whatsapp"></i></span>
                        <input type="tel" id="recoveryPhone" required
                            class="w-full py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#05976A] focus:border-transparent"
                            placeholder="Ingrese su número de WhatsApp">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Formato: +573001234567 (incluyendo código de país)</p>
                </div>
                <button type="submit" id="recoveryButton"
                    class="w-full bg-[#05976A] text-white font-bold py-2 px-4 rounded hover:bg-[#047A5B] transition duration-300 flex justify-center items-center">
                    <span id="recoveryButtonText">Enviar enlace de recuperación</span>
                    <div id="recoveryLoadingSpinner" class="loading-spinner ml-2"></div>
                </button>
            </form>
        </div>
    </div>
    <!-- Modal para ingreso de token y nueva contraseña -->
    <div id="tokenModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-[#05976A]">Restablecer Contraseña</h3>
                <button id="closeTokenModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="tokenForm">
                <div id="tokenMessage" class="hidden mb-4 p-3 rounded text-sm"></div>
                <div class="mb-4">
                    <label for="tokenInput" class="block text-gray-700 text-sm font-bold mb-2">Código de
                        recuperación:</label>
                    <input type="text" id="tokenInput" required
                        class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                        placeholder="Ingrese el código recibido">
                </div>
                <div class="mb-4">
                    <label for="newPassword" class="block text-gray-700 text-sm font-bold mb-2">Nueva
                        contraseña:</label>
                    <input type="password" id="newPassword" required
                        class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                        placeholder="Ingrese nueva contraseña">
                </div>
                <div class="mb-4">
                    <label for="confirmNewPassword" class="block text-gray-700 text-sm font-bold mb-2">Confirmar nueva
                        contraseña:</label>
                    <input type="password" id="confirmNewPassword" required
                        class="w-full py-2 px-3 border rounded focus:outline-none focus:ring-2 focus:ring-[#05976A]"
                        placeholder="Repita la nueva contraseña">
                </div>
                <button type="submit"
                    class="w-full bg-[#05976A] text-white font-bold py-2 px-4 rounded hover:bg-[#047A5B] transition duration-300">
                    Restablecer contraseña
                </button>
            </form>
        </div>
    </div>
    
     <!-- Script de reCAPTCHA -->
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Mostrar/ocultar contraseña en registro
        document.getElementById('toggleRegisterPassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('registerPassword');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Cambiar entre formularios de login y registro
        document.getElementById('showRegisterForm').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('loginFormContainer').classList.remove('form-visible');
            document.getElementById('loginFormContainer').classList.add('form-hidden');

            setTimeout(() => {
                document.getElementById('registerFormContainer').classList.remove('form-hidden');
                document.getElementById('registerFormContainer').classList.add('form-visible');
            }, 50);
        });

        document.getElementById('showLoginForm').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('registerFormContainer').classList.remove('form-visible');
            document.getElementById('registerFormContainer').classList.add('form-hidden');

            setTimeout(() => {
                document.getElementById('loginFormContainer').classList.remove('form-hidden');
                document.getElementById('loginFormContainer').classList.add('form-visible');
            }, 50);
        });
        // Token modal handling
        const tokenModal = document.getElementById('tokenModal');
        const closeTokenModal = document.getElementById('closeTokenModal');
        const tokenForm = document.getElementById('tokenForm');

        closeTokenModal.addEventListener('click', function () {
            tokenModal.classList.add('hidden');
        });

        // Password recovery modal handling
        const passwordModal = document.getElementById('passwordModal');
        const recoveryForm = document.getElementById('recoveryForm');
        const recoveryMessage = document.getElementById('recoveryMessage');
        const recoveryButtonText = document.getElementById('recoveryButtonText');
        const recoverySpinner = document.getElementById('recoveryLoadingSpinner');

        document.getElementById('forgotPassword').addEventListener('click', function (e) {
            e.preventDefault();
            passwordModal.classList.remove('hidden');
        });

        document.getElementById('closeModal').addEventListener('click', function () {
            passwordModal.classList.add('hidden');
            recoveryForm.reset();
            recoveryMessage.classList.add('hidden');
        });

        // Recovery form submission
        recoveryForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('recoveryUsername').value;
            const phone = document.getElementById('recoveryPhone').value;

            // Basic phone number validation
            const phoneRegex = /^\+[0-9]{11,15}$/;
            if (!phoneRegex.test(phone)) {
                recoveryMessage.textContent = 'Por favor, ingrese un número de WhatsApp válido con formato internacional (+573001234567)';
                recoveryMessage.classList.remove('hidden');
                recoveryMessage.classList.add('bg-red-100', 'text-red-700');
                recoveryMessage.classList.remove('bg-green-100', 'text-green-700');
                return;
            }

            // Show loading state
            recoveryButtonText.textContent = 'Enviando...';
            recoverySpinner.classList.remove('hidden');

            try {
                const response = await fetch('includes/recovery_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        phone: phone
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Open WhatsApp in new tab
                    window.open(data.whatsapp_link, '_blank');

                    // Show success message
                    recoveryMessage.textContent = 'Se ha enviado el código de recuperación a tu WhatsApp';
                    recoveryMessage.classList.remove('hidden', 'bg-red-100', 'text-red-700');
                    recoveryMessage.classList.add('bg-green-100', 'text-green-700');

                    // Close recovery modal and reset form
                    passwordModal.classList.add('hidden');
                    recoveryForm.reset();

                    // Show token modal after a short delay
                    setTimeout(() => {
                        tokenModal.classList.remove('hidden');
                    }, 1000);

                } else {
                    recoveryMessage.textContent = data.message;
                    recoveryMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                    recoveryMessage.classList.add('bg-red-100', 'text-red-700');
                }
            } catch (error) {
                recoveryMessage.textContent = 'Error al procesar la solicitud';
                recoveryMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                recoveryMessage.classList.add('bg-red-100', 'text-red-700');
            } finally {
                recoveryButtonText.textContent = 'Enviar';
                recoverySpinner.classList.add('hidden');
            }
        });

        //envio del formulario
        tokenForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const token = document.getElementById('tokenInput').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmNewPassword').value;
            const tokenMessage = document.getElementById('tokenMessage');

            // Validate passwords match
            if (newPassword !== confirmPassword) {
                tokenMessage.textContent = 'Las contraseñas no coinciden';
                tokenMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                tokenMessage.classList.add('bg-red-100', 'text-red-700');
                return;
            }

            // Validate password length
            if (newPassword.length < 8) {
                tokenMessage.textContent = 'La contraseña debe tener al menos 8 caracteres';
                tokenMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                tokenMessage.classList.add('bg-red-100', 'text-red-700');
                return;
            }

            // Send to server
            fetch('includes/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token: token,
                    newPassword: newPassword
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        tokenMessage.textContent = 'Contraseña actualizada correctamente';
                        tokenMessage.classList.remove('hidden', 'bg-red-100', 'text-red-700');
                        tokenMessage.classList.add('bg-green-100', 'text-green-700');

                        // Close modal after 3 seconds
                        setTimeout(() => {
                            tokenModal.classList.add('hidden');
                            tokenForm.reset();
                            // Redirect to login page
                            window.location.href = 'index.php';
                        }, 3000);
                    } else {
                        tokenMessage.textContent = data.message;
                        tokenMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                        tokenMessage.classList.add('bg-red-100', 'text-red-700');
                    }
                })
                .catch(error => {
                    tokenMessage.textContent = 'Error al procesar la solicitud';
                    tokenMessage.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                    tokenMessage.classList.add('bg-red-100', 'text-red-700');
                    console.error('Error:', error);
                });
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('showTokenModal')) {
            tokenModal.classList.remove('hidden');
        }

        // Envío de formulario de login
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            const errorMessage = document.getElementById('errorMessage');
            const buttonText = document.getElementById('buttonText');
            const spinner = document.getElementById('loadingSpinner');

            // Mostrar estado de carga
            buttonText.textContent = 'VERIFICANDO...';
            spinner.style.display = 'block';
            errorMessage.classList.add('hidden');

            // Enviar datos al servidor
            fetch('includes/login_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    remember: remember
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'dashboard.php';
                    } else {
                        errorMessage.textContent = data.message;
                        errorMessage.classList.remove('hidden');
                        buttonText.textContent = 'INGRESAR';
                        spinner.style.display = 'none';
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'Error de conexión con el servidor';
                    errorMessage.classList.remove('hidden');
                    buttonText.textContent = 'INGRESAR';
                    spinner.style.display = 'none';
                });
        });

                // Envío de formulario de registro
                document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('registerUsername').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const phone = document.getElementById('registerPhone').value;
            const role = 'aprendiz'; // Siempre será aprendiz
            const ficha = document.getElementById('registerFicha')?.value;
            const errorMessage = document.getElementById('registerErrorMessage');
            const successMessage = document.getElementById('registerSuccessMessage');
            const buttonText = document.getElementById('registerButtonText');
            const spinner = document.getElementById('registerLoadingSpinner');

            // Validaciones
            if (password.length < 8) {
                errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres';
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
                return;
            }

            if (password !== confirmPassword) {
                errorMessage.textContent = 'Las contraseñas no coinciden';
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
                return;
            }

            const phoneRegex = /^\+[0-9]{11,15}$/;
            if (!phoneRegex.test(phone)) {
                errorMessage.textContent = 'Por favor, ingrese un número de WhatsApp válido con formato internacional (+573001234567)';
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
                return;
            }

            // Validar ficha si el rol es aprendiz
            if (role === 'aprendiz') {
                if (!ficha) {
                    errorMessage.textContent = 'Por favor, ingrese el número de ficha';
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch('includes/validate_ficha.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ ficha: ficha })
                    });

                    const validationData = await response.json();
                    if (!validationData.valid) {
                        errorMessage.textContent = 'La ficha ingresada no es válida';
                        errorMessage.classList.remove('hidden');
                        successMessage.classList.add('hidden');
                        return;
                    }
                } catch (error) {
                    errorMessage.textContent = 'Error al validar la ficha';
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                    return;
                }
            }

            // Mostrar estado de carga
            buttonText.textContent = 'REGISTRANDO...';
            spinner.style.display = 'block';
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            // Enviar datos al servidor
            fetch('includes/register_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    phone: phone,
                    role: role,
                    ficha: role === 'aprendiz' ? ficha : null
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = '¡Registro exitoso! Redirigiendo...';
                        successMessage.classList.remove('hidden');
                        errorMessage.classList.add('hidden');

                        // Redirigir después de 2 segundos
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 2000);
                    } else {
                        errorMessage.textContent = data.message || 'Error al registrar el usuario';
                        errorMessage.classList.remove('hidden');
                        successMessage.classList.add('hidden');
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'Error de conexión con el servidor';
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                })
                .finally(() => {
                    buttonText.textContent = 'REGISTRARSE';
                    spinner.style.display = 'none';
                });
        });

       // Recordar usuario al recargar la página
       document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('rememberUser') === 'true') {
                document.getElementById('username').value = localStorage.getItem('savedUsername') || '';
                document.getElementById('remember').checked = true;
            }
        });

        // Guardar usuario si se marca "Recordar sesión"
        document.getElementById('remember').addEventListener('change', function () {
            if (this.checked) {
                localStorage.setItem('rememberUser', 'true');
                localStorage.setItem('savedUsername', document.getElementById('username').value);
            } else {
                localStorage.removeItem('rememberUser');
                localStorage.removeItem('savedUsername');
            }
        });

        // Mostrar/ocultar campo de ficha según el rol seleccionado
document.getElementById('registerRole').addEventListener('change', function() {
    const fichaContainer = document.getElementById('fichaContainer');
    if (this.value === 'aprendiz') {
        fichaContainer.classList.remove('hidden');
        document.getElementById('registerFicha').required = true;
    } else {
        fichaContainer.classList.add('hidden');
        document.getElementById('registerFicha').required = false;
    }
});


    </script>
</body>

</html>