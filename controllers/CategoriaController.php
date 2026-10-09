<?php
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Categoria();
    }

    // Listar categorías para vistas
    public function listar()
    {
        return $this->modelo->obtenerTodas();
    }

    // Guardar (Crear o Editar)
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_categoria = isset($_POST['id_categoria']) ? trim($_POST['id_categoria']) : '';
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);

            if (empty($nombre)) {
                header("Location: index.php?vista=admin_categorias&error=nombre_vacio");
                exit();
            }

            if (!empty($id_categoria)) {
                // Actualizar
                $exito = $this->modelo->actualizar($id_categoria, $nombre, $descripcion);
                $accion = 'actualizado';
            } else {
                // Crear
                $exito = $this->modelo->crear($nombre, $descripcion);
                $accion = 'creado';
            }

            if ($exito) {
                header("Location: index.php?vista=admin_categorias&exito=$accion");
            } else {
                header("Location: index.php?vista=admin_categorias&error=db_fallo");
            }
            exit();
        }
    }

    // Eliminar
    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $id_categoria = intval($_GET['id']);
            $exito = $this->modelo->eliminar($id_categoria);

            if ($exito) {
                header("Location: index.php?vista=admin_categorias&exito=eliminado");
            } else {
                header("Location: index.php?vista=admin_categorias&error=no_eliminado");
            }
            exit();
        }
    }
}

// Enrutador interno del controlador si se invoca directo por URL
if (isset($_GET['action'])) {
    $controller = new CategoriaController();
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}