<?php
/**
 * Controlador Producto - Ferretería El Constructor
 */
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProductoController
{
    private $model;
    private $categoriaModel;

    public function __construct()
    {
        $this->model = new Producto();
        $this->categoriaModel = new Categoria();
    }

    public function listar()
    {
        return $this->model->obtenerTodos();
    }

    public function listarCategorias()
    {
        return $this->categoriaModel->obtenerTodas();
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_producto = $_POST['id_producto'] ?? null;
            $id_categoria = $_POST['id_categoria'];
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $cantidad = intval($_POST['cantidad']);
            $estado = $_POST['estado'] ?? 'activo';

            // Manejo de imagen
            $imagenNombre = $_POST['imagen_actual'] ?? 'default.png';

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = 'prod_' . uniqid() . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/img/productos/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imagenNombre = $newFileName;
                    }
                }
            }

            if (!empty($id_producto)) {
                // Actualizar
                $this->model->actualizar($id_producto, $id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagenNombre, $estado);
            } else {
                // Crear
                $this->model->crear($id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagenNombre, $estado);
            }

            // Redirección corregida apuntando al enrutador raíz
            header('Location: index.php?vista=admin_productos&mensaje=exito');
            exit();
        }
    }

    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->model->eliminar($id);

            // Redirección corregida apuntando al enrutador raíz
            header('Location: index.php?vista=admin_productos&mensaje=eliminado');
            exit();
        }
    }
}
?>