<?php
require_once __DIR__ . '/Database.php';

class Resena
{
    public static function obtenerTodas()
    {
        $pdo = Database::conectar();
        $stmt = $pdo->query("SELECT r.*, u.nombre, u.apellido FROM resenas r JOIN usuarios u ON r.id_usuario = u.id_usuario ORDER BY r.fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($id_usuario, $comentario, $calificacion)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("INSERT INTO resenas (id_usuario, comentario, calificacion, fecha) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$id_usuario, $comentario, $calificacion]);
    }
}
?>