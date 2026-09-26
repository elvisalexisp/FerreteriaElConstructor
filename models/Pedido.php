<?php
require_once __DIR__ . '/Database.php';

class Pedido
{
    public static function crearPedido($id_usuario, $total, $direccion_envio, $carritoItems)
    {
        $pdo = Database::conectar();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, total, direccion_envio, estado, fecha) VALUES (?, ?, ?, 'Pendiente', NOW())");
            $stmt->execute([$id_usuario, $total, $direccion_envio]);
            $id_pedido = $pdo->lastInsertId();

            $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, ?, ?)");

            foreach ($carritoItems as $item) {
                $stmtDetalle->execute([
                    $id_pedido,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio']
                ]);
            }

            $pdo->commit();
            return $id_pedido;

        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public static function obtenerPorUsuario($id_usuario)
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>