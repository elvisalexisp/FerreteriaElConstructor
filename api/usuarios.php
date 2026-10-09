<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Usuario.php';

$usuarioModel = new Usuario();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            echo json_encode($usuarioModel->obtenerPorId($id));
        } else {
            echo json_encode($usuarioModel->obtenerTodos());
        }
        break;

    case 'POST':
        $id_usuario = $_POST['id_usuario'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $tipo_usuario = trim($_POST['tipo_usuario'] ?? 'cliente');

        if (!empty($id_usuario)) {
            // Actualizar usuario existente
            $resultado = $usuarioModel->actualizar($id_usuario, $nombre, $apellido, $correo, $telefono, $direccion, $tipo_usuario);

            // Si se proporciona una contraseña nueva, la actualizamos
            if (!empty($password)) {
                $usuarioModel->actualizarPassword($id_usuario, $password);
            }

            echo json_encode(['success' => $resultado]);
        } else {
            // Crear nuevo usuario
            if (!empty($nombre) && !empty($correo) && !empty($password)) {
                $resultado = $usuarioModel->crear($nombre, $apellido, $correo, $password, $telefono, $direccion, $tipo_usuario);
                echo json_encode(['success' => $resultado]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>