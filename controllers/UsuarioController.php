<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{
    public function listar()
    {
        $usuarioModel = new Usuario();
        return $usuarioModel->obtenerTodos();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorCorreo($email);

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['email'] = $usuario['correo'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                if (in_array($usuario['tipo_usuario'], ['admin', 'subadmin'])) {
                    header("Location: index.php?vista=admin");
                } else {
                    header("Location: index.php?vista=home");
                }
                exit();
            } else {
                header("Location: index.php?vista=login&error=1");
                exit();
            }
        }
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $tipo_usuario = 'cliente';

            $usuarioModel = new Usuario();

            $existente = $usuarioModel->obtenerPorCorreo($email);
            if ($existente) {
                header("Location: index.php?vista=registro&error=email_existente");
                exit();
            }

            $registrado = $usuarioModel->crear($nombre, $apellido, $email, $password, $telefono, $direccion, $tipo_usuario);

            if ($registrado) {
                header("Location: index.php?vista=login&registro=exito");
                exit();
            } else {
                header("Location: index.php?vista=registro&error=1");
                exit();
            }
        }
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $tipo_usuario = trim($_POST['tipo_usuario'] ?? 'cliente');

            $usuarioModel = new Usuario();

            if (!empty($id_usuario)) {
                // Actualizar usuario existente
                $usuarioModel->actualizar($id_usuario, $nombre, $apellido, $correo, $telefono, $direccion, $tipo_usuario);
                if (!empty($password)) {
                    $usuarioModel->actualizarPassword($id_usuario, $password);
                }
            } else {
                // Crear nuevo usuario
                $usuarioModel->crear($nombre, $apellido, $correo, $password, $telefono, $direccion, $tipo_usuario);
            }

            // AQUÍ ESTABLECEMOS EL MENSAJE DE ÉXITO EN EL CONTROLADOR
            $_SESSION['mensaje_exito'] = "¡Los cambios se han guardado con éxito!";
            header("Location: index.php?vista=admin_usuarios");
            exit();
        }
    }

    public function eliminar()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuarioModel = new Usuario();
            $usuarioModel->eliminar($id);

            // AQUÍ ESTABLECEMOS EL MENSAJE DE ELIMINACIÓN EN EL CONTROLADOR
            $_SESSION['mensaje_exito'] = "¡El usuario ha sido eliminado con éxito!";
        }
        header("Location: index.php?vista=admin_usuarios");
        exit();
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: index.php?vista=login&logout=success");
        exit();
    }
}
?>