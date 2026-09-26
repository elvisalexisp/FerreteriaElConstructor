<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/conexion.php';
$pdo = Conexion::conectar();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
                $stmt->execute([$_GET['id']]);
                $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($categoria) {
                    echo json_encode($categoria);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Categoría no encontrada"]);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM categorias");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'POST':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['nombre'])) {
                http_response_code(400);
                echo json_encode(["error" => "El nombre de la categoría es obligatorio"]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'] ?? null
            ]);

            http_response_code(201);
            echo json_encode([
                "mensaje" => "Categoría registrada correctamente",
                "id_categoria" => $pdo->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id_categoria']) && !isset($data['id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID de la categoría para actualizar"]);
                break;
            }

            $id = $data['id_categoria'] ?? $data['id'];

            $stmt = $pdo->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'] ?? null,
                $id
            ]);

            echo json_encode(["mensaje" => "Categoría actualizada correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id_categoria'] ?? $data['id'] ?? $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID para eliminar la categoría"]);
                break;
            }

            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            $stmt->execute([$id]);

            echo json_encode(["mensaje" => "Categoría eliminada correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>