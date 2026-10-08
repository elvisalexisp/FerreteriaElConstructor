<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/wishlist.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$wishlistModel = new Wishlist();
$method = $_SERVER['REQUEST_METHOD'];

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => '¡Hola! Inicia sesión o crea una cuenta para guardar tus favoritos y comprar tus productos.']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

switch ($method) {
    case 'GET':
        $items = $wishlistModel->obtenerPorUsuario($id_usuario);
        echo json_encode(['success' => true, 'data' => $items]);
        break;

    case 'POST':
        $id_producto = intval($_POST['id_producto'] ?? 0);
        if ($id_producto > 0) {
            $resultado = $wishlistModel->agregar($id_usuario, $id_producto);
            echo json_encode(['success' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Producto inválido']);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $putData);
        $id_wishlist = intval($_POST['id_wishlist'] ?? $putData['id_wishlist'] ?? 0);

        if ($id_wishlist > 0) {
            $resultado = $wishlistModel->eliminar($id_wishlist, $id_usuario);
            echo json_encode(['success' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID de wishlist inválido']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>