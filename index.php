<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

$base_url = $protocol . $host . "/FerreteriaElConstructor/";

if ($host === 'localhost' || $host === '127.0.0.1') {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "http://" . $host . "/";
}

if (isset($_SESSION['rol']) && in_array(strtolower(trim($_SESSION['rol'])), ['admin', 'administrador', '1'])) {
    header("Location: " . $base_url . "views/admin/index.php");
    exit();
}

header("Location: " . $base_url . "views/clientes/index.php");
exit();
?>