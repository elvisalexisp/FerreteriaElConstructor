<?php
/**
 * API Productos - Ferretería El Constructor
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

$controller = new ProductoController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Si piden un ID específico o la lista completa
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            require_once __DIR__ . '/../models/Producto.php';
            $model = new Producto();
            $resultado = $model->obtenerPorId($id);
            echo json_encode($resultado);
        } else {
            $resultado = $controller->listar();
            echo json_encode($resultado);
        }
        break;

    case 'POST':
        // Maneja la creación o actualización desde la vista
        $controller->guardar();
        break;

    case 'DELETE':
        // Eliminar producto vía API / AJAX
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            require_once __DIR__ . '/../models/Producto.php';
            $model = new Producto();
            $eliminado = $model->eliminar($id);
            echo json_encode(['success' => $eliminado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>