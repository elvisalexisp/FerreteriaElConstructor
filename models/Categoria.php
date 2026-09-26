<?php
require_once __DIR__ . '/Database.php';

class Categoria
{
    public static function obtenerTodas()
    {
        $pdo = Database::conectar();
        $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id_categoria)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id_categoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>