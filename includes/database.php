<?php
// conexion.php
$host = '127.0.0.1'; // O 'localhost' si estás trabajando localmente
$dbname = 'diesel';
$username = 'root'; // Usuario de tu base de datos
$password = ''; // Contraseña de tu base de datos

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]));
}
?>