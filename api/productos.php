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
                $stmt = $pdo->prepare("SELECT p.*, c.nombre AS nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria WHERE p.id_producto = ?");
                $stmt->execute([$_GET['id']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($producto) {
                    echo json_encode($producto);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Producto no encontrado"]);
                }
            } else {
                // Devuelve todos los productos uniendo la categoría para obtener su nombre legible
                $stmt = $pdo->query("SELECT p.*, c.nombre AS nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'POST':
        try {
            // Soporta tanto FormData (con archivos/imágenes) como JSON puro
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? $_POST['cantidad'] ?? 0;
            $categoria_id = $_POST['categoria_id'] ?? null;

            // Manejo de subida de imagen opcional
            $nombreImagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/img/productos/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $nombreImagen = $newFileName;
                    }
                }
            }

            if (empty($nombre) || empty($precio)) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos obligatorios (nombre, precio)"]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, cantidad, id_categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nombre,
                $descripcion,
                $precio,
                $stock,
                $categoria_id,
                $nombreImagen
            ]);

            http_response_code(201);
            echo json_encode([
                "mensaje" => "Producto registrado correctamente",
                "id_producto" => $pdo->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'PUT':
        try {
            // Manejo para cuando se envía por FormData (con _method=PUT) o JSON
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? $_POST['cantidad'] ?? 0;
            $categoria_id = $_POST['categoria_id'] ?? null;

            if (!$id) {
                $data = json_decode(file_get_contents("php://input"), true);
                $id = $data['id'] ?? null;
                $nombre = $data['nombre'] ?? '';
                $descripcion = $data['descripcion'] ?? '';
                $precio = $data['precio'] ?? 0;
                $stock = $data['stock'] ?? $data['cantidad'] ?? 0;
                $categoria_id = $data['categoria_id'] ?? null;
            }

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID del producto para actualizar"]);
                break;
            }

            // Verificar si hay nueva imagen
            $sqlImagenUpdate = "";
            $params = [$nombre, $descripcion, $precio, $stock, $categoria_id];

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/img/productos/';
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $sqlImagenUpdate = ", imagen = ?";
                        $params[] = $newFileName;
                    }
                }
            }

            $params[] = $id;

            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?, id_categoria = ? {$sqlImagenUpdate} WHERE id_producto = ?");
            $stmt->execute($params);

            echo json_encode(["mensaje" => "Producto actualizado correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere el ID para eliminar el producto"]);
                break;
            }

            $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt->execute([$id]);

            echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
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