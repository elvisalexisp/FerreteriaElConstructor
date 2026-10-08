<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Carrito.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../config/conexion.php'; // Requerido para la conexión a la BD en la validación de stock

$carritoObj = new Carrito();
$infoCarrito = $carritoObj->obtenerContenido();
$items = $infoCarrito['items'];
$totalGeneral = $infoCarrito['total'];

$mensaje_exito = '';
$error_checkout = '';

// Procesar el pedido y guardarlo en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_checkout'])) {
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');

    // Validaciones de formato rigurosas
    if (empty($direccion) || empty($telefono)) {
        $error_checkout = 'Por favor, ingresa tu dirección de entrega y un teléfono de contacto.';
    } elseif (mb_strlen($direccion) < 8) {
        $error_checkout = 'Por favor, ingresa una dirección de entrega más detallada (Zona, barrio, calle o referencias).';
    } elseif (!preg_match('/^[0-9]{8}$/', $telefono)) {
        $error_checkout = 'El número de teléfono debe contener exactamente 8 dígitos (formato válido en el país).';
    } elseif (empty($items)) {
        $error_checkout = 'Tu carrito está vacío.';
    } elseif (!isset($_SESSION['id_usuario'])) {
        $error_checkout = 'Debes iniciar sesión para confirmar tu pedido.';
    } else {
        try {
            // Validar stock disponible antes de proceder con el pedido
            $conexionDb = Conexion::conectar();
            $validacionStock = $carritoObj->validarStockDisponible($conexionDb);

            if ($validacionStock !== true) {
                $error_checkout = $validacionStock; // Muestra el mensaje específico de stock insuficiente
            } else {
                $pedidoModel = new Pedido();

                $id_usuario = $_SESSION['id_usuario'];
                $nit = 'C/F';
                $nombre_factura = 'Consumidor Final';
                $direccionEnvio = $direccion . (!empty($observaciones) ? ' - Notas: ' . $observaciones : '');

                $id_pedido = $pedidoModel->crear($id_usuario, $totalGeneral, $items, $direccionEnvio, $nit, $nombre_factura);

                if ($id_pedido) {
                    $carritoObj->vaciar();

                    $mensaje_exito = '¡Tu pedido ha sido registrado con éxito! Ya puedes visualizarlo en tu historial de pedidos.';
                    $items = [];
                    $totalGeneral = 0;
                } else {
                    $error_checkout = 'Hubo un error al registrar el pedido en el sistema. Inténtalo de nuevo.';
                }
            }
        } catch (Exception $e) {
            $error_checkout = 'Error de sistema: ' . $e->getMessage();
        }
    }
}

// 1. Incluir el header de cliente
include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<!-- Hoja de estilos específica para esta vista -->
<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/carrito.css">

