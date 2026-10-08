<?php
require_once __DIR__ . '/../config/conexion.php';

class Pedido
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::conectar();
    }

    // Obtener todos los pedidos con datos de usuario (Admin)
    public function obtenerTodos()
    {
        $sql = "SELECT p.*, u.nombre as cliente_nombre, u.correo FROM pedidos p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario 
                ORDER BY p.fecha DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener pedidos por cliente con sus respectivos detalles de productos
    public function obtenerPorCliente($id_usuario)
    {
        $sql = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario]);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Adjuntar los detalles de cada pedido
        foreach ($pedidos as &$pedido) {
            $sqlDetalles = "SELECT dp.*, pr.nombre as producto_nombre, pr.imagen FROM detalle_pedido dp 
                            JOIN productos pr ON dp.id_producto = pr.id_producto 
                            WHERE dp.id_pedido = ?";
            $stmtDet = $this->db->prepare($sqlDetalles);
            $stmtDet->execute([$pedido['id_pedido']]); // <-- Corregido con flecha ->
            $pedido['detalles'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pedidos;
    }

    // Obtener un pedido específico con sus detalles y datos del cliente
    public function obtenerPorId($id_pedido)
    {
        $sql = "SELECT p.*, u.nombre as cliente_nombre, u.correo, u.telefono FROM pedidos p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario 
                WHERE p.id_pedido = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_pedido]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            $sqlDetalles = "SELECT dp.*, pr.nombre as producto_nombre FROM detalle_pedido dp 
                            JOIN productos pr ON dp.id_producto = pr.id_producto 
                            WHERE dp.id_pedido = ?";
            $stmtDet = $this->db->prepare($sqlDetalles);
            $stmtDet->execute([$id_pedido]); // <-- Corregido con flecha ->
            $pedido['detalles'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pedido;
    }

    // Crear un nuevo pedido e insertar los productos en detalle_pedido usando la columna 'precio'
    public function crear($id_usuario, $total, $itemsCarrito, $direccionEnvio, $nit, $nombreFactura)
    {
        try {
            $this->db->beginTransaction();

            $sqlPedido = "INSERT INTO pedidos (id_usuario, fecha, total, direccion_envio, nit, nombre_factura, estado) VALUES (?, NOW(), ?, ?, ?, ?, 'Pendiente')";
            $stmt = $this->db->prepare($sqlPedido);
            $stmt->execute([$id_usuario, $total, $direccionEnvio, $nit, $nombreFactura]);
            $id_pedido = $this->db->lastInsertId();

            $sqlDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, ?, ?)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($itemsCarrito as $item) {
                $stmtDetalle->execute([
                    $id_pedido,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio']
                ]);
            }

            $this->db->commit();
            return $id_pedido;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Actualizar estado del pedido (Admin)
    public function actualizarEstado($id_pedido, $estado)
    {
        $sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $id_pedido]);
    }

    // Eliminar pedido y sus detalles
    public function eliminar($id_pedido)
    {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?");
            $stmt1->execute([$id_pedido]);

            $stmt2 = $this->db->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
            $stmt2->execute([$id_pedido]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>