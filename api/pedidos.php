<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/conexion.php';

try {
    $pdo = Conexion::conectar();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            if (isset($_GET['id'])) {
                $id_pedido = $_GET['id'];
                $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
                $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$pedido) {
                    http_response_code(404);
                    echo json_encode(["error" => "Pedido no encontrado"]);
                    break;
                }

                // Detalles del pedido
                $stmtDetalles = $pdo->prepare("SELECT d.*, p.nombre as producto_nombre FROM detalle_pedido d LEFT JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_pedido = ?");
                $stmtDetalles->execute([$id_pedido]);
                $pedido['detalles'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($pedido);
            } else {
                // Consulta adaptada a tus columnas exactas (buscando el nombre del usuario de forma segura)
                $sql = "SELECT p.*, 
                               COALESCE(u.nombre, u.nombre_usuario, CONCAT('Usuario #', p.id_usuario)) as cliente_nombre 
                        FROM pedidos p 
                        LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                        ORDER BY p.id_pedido DESC";

                $stmt = $pdo->query($sql);
                $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($pedidos);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error_sql" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id_pedido']) || !isset($data['estado'])) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID del pedido y el nuevo estado"]);
                break;
            }

            $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id_pedido = ?");
            $stmt->execute([
                $data['estado'],
                $data['id_pedido']
            ]);

            echo json_encode(["mensaje" => "Estado del pedido actualizado correctamente"]);
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