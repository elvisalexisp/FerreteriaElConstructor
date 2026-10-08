<?php
/**
 * Modelo Producto - Ferretería El Constructor
 */
require_once __DIR__ . '/../config/conexion.php';

class Producto
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ? $db : Conexion::conectar();
    }

    // Obtener todos los productos con su respectiva categoría
    public function obtenerTodos()
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                ORDER BY p.id_producto DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por ID
    public function obtenerPorId($id_producto)
    {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener productos filtrados por categoría (solo activos para la vista de clientes)
    public function obtenerPorCategoria($id_categoria)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.nombre AS categoria_nombre 
                                    FROM productos p 
                                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                                    WHERE p.id_categoria = ? AND p.estado = 'activo' 
                                    ORDER BY p.id_producto DESC");
        $stmt->execute([$id_categoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar productos por nombre o descripción (solo activos para la vista de clientes)
    public function buscar($termino)
    {
        $busqueda = "%" . $termino . "%";
        $stmt = $this->db->prepare("SELECT p.*, c.nombre AS categoria_nombre 
                                    FROM productos p 
                                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                                    WHERE (p.nombre LIKE ? OR p.descripcion LIKE ?) AND p.estado = 'activo' 
                                    ORDER BY p.id_producto DESC");
        $stmt->execute([$busqueda, $busqueda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo producto
    public function crear($id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagen, $estado)
    {
        $stmt = $this->db->prepare("INSERT INTO productos (id_categoria, nombre, descripcion, precio, cantidad, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagen, $estado]);
    }

    // Actualizar producto
    public function actualizar($id_producto, $id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagen, $estado)
    {
        $stmt = $this->db->prepare("UPDATE productos SET id_categoria = ?, nombre = ?, descripcion = ?, precio = ?, cantidad = ?, imagen = ?, estado = ? WHERE id_producto = ?");
        return $stmt->execute([$id_categoria, $nombre, $descripcion, $precio, $cantidad, $imagen, $estado, $id_producto]);
    }

    // Eliminar producto
    public function eliminar($id_producto)
    {
        $stmt = $this->db->prepare("DELETE FROM productos WHERE id_producto = ?");
        return $stmt->execute([$id_producto]);
    }
}
?>