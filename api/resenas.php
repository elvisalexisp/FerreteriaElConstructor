<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Resena.php';

$resenaModel = new Resena();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id_producto'])) {
            $id_producto = intval($_GET['id_producto']);
            echo json_encode($resenaModel->obtenerPorProducto($id_producto));
        } else {
            echo json_encode($resenaModel->obtenerTodas());
        }
        break;

    case 'POST':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit();
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_producto = intval($_POST['id_producto'] ?? 0);
        $calificacion = intval($_POST['calificacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id_producto > 0 && $calificacion >= 1 && $calificacion <= 5 && !empty($comentario)) {
            $resultado = $resenaModel->crear($id_usuario, $id_producto, $calificacion, $comentario);
            echo json_encode(['success' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos o inválidos']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>