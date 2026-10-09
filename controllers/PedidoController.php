<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Carrito.php';

class PedidoController
{

    public function procesarCheckout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?vista=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $direccion = $_POST['direccion_envio'] ?? '';
            $nit = $_POST['nit'] ?? 'CF';
            $nombreFactura = $_POST['nombre_factura'] ?? 'Consumidor Final';
            $id_usuario = $_SESSION['usuario']['id_usuario'];

            $carritoObj = new Carrito();
            $contenido = $carritoObj->obtenerContenido();
            $items = $contenido['items'];
            $total = $contenido['total'];

            if (empty($items)) {
                header('Location: index.php?vista=carrito');
                exit();
            }

            $pedidoModel = new Pedido();
            $id_pedido = $pedidoModel->crear($id_usuario, $total, $items, $direccion, $nit, $nombreFactura);

            if ($id_pedido) {
                $carritoObj->vaciar();
                header('Location: index.php?vista=pedidos&exito=1');
                exit();
            } else {
                header('Location: index.php?vista=carrito&error=1');
                exit();
            }
        }
    }

    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pedido = $_POST['id_pedido'] ?? null;
            $estado = $_POST['estado'] ?? null;

            if ($id_pedido && $estado) {
                $pedidoModel = new Pedido();
                $pedidoModel->actualizarEstado($id_pedido, $estado);
            }
            header('Location: index.php?vista=admin_consultas');
            exit();
        }
    }

    public function eliminar()
    {
        $id_pedido = $_GET['id'] ?? null;
        if ($id_pedido) {
            $pedidoModel = new Pedido();
            $pedidoModel->eliminar($id_pedido);
        }
        header('Location: index.php?vista=admin_consultas');
        exit();
    }
}