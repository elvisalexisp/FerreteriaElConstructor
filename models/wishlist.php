<?php
require_once __DIR__ . '/../config/conexion.php';

class Wishlist
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ? $db : Conexion::conectar();
    }

    // Obtener todos los productos en la wishlist de un usuario específico
    public function obtenerPorUsuario($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT w.id_wishlist, p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen 
                                    FROM wishlist w 
                                    INNER JOIN productos p ON w.id_producto = p.id_producto 
                                    WHERE w.id_usuario = ? 
                                    ORDER BY w.id_wishlist DESC");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar un producto a la wishlist (evitando duplicados)
    public function agregar($id_usuario, $id_producto)
    {
        // Verificar si ya existe en la wishlist
        $stmtCheck = $this->db->prepare("SELECT id_wishlist FROM wishlist WHERE id_usuario = ? AND id_producto = ?");
        $stmtCheck->execute([$id_usuario, $id_producto]);

        if ($stmtCheck->rowCount() === 0) {
            $stmt = $this->db->prepare("INSERT INTO wishlist (id_usuario, id_producto) VALUES (?, ?)");
            return $stmt->execute([$id_usuario, $id_producto]);
        }
        return true; // Ya estaba agregado
    }

    // Eliminar un producto de la wishlist
    public function eliminar($id_wishlist, $id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE id_wishlist = ? AND id_usuario = ?");
        return $stmt->execute([$id_wishlist, $id_usuario]);
    }
}
?>