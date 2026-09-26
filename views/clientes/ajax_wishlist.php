<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']) || isset($_SESSION['id_usuario']);

if (!$isLoggedIn) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'] ?? $_SESSION['id_usuario'] ?? $_SESSION['usuario'] ?? 1;
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'] ?? 1;
}

$id_producto = intval($_POST['id_producto'] ?? 0);

if ($id_producto <= 0) {
    echo json_encode(['success' => false, 'message' => 'Producto inválido']);
    exit();
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Wishlist.php';

try {
    $pdo = Database::conectar();

    $stmtCheck = $pdo->prepare("SELECT id_wishlist FROM wishlist WHERE id_usuario = ? AND id_producto = ?");
    $stmtCheck->execute([$id_usuario, $id_producto]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $eliminado = Wishlist::eliminar($existing['id_wishlist'], $id_usuario);
        echo json_encode([
            'success' => true,
            'status' => 'removed',
            'message' => 'Producto eliminado de tu lista de deseos.'
        ]);
    } else {
        $agregado = Wishlist::agregar($id_usuario, $id_producto);
        echo json_encode([
            'success' => true,
            'status' => 'added',
            'message' => '¡Producto añadido a la lista de deseos!'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}