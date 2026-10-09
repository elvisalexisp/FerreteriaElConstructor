<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Producto.php';

class Carrito
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    // Obtener contenido actual del carrito con detalles de productos
    public function obtenerContenido()
    {
        $items = [];
        $total = 0;
        $productoModel = new Producto(Conexion::conectar());

        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $producto = $productoModel->obtenerPorId($id_producto);
            if ($producto) {
                // Nos aseguramos de manejar precios y cantidades de forma numérica segura
                $precio = floatval($producto['precio']);
                $cantidad = intval($cantidad);
                $subtotal = $precio * $cantidad;

                $total += $subtotal;

                $items[] = [
                    'id_producto' => $producto['id_producto'],
                    'nombre' => $producto['nombre'],
                    'precio' => $precio,
                    'imagen' => $producto['imagen'] ?? 'default.png',
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal
                ];
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'cantidad_total' => array_sum($_SESSION['carrito'])
        ];
    }

    // Agregar producto
    public function agregar($id_producto, $cantidad = 1)
    {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);

        if ($id_producto <= 0)
            return false;
        if ($cantidad <= 0)
            $cantidad = 1;

        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_producto] = $cantidad;
        }
        return true;
    }

    // Actualizar cantidad exacta
    public function actualizarCantidad($id_producto, $cantidad)
    {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);

        if ($id_producto <= 0)
            return false;

        if ($cantidad <= 0) {
            $this->eliminar($id_producto);
        } else {
            $_SESSION['carrito'][$id_producto] = $cantidad;
        }
        return true;
    }

    // Eliminar producto individual
    public function eliminar($id_producto)
    {
        $id_producto = intval($id_producto);
        if (isset($_SESSION['carrito'][$id_producto])) {
            unset($_SESSION['carrito'][$id_producto]);
        }
        return true;
    }

    // Vaciar carrito
    public function vaciar()
    {
        $_SESSION['carrito'] = [];
        return true;
    }

    // Validar stock disponible de los productos en la sesión antes de confirmar pedido
    public function validarStockDisponible($conexion)
    {
        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            return "El carrito está vacío.";
        }

        $productoModel = new Producto($conexion);

        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $producto = $productoModel->obtenerPorId($id_producto);

            if (!$producto) {
                return "El producto con ID {$id_producto} ya no se encuentra disponible.";
            }

            $stockActual = intval($producto['cantidad'] ?? 0);
            if (intval($cantidad) > $stockActual) {
                return "Lo sentimos, stock insuficiente para '{$producto['nombre']}'. Disponible: {$stockActual}, solicitado: {$cantidad}.";
            }
        }

        return true;
    }
}
?>