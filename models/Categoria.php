<?php
require_once __DIR__ . '/../config/conexion.php';

class Categoria
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    // Obtener todas las categorías
    public function obtenerTodas()
    {
        $sql = "SELECT id_categoria, nombre, descripcion FROM categorias ORDER BY id_categoria ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una categoría por su ID
    public function obtenerPorId($id_categoria)
    {
        $stmt = $this->db->prepare("SELECT id_categoria, nombre, descripcion FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id_categoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva categoría
    public function crear($nombre, $descripcion)
    {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        return $stmt->execute([$nombre, $descripcion]);
    }

    // Actualizar categoría
    public function actualizar($id_categoria, $nombre, $descripcion)
    {
        $stmt = $this->db->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        return $stmt->execute([$nombre, $descripcion, $id_categoria]);
    }

    // Eliminar categoría
    public function eliminar($id_categoria)
    {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        return $stmt->execute([$id_categoria]);
    }
}
?>