<div class="carrito-container">
    <div class="carrito-header">
        <h2><i class="fa-solid fa-cart-shopping"></i> Tu Carrito de Compras</h2>
        <p>Revisa los materiales y herramientas seleccionados antes de enviar tu pedido.</p>
    </div>

    <?php if (!empty($mensaje_exito)): ?>
        <div class="alerta-exito">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Pedido Registrado!</h3>
            <p><?php echo $mensaje_exito; ?></p>
            <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo" class="btn-primary">Volver al Catálogo</a>
        </div>
    <?php elseif (empty($items)): ?>
        <div class="carrito-vacio">
            <i class="fa-solid fa-box-open"></i>
            <p>Tu carrito está vacío.</p>
            <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo" class="btn-primary">Ver Catálogo de
                Productos</a>
        </div>
    <?php else: ?>

        <?php if (!empty($error_checkout)): ?>
            <div class="alerta-error">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error_checkout; ?>
            </div>
        <?php endif; ?>

        <div class="carrito-grid">
            <!-- Listado de ítems -->
            <div class="carrito-items-list">
                <?php foreach ($items as $item): ?>
                    <div class="carrito-item-card" data-id="<?php echo $item['id_producto']; ?>">
                        <img src="<?php echo $directorio_raiz; ?>assets/img/productos/<?php echo htmlspecialchars($item['imagen']); ?>"
                            alt="Producto" onerror="this.src='<?php echo $directorio_raiz; ?>assets/img/default.png';">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                            <p class="item-precio">Q <?php echo number_format($item['precio'], 2); ?></p>
                        </div>
                        <div class="item-controls">
                            <input type="number" class="input-cantidad" value="<?php echo $item['cantidad']; ?>" min="1"
                                onchange="actualizarCantidad(<?php echo $item['id_producto']; ?>, this.value)">
                            <p class="item-subtotal">Subtotal: <strong>Q
                                    <?php echo number_format($item['subtotal'], 2); ?></strong></p>
                            <button class="btn-eliminar-item" onclick="eliminarDelCarrito(<?php echo $item['id_producto']; ?>)"
                                title="Eliminar">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumen de compra y Formulario de Checkout Integrado -->
            <div class="carrito-resumen-card">
                <h3>Resumen del Pedido</h3>
                <div class="resumen-line">
                    <span>Subtotal</span>
                    <span>Q <?php echo number_format($totalGeneral, 2); ?></span>
                </div>
                <div class="resumen-line">
                    <span>Entrega</span>
                    <span>A coordinar</span>
                </div>
                <hr>
                <div class="resumen-total">
                    <span>Total</span>
                    <span>Q <?php echo number_format($totalGeneral, 2); ?></span>
                </div>

                <!-- Formulario con restricciones para Teléfono (8 dígitos) y Dirección -->
                <form action="" method="POST" class="form-checkout-integrado">
                    <input type="hidden" name="accion_checkout" value="1">

                    <div class="campo-grupo">
                        <label for="direccion">Dirección de Entrega:</label>
                        <input type="text" id="direccion" name="direccion" required
                            placeholder="Ej. Zona 1, 3era Calle 4-50, Cobán" minlength="8"
                            value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>">
                    </div>

                    <div class="campo-grupo">
                        <label for="telefono">Teléfono de Contacto (8 dígitos):</label>
                        <input type="tel" id="telefono" name="telefono" required placeholder="Ej. 55123456" maxlength="8"
                            minlength="8" pattern="[0-9]{8}" title="Debe ingresar exactamente 8 números"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);"
                            value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                    </div>

                    <div class="campo-grupo">
                        <label for="observaciones">Notas adicionales (Opcional):</label>
                        <textarea id="observaciones" name="observaciones"
                            placeholder="Instrucciones para la entrega..."><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary btn-block">
                        Confirmar y Enviar Pedido
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const DIRECTORIO_RAIZ = "<?php echo $directorio_raiz; ?>";
    let productoAEliminar = null;

    function actualizarCantidad(idProducto, nuevaCantidad) {
        fetch(DIRECTORIO_RAIZ + 'api/carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'actualizar', id_producto: idProducto, cantidad: nuevaCantidad })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                }
            });
    }

    // Abre el modal personalizado en lugar de usar confirm()
    function eliminarDelCarrito(idProducto) {
        productoAEliminar = idProducto;
        const modal = document.getElementById('modal-confirmar-eliminar');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    // Cierra el modal sin eliminar
    function cerrarModalEliminar() {
        productoAEliminar = null;
        const modal = document.getElementById('modal-confirmar-eliminar');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Ejecuta la petición DELETE cuando el usuario confirma en el modal
    function confirmarEliminacion() {
        if (!productoAEliminar) return;

        fetch(DIRECTORIO_RAIZ + 'api/carrito.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_producto: productoAEliminar })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    cerrarModalEliminar();
                }
            })
            .catch(() => {
                cerrarModalEliminar();
            });
    }
</script>

<!-- Modal Personalizado de Confirmación para Eliminar -->
<div id="modal-confirmar-eliminar" class="modal-eliminar-overlay" style="display: none;">
    <div class="modal-eliminar-contenido">
        <div class="modal-eliminar-icono">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>¿Eliminar producto?</h3>
        <p>¿Estás seguro de que deseas quitar este artículo de tu carrito de compras?</p>
        <div class="modal-eliminar-acciones">
            <button type="button" class="btn-cancelar-modal" onclick="cerrarModalEliminar()">Cancelar</button>
            <button type="button" class="btn-confirmar-modal" onclick="confirmarEliminacion()">Sí, eliminar</button>
        </div>
    </div>
</div>

<?php
// 2. Incluir el footer al final de la vista
include_once __DIR__ . '/../layouts/footer.php';
?>