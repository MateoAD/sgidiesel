<?php
// /includes/check_token.php
session_start();
require 'database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

$stmt = $pdo->prepare("SELECT id, usuario FROM usuarios WHERE token_recuperacion = ? AND expiracion_token > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['usuario'];
    echo json_encode(['valid' => true]);
} else {
    echo json_encode(['valid' => false]);
}
?>