<?php
require_once __DIR__ . '/Database.php';

class Wishlist
{
    public static function obtenerPorUsuario($id_usuario)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("
            SELECT w.id_wishlist, p.id_producto, p.nombre, p.precio, p.imagen, p.descripcion 
            FROM wishlist w 
            JOIN productos p ON w.id_producto = p.id_producto 
            WHERE w.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function agregar($id_usuario, $id_producto)
    {
        $pdo = Database::conectar();
        $stmtCheck = $pdo->prepare("SELECT id_wishlist FROM wishlist WHERE id_usuario = ? AND id_producto = ?");
        $stmtCheck->execute([$id_usuario, $id_producto]);
        if ($stmtCheck->rowCount() > 0) {
            return true; // Ya existe
        }

        $stmt = $pdo->prepare("INSERT INTO wishlist (id_usuario, id_producto) VALUES (?, ?)");
        return $stmt->execute([$id_usuario, $id_producto]);
    }

    public static function eliminar($id_wishlist, $id_usuario)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id_wishlist = ? AND id_usuario = ?");
        return $stmt->execute([$id_wishlist, $id_usuario]);
    }
}
?>