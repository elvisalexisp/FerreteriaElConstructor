<?php
/**
 * API Carrito - Ferretería El Constructor
 */
header('Content-Type: application/json; charset=utf-8');

// Iniciar sesión si aún no está iniciada (indispensable para verificar $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401); // Código HTTP 401 Unauthorized
    echo json_encode([
        "status" => "error",
        "error" => "¡Hola! Inicia sesión o crea una cuenta para guardar tus favoritos y comprar tus productos."
    ]);
    exit();
}

require_once __DIR__ . '/../models/Carrito.php';

$carrito = new Carrito();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? ($input['action'] ?? '');

switch ($method) {
    case 'GET':
        $contenido = $carrito->obtenerContenido();
        echo json_encode(["status" => "success", "data" => $contenido]);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $id_producto = $input['id_producto'] ?? $_POST['id_producto'] ?? null;
        $cantidad = $input['cantidad'] ?? $_POST['cantidad'] ?? 1;

        if (!$id_producto) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID de producto requerido"]);
            exit();
        }

        if ($action === 'actualizar') {
            $carrito->actualizarCantidad($id_producto, $cantidad);
        } else {
            $carrito->agregar($id_producto, $cantidad);
        }

        $contenidoActualizado = $carrito->obtenerContenido();

        echo json_encode([
            "status" => "success",
            "message" => "Carrito actualizado",
            "data" => $contenidoActualizado
        ]);
        break;

    case 'DELETE':
        $id_producto = $_GET['id'] ?? ($input['id_producto'] ?? null);

        if ($action === 'vaciar') {
            $carrito->vaciar();
            echo json_encode([
                "status" => "success",
                "message" => "Carrito vaciado",
                "data" => $carrito->obtenerContenido()
            ]);
            exit();
        }

        if ($id_producto) {
            $carrito->eliminar($id_producto);
            echo json_encode([
                "status" => "success",
                "message" => "Producto eliminado",
                "data" => $carrito->obtenerContenido()
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Producto no especificado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        break;
}
?>