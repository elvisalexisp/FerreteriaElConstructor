<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';

class PedidoController
{
    public static function procesarCheckout()
    {
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['carrito'])) {
            $usuario_sesion = $_SESSION['usuario'];
            $id_usuario = is_array($usuario_sesion) ? ($usuario_sesion['id_usuario'] ?? $usuario_sesion['id'] ?? 1) : $usuario_sesion;

            // Recoger datos del formulario
            $dir_input = trim($_POST['direccion_envio'] ?? 'Cobán');
            $nit = trim($_POST['nit'] ?? 'C/F');
            $nombre_factura = trim($_POST['nombre_factura'] ?? 'Consumidor Final');

            $direccion_envio = "Dir: " . $dir_input . " | NIT: " . $nit . " | Factura a: " . $nombre_factura;

            $total = 0;
            $carritoItems = [];

            foreach ($_SESSION['carrito'] as $id_producto => $item) {
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;

                $carritoItems[] = [
                    'id_producto' => $id_producto,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio']
                ];
            }

            // Crear el pedido en la base de datos
            $id_pedido = Pedido::crearPedido($id_usuario, $total, $direccion_envio, $carritoItems);

            if ($id_pedido) {
                unset($_SESSION['carrito']);
                header("Location: ../views/clientes/pedidos.php?exito=1");
                exit();
            } else {
                header("Location: ../views/clientes/carrito.php?error=transaccion");
                exit();
            }
        } else {
            header("Location: ../views/clientes/carrito.php");
            exit();
        }
    }
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'checkout') {
    PedidoController::procesarCheckout();
}
?>