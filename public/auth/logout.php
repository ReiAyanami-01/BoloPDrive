<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php'; // BASE_URL y sesión

// Si hay usuario en sesión, registra la interacción (si tienes esta función)
if (isset($_SESSION['user']['id'])) {
    // Si no tienes log_interaccion, puedes comentar la siguiente línea
    if (function_exists('pdo')) {
        $uid = (int)$_SESSION['user']['id'];
        $pdo = pdo();
        $stmt = $pdo->prepare("INSERT INTO interacciones (tipo, usuario_id, detalle) VALUES (?,?,?)");
        $stmt->execute(['logout', $uid, json_encode([], JSON_UNESCAPED_UNICODE)]);
    }
}

// Cerrar sesión de forma segura
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();

// Redirige al login
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
