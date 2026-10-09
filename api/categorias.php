<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Categoria.php';

$categoriaModel = new Categoria();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = $categoriaModel->obtenerPorId($id);
            if ($data) {
                echo json_encode(["status" => "success", "data" => $data]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Categoría no encontrada"]);
            }
        } else {
            $data = $categoriaModel->obtenerTodas();
            echo json_encode(["status" => "success", "data" => $data]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $nombre = $input['nombre'] ?? '';
        $descripcion = $input['descripcion'] ?? '';

        if (!empty($nombre)) {
            $resultado = $categoriaModel->crear($nombre, $descripcion);
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Categoría creada correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El nombre es obligatorio"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        break;
}