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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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

        .auth-container {
            width: 90%;
            max-width: 900px;
            min-height: 500px;
            margin: 2rem auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .slider-wrapper {
            display: flex;
            width: 200%;
            height: 100%;
            transition: transform 0.6s ease;
        }

        .panel {
            width: 50%;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-panel {
            background: linear-gradient(135deg, #56B847 0%, #4A655D 100%);
            color: white;
            text-align: center;
        }

        .form-panel {
            background: white;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        /* Login state */
        .login-state .slider-wrapper {
            transform: translateX(0);
        }

        /* Register state */
        .register-state .slider-wrapper {
            transform: translateX(-50%);
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .welcome-text {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .toggle-btn:hover {
            background: white;
            color: #4A655D;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-subtitle {
            color: #718096;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 0.9rem 0.9rem 2.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #56B847;
            box-shadow: 0 0 0 3px rgba(86, 184, 71, 0.1);
            background: white;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #56B847, #4A655D);
            color: white;
            padding: 0.9rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(86, 184, 71, 0.3);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .form-link {
            color: #56B847;
            font-weight: 600;
            text-decoration: none;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            margin-right: 8px;
            cursor: pointer;
            position: relative;
        }

        .custom-checkbox:checked {
            background-color: #56B847;
            border-color: #56B847;
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
        }

        .loading-spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            border: 1px solid #feb2b2;
        }

        .success-message {
            background: #c6f6d5;
            color: #2f855a;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            border: 1px solid #9ae6b4;
        }

        /* Modal styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #a0aec0;
            cursor: pointer;
        }

        /* Animación suave al alternar formularios en móvil */
        @keyframes formSwitchIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

             /* Responsive adjustments - Mobile First Approach */
        @media (max-width: 1024px) {
            .auth-container {
                width: 90%;
                margin: 1rem auto;
                border-radius: 12px;
            }
            
            header .container {
                padding: 0.75rem 1rem;
            }
            
            header img {
                height: 40px;
                margin-right: 0.75rem;
            }
            
            header h1 {
                font-size: 1.1rem;
            }
            
            header p {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .auth-container {
                width: 95%;
                max-width: 460px;
                min-height: auto;
                margin: 0.5rem auto;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .slider-wrapper {
                width: 100%;
                transform: none !important;
            }

            .panel {
                width: 100%;
                padding: 1.25rem;
            }

            .welcome-panel {
                display: none;
            }

            .form-panel {
                display: none;
            }

            .form-panel.mobile-active {
                display: flex;
                animation: formSwitchIn 0.35s ease both;
            }

            .welcome-title {
                font-size: 1.4rem;
                margin-bottom: 0.75rem;
            }

            .welcome-text {
                font-size: 0.85rem;
                margin-bottom: 1.25rem;
                line-height: 1.4;
            }

            .form-title {
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .form-subtitle {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .form-input {
                padding: 0.85rem 0.85rem 0.85rem 2.8rem;
                font-size: 16px; /* Previene zoom en iOS */
                border-radius: 8px;
                min-height: 48px; /* Mejor tamaño táctil */
            }

            .input-icon {
                left: 0.9rem;
                font-size: 1rem;
            }

            .toggle-password {
                right: 0.9rem;
                font-size: 1rem;
            }

            .checkbox-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .form-link {
                font-size: 0.85rem;
                margin-top: 0.5rem;
            }

            .submit-btn, .toggle-btn {
                padding: 0.9rem;
                font-size: 0.95rem;
                min-height: 50px;
                border-radius: 8px;
                font-weight: 600;
            }

            .form-footer {
                margin-top: 1.5rem;
            }

            .form-footer p {
                font-size: 0.85rem;
            }

            main {
                padding: 1rem 0;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                width: 100%;
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }

            .panel {
                padding: 1rem;
            }

            .welcome-title {
                font-size: 1.3rem;
            }

            .welcome-text {
                font-size: 0.8rem;
            }

            .form-title {
                font-size: 1.2rem;
            }

            .form-input {
                padding: 0.75rem 0.75rem 0.75rem 2.5rem;
                font-size: 16px;
                min-height: 44px;
            }

            .input-icon {
                left: 0.8rem;
                font-size: 0.9rem;
            }

            .toggle-password {
                right: 0.8rem;
                font-size: 0.9rem;
            }

            .submit-btn, .toggle-btn {
                padding: 0.8rem;
                font-size: 0.9rem;
                min-height: 46px;
            }

            .checkbox-group label {
                font-size: 0.8rem;
            }

            /* Optimización específica para formularios móviles */
            .input-group {
                margin-bottom: 1rem;
            }

            /* Mejorar la experiencia táctil */
            .submit-btn:active, .toggle-btn:active {
                transform: scale(0.98);
            }

            /* Asegurar que los inputs no se salgan de la pantalla */
            input, select, textarea {
                max-width: 100%;
            }

            /* Optimizar el logo en móviles */
            .container.mx-auto.flex.items-center.px-4.py-3.justify-center img {
                height: 40px;
                max-width: 80px;
            }
        }

        @media (max-width: 320px) {
            .panel {
                padding: 0.75rem;
            }

            .form-title {
                font-size: 1.1rem;
            }

            .form-input {
                padding: 0.65rem 0.65rem 0.65rem 2.3rem;
                font-size: 14px;
            }

            .input-icon {
                left: 0.7rem;
                font-size: 0.85rem;
            }

            .submit-btn, .toggle-btn {
                padding: 0.7rem;
                font-size: 0.85rem;
            }

            .welcome-title {
                font-size: 1.1rem;
            }

            .welcome-text {
                font-size: 0.75rem;
            }
        }

        /* Orientación landscape en móviles */
        @media (max-width: 768px) and (orientation: landscape) {
            .auth-container {
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            }

            .panel {
                padding: 1rem;
            }

            .input-group {
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">
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
        <div class="auth-container login-state bg-white" id="authContainer">
            <div class="slider-wrapper">
                <!-- Login Panel -->
                <div class="panel form-panel" id="loginFormPanel">
                    <div style="max-width: 400px; margin: 0 auto; width: 100%;">
                        <div class="container mx-auto flex items-center px-4 py-3 justify-center">
    <img src="./img/logoSena.png" alt="SENA Logo" class="h-16 w-auto">
</div>
                        <h2 class="form-title">Iniciar Sesión</h2>
                        <p class="form-subtitle">SGI Taller Diesel</p>

                        <form id="loginForm">
                            <div id="loginErrorMessage" class="error-message hidden"></div>

                            <div class="input-group">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="username" name="username" class="form-input"
                                    placeholder="Nombre Completo" required autocomplete="username">
                            </div>

                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="form-input"
                                    placeholder="Contraseña" required autocomplete="current-password">
                                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" id="remember" name="remember" class="custom-checkbox" style="accent-color: #4BFF04FF;">
                                <label for="remember" style="font-size: 0.9rem; color: #4a5568;">Recordar sesión</label>
                                <a href="#" id="forgotPassword" class="form-link ml-auto">¿Olvidaste tu contraseña?</a>
                            </div>

                            <button type="submit" class="submit-btn" id="loginButton">
                                <span id="loginButtonText">Ingresar</span>
                                <div id="loginLoadingSpinner" class="loading-spinner hidden"></div>
                            </button>

                            <div class="form-footer">
                                <p style="color: #718096;">¿No tienes una cuenta?
                                    <a href="#" id="showRegisterForm" class="form-link">Regístrate aquí</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Welcome Panel for Login -->
                <div class="panel welcome-panel">
                    <div style="max-width: 300px; margin: 0 auto;">
                        <h2 class="welcome-title">¡Bienvenido de vuelta!</h2>
                        <p class="welcome-text">Para acceder al inventario del taller diesel, por favor inicie sesión con su

                            información personal</p>
                        <button class="toggle-btn" id="loginWelcomeBtn">Registrarse</button>
                    </div>
                </div>

                <!-- Register Panel -->
                <div class="panel form-panel" id="registerFormPanel">
                    <div style="max-width: 400px; margin: 0 auto; width: 100%;">
                        <h2 class="form-title">Crear Cuenta</h2>
                        <p class="form-subtitle">Únete a nuestro sistema</p>

                        <form id="registerForm">
                            <div id="registerErrorMessage" class="error-message hidden"></div>
                            <div id="registerSuccessMessage" class="success-message hidden"></div>

                            <div class="input-group">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="registerUsername" name="username" class="form-input"
                                    placeholder="Nombre Completo" required autocomplete="username">
                            </div>

                            <div class="input-group">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="registerFicha" name="ficha" class="form-input"
                                    placeholder="Número de Ficha" required>
                            </div>

                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="registerPassword" name="password" class="form-input"
                                    placeholder="Contraseña (min. 8 caracteres)" required autocomplete="new-password">
                                <i class="fas fa-eye toggle-password" id="toggleRegisterPassword"></i>
                            </div>

                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-input"
                                    placeholder="Confirmar Contraseña" required autocomplete="new-password">
                            </div>

                            <div class="input-group">
                                <i class="fab fa-whatsapp input-icon"></i>
                                <input type="tel" id="registerPhone" name="phone" class="form-input"
                                    placeholder="+573001234567" required>
                            </div>

                            <button type="submit" class="submit-btn" id="registerButton">
                                <span id="registerButtonText">Registrarse</span>
                                <div id="registerLoadingSpinner" class="loading-spinner hidden"></div>
                            </button>

                            <div class="form-footer">
                                <p style="color: #718096;">¿Ya tienes una cuenta?
                                    <a href="#" id="showLoginForm" class="form-link">Inicia sesión aquí</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Welcome Panel for Register -->
                <div class="panel welcome-panel">
                    <div style="max-width: 300px; margin: 0 auto;">
                        <h2 class="welcome-title">¡Hola, aprendiz!</h2>
                        <p class="welcome-text">Ingresa tus datos personales y comienza tu experiencia con nosotros</p>

                        <button class="toggle-btn" id="registerWelcomeBtn">Iniciar Sesión</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Password Recovery Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Recuperar Contraseña</h3>
                <button id="closeModal" class="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="recoveryForm">
                <div id="recoveryMessage" class="hidden"></div>

                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="recoveryUsername" class="form-input" placeholder="Usuario" required>
                </div>

                <div class="input-group">
                    <i class="fab fa-whatsapp input-icon"></i>
                    <input type="tel" id="recoveryPhone" class="form-input" placeholder="+573001234567" required>
                </div>

                <button type="submit" class="submit-btn" id="recoveryButton">
                    <span id="recoveryButtonText">Enviar enlace de recuperación</span>
                    <div id="recoveryLoadingSpinner" class="loading-spinner hidden"></div>
                </button>
            </form>
        </div>
    </div>

    <!-- Token Modal -->
    <div id="tokenModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Restablecer Contraseña</h3>
                <button id="closeTokenModal" class="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="tokenForm">
                <div id="tokenMessage" class="hidden"></div>

                <div class="input-group">
                    <i class="fas fa-key input-icon"></i>
                    <input type="text" id="tokenInput" class="form-input" placeholder="Código de recuperación" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="newPassword" class="form-input" placeholder="Nueva contraseña" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="confirmNewPassword" class="form-input"
                        placeholder="Confirmar nueva contraseña" required>
                </div>

                <button type="submit" class="submit-btn">
                    Restablecer contraseña
                </button>
            </form>
        </div>
    </div>

    <script>
        // Slider functionality
        const authContainer = document.getElementById('authContainer');
        const showRegisterForm = document.getElementById('showRegisterForm');
        const showLoginForm = document.getElementById('showLoginForm');
        const loginWelcomeBtn = document.getElementById('loginWelcomeBtn');
        const registerWelcomeBtn = document.getElementById('registerWelcomeBtn');
        const loginFormPanel = document.getElementById('loginFormPanel');
        const registerFormPanel = document.getElementById('registerFormPanel');

        function applyResponsiveFormState() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            const isLoginState = authContainer.classList.contains('login-state');

            if (isMobile) {
                loginFormPanel.classList.toggle('mobile-active', isLoginState);
                registerFormPanel.classList.toggle('mobile-active', !isLoginState);
                return;
            }

            loginFormPanel.classList.remove('mobile-active');
            registerFormPanel.classList.remove('mobile-active');
        }

        function toggleForms() {
            authContainer.classList.toggle('login-state');
            authContainer.classList.toggle('register-state');
            applyResponsiveFormState();
        }

        applyResponsiveFormState();
        window.addEventListener('resize', applyResponsiveFormState);

        showRegisterForm.addEventListener('click', function (e) {
            e.preventDefault();
            toggleForms();
        });

        showLoginForm.addEventListener('click', function (e) {
            e.preventDefault();
            toggleForms();
        });

        loginWelcomeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleForms();
        });

        registerWelcomeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleForms();
        });

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        document.getElementById('toggleRegisterPassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('registerPassword');
            const icon = this;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Modal handlers
        const passwordModal = document.getElementById('passwordModal');
        const tokenModal = document.getElementById('tokenModal');

        // Add event listener only if the element exists
        const forgotPasswordLink = document.getElementById('forgotPassword');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function (e) {
                e.preventDefault();
                passwordModal.classList.add('show');
            });
        }

        document.getElementById('closeModal').addEventListener('click', function () {
            passwordModal.classList.remove('show');
        });

        document.getElementById('closeTokenModal').addEventListener('click', function () {
            tokenModal.classList.remove('show');
        });

        // Close modals when clicking outside
        [passwordModal, tokenModal].forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            const errorMessage = document.getElementById('loginErrorMessage');
            const buttonText = document.getElementById('loginButtonText');
            const spinner = document.getElementById('loginLoadingSpinner');

            // Show loading state
            buttonText.textContent = 'VERIFICANDO...';
            spinner.classList.remove('hidden');
            errorMessage.classList.add('hidden');

            // Send to server
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
                        spinner.classList.add('hidden');
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'Error de conexión con el servidor';
                    errorMessage.classList.remove('hidden');
                    buttonText.textContent = 'INGRESAR';
                    spinner.classList.add('hidden');
                });
        });

        // Register form submission
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('registerUsername').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const phone = document.getElementById('registerPhone').value;
            const ficha = document.getElementById('registerFicha').value;
            const errorMessage = document.getElementById('registerErrorMessage');
            const successMessage = document.getElementById('registerSuccessMessage');
            const buttonText = document.getElementById('registerButtonText');
            const spinner = document.getElementById('registerLoadingSpinner');

            // Validations
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

            // Validate ficha
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

            // Show loading state
            buttonText.textContent = 'REGISTRANDO...';
            spinner.classList.remove('hidden');
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            // Send to server
            fetch('includes/register_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    phone: phone,
                    role: 'aprendiz',
                    ficha: ficha
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = '¡Registro exitoso! Redirigiendo...';
                        successMessage.classList.remove('hidden');
                        errorMessage.classList.add('hidden');

                        // Redirect after 2 seconds
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
                    spinner.classList.add('hidden');
                });
        });

        // Recovery form submission
        document.getElementById('recoveryForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('recoveryUsername').value;
            const phone = document.getElementById('recoveryPhone').value;
            const recoveryMessage = document.getElementById('recoveryMessage');
            const recoveryButtonText = document.getElementById('recoveryButtonText');
            const recoverySpinner = document.getElementById('recoveryLoadingSpinner');

            // Basic phone number validation
            const phoneRegex = /^\+[0-9]{11,15}$/;
            if (!phoneRegex.test(phone)) {
                recoveryMessage.textContent = 'Por favor, ingrese un número de WhatsApp válido con formato internacional (+573001234567)';
                recoveryMessage.classList.remove('hidden', 'success-message');
                recoveryMessage.classList.add('error-message');
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
                    recoveryMessage.classList.remove('hidden', 'error-message');
                    recoveryMessage.classList.add('success-message');

                    // Close recovery modal and show token modal
                    setTimeout(() => {
                        passwordModal.classList.remove('show');
                        tokenModal.classList.add('show');
                        document.getElementById('recoveryForm').reset();
                    }, 2000);

                } else {
                    recoveryMessage.textContent = data.message;
                    recoveryMessage.classList.remove('hidden', 'success-message');
                    recoveryMessage.classList.add('error-message');
                }
            } catch (error) {
                recoveryMessage.textContent = 'Error al procesar la solicitud';
                recoveryMessage.classList.remove('hidden', 'success-message');
                recoveryMessage.classList.add('error-message');
            } finally {
                recoveryButtonText.textContent = 'Enviar enlace de recuperación';
                recoverySpinner.classList.add('hidden');
            }
        });

        // Token form submission
        document.getElementById('tokenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const token = document.getElementById('tokenInput').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmNewPassword').value;
            const tokenMessage = document.getElementById('tokenMessage');

            // Validate passwords match
            if (newPassword !== confirmPassword) {
                tokenMessage.textContent = 'Las contraseñas no coinciden';
                tokenMessage.classList.remove('hidden', 'success-message');
                tokenMessage.classList.add('error-message');
                return;
            }

            // Validate password length
            if (newPassword.length < 8) {
                tokenMessage.textContent = 'La contraseña debe tener al menos 8 caracteres';
                tokenMessage.classList.remove('hidden', 'success-message');
                tokenMessage.classList.add('error-message');
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
                        tokenMessage.classList.remove('hidden', 'error-message');
                        tokenMessage.classList.add('success-message');

                        // Close modal after 3 seconds
                        setTimeout(() => {
                            tokenModal.classList.remove('show');
                            document.getElementById('tokenForm').reset();
                            // Redirect to login page
                            window.location.href = 'index.php';
                        }, 3000);
                    } else {
                        tokenMessage.textContent = data.message;
                        tokenMessage.classList.remove('hidden', 'success-message');
                        tokenMessage.classList.add('error-message');
                    }
                })
                .catch(error => {
                    tokenMessage.textContent = 'Error al procesar la solicitud';
                    tokenMessage.classList.remove('hidden', 'success-message');
                    tokenMessage.classList.add('error-message');
                    console.error('Error:', error);
                });
        });

        // Remember user functionality
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('rememberUser') === 'true') {
                document.getElementById('username').value = localStorage.getItem('savedUsername') || '';
                document.getElementById('remember').checked = true;
            }
        });

        document.getElementById('remember').addEventListener('change', function () {
            if (this.checked) {
                localStorage.setItem('rememberUser', 'true');
                localStorage.setItem('savedUsername', document.getElementById('username').value);
            } else {
                localStorage.removeItem('rememberUser');
                localStorage.removeItem('savedUsername');
            }
        });

        // Check URL parameters for token modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('showTokenModal')) {
            tokenModal.classList.add('show');
        }

        // Error handling for inactive users
        if (urlParams.has('error') && urlParams.get('error') === 'inactive') {
            const errorMessage = document.getElementById('loginErrorMessage');
            errorMessage.textContent = 'Su cuenta ha sido desactivada. Contacte al administrador.';
            errorMessage.classList.remove('hidden');
        }
    </script>
    <footer class="bg-[#2D3A36] text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p>© <?= date('Y') ?> SENA - Sistema de Gestión de Inventarios. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>
