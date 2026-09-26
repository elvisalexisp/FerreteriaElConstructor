<?php
require_once __DIR__ . '/Database.php';

class Producto
{

    public static function obtenerTodos()
    {
        $pdo = Database::conectar();
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY id_producto DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id_producto)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>