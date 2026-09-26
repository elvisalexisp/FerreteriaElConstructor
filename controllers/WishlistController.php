<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Wishlist.php';

$id_usuario = $_SESSION['usuario']['id_usuario'] ?? 1;
$id_producto = intval($_POST['id_producto'] ?? 0);

if ($id_producto > 0) {
    $resultado = Wishlist::agregar($id_usuario, $id_producto);
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Agregado a favoritos']);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Error al procesar']);