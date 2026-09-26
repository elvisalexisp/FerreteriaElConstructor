<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'No autorizado']));
}

$id = $_POST['id'] ?? 0;
$cantidad = intval($_POST['cantidad'] ?? 1);

if (isset($_SESSION['carrito'][$id])) {
    if ($cantidad > 0) {
        $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
    } else {
        unset($_SESSION['carrito'][$id]);
    }
}

$subtotalItem = 0;
if (isset($_SESSION['carrito'][$id])) {
    $subtotalItem = $_SESSION['carrito'][$id]['precio'] * $_SESSION['carrito'][$id]['cantidad'];
}

$totalGeneral = 0;
foreach ($_SESSION['carrito'] as $item) {
    $totalGeneral += $item['precio'] * $item['cantidad'];
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'subtotalItem' => number_format($subtotalItem, 2),
    'totalGeneral' => number_format($totalGeneral, 2),
    'carritoVacio' => empty($_SESSION['carrito'])
]);