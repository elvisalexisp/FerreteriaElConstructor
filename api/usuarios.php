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
                $stmt = $pdo->prepare("SELECT id_usuario, nombre, apellido, correo, telefono, direccion, tipo_usuario FROM usuarios WHERE id_usuario = ?");
                $stmt->execute([$_GET['id']]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuario) {
                    echo json_encode($usuario);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Usuario no encontrado"]);
                }
            } else {
                $stmt = $pdo->query("SELECT id_usuario, nombre, apellido, correo, telefono, direccion, tipo_usuario FROM usuarios");
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

            if (!isset($data['nombre']) || !isset($data['correo']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos obligatorios (nombre, correo, password)"]);
                break;
            }

            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, correo, password, telefono, direccion, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre'],
                $data['apellido'] ?? '',
                $data['correo'],
                $passwordHash,
                $data['telefono'] ?? '',
                $data['direccion'] ?? '',
                $data['tipo_usuario'] ?? 'cliente'
            ]);

            http_response_code(201);
            echo json_encode([
                "mensaje" => "Usuario registrado correctamente",
                "id" => $pdo->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id_usuario'])) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID del usuario para actualizar"]);
                break;
            }

            if (!empty($data['password'])) {
                $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, password = ?, telefono = ?, direccion = ?, tipo_usuario = ? WHERE id_usuario = ?");
                $stmt->execute([
                    $data['nombre'],
                    $data['apellido'] ?? '',
                    $data['correo'],
                    $passwordHash,
                    $data['telefono'] ?? '',
                    $data['direccion'] ?? '',
                    $data['tipo_usuario'] ?? 'cliente',
                    $data['id_usuario']
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, telefono = ?, direccion = ?, tipo_usuario = ? WHERE id_usuario = ?");
                $stmt->execute([
                    $data['nombre'],
                    $data['apellido'] ?? '',
                    $data['correo'],
                    $data['telefono'] ?? '',
                    $data['direccion'] ?? '',
                    $data['tipo_usuario'] ?? 'cliente',
                    $data['id_usuario']
                ]);
            }

            echo json_encode(["mensaje" => "Usuario actualizado correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id_usuario'] ?? $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID para eliminar el usuario"]);
                break;
            }

            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);

            echo json_encode(["mensaje" => "Usuario eliminado correctamente"]);
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