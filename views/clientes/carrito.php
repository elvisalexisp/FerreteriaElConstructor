<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($isLocal) {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "https://ferreteriaelconstructor.gt.tc/";
}

if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $id = $_GET['id'] ?? 0;
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
    header("Location: carrito.php");
    exit();
}

include __DIR__ . '/../layouts/header_cliente.php';
?>

<div class="main-content-container cart-container">
    <h2 class="cart-title">🛒 Tu Carrito de Compras</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'transaccion'): ?>
        <div
            style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">
            ⚠️ Hubo un error al procesar tu pedido. Por favor, intenta de nuevo.
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['carrito'])): ?>
        <div class="card cart-empty-card">
            <p class="cart-empty-text">Tu carrito está vacío actualmente.</p>
            <a href="index.php" class="btn btn-primary">Explorar Catálogo</a>
        </div>
    <?php else: ?>
        <div class="card cart-content-card">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalGeneral = 0;
                    foreach ($_SESSION['carrito'] as $id => $item):
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $totalGeneral += $subtotal;
                        ?>
                        <tr>
                            <td class="product-name-col">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </td>
                            <td class="product-price-col">Q <?php echo number_format($item['precio'], 2); ?></td>
                            <td class="product-qty-col">
                                <!-- Input dinámico con AJAX -->
                                <input type="number" class="qty-input actualizar-cantidad" data-id="<?php echo $id; ?>"
                                    value="<?php echo $item['cantidad']; ?>" min="1">
                            </td>
                            <td class="product-subtotal-col">Q <span
                                    id="subtotal-<?php echo $id; ?>"><?php echo number_format($subtotal, 2); ?></span></td>
                            <td class="text-center">
                                <a href="carrito.php?accion=eliminar&id=<?php echo $id; ?>" class="btn-delete">🗑️ Quitar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-footer-wrapper">
                <div class="cart-total-section">
                    <h3 class="cart-total-title">Total a Pagar: <span class="cart-total-amount">Q <span
                                id="total-general"><?php echo number_format($totalGeneral, 2); ?></span></span></h3>
                </div>

                <form action="../../controllers/PedidoController.php" method="POST" class="checkout-form">
                    <input type="hidden" name="accion" value="checkout">

                    <div class="form-group">
                        <label class="form-label">Dirección de Entrega:</label>
                        <input type="text" name="direccion_envio" required value="Cobán" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">NIT para Factura:</label>
                        <input type="text" name="nit" required placeholder="Ej. 1234567-8 o C/F" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre de Facturación:</label>
                        <input type="text" name="nombre_factura" required placeholder="Nombre o Razón Social"
                            class="form-input">
                    </div>

                    <button type="submit" class="btn btn-success">Confirmar y Realizar Pedido</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.actualizar-cantidad').forEach(input => {
        input.addEventListener('change',function() {
            const id=this.getAttribute('data-id');
            const cantidad=this.value;

            if(cantidad<=0) {
                window.location.href=`carrito.php?accion=eliminar&id=${id}`;
                return;
            }

            const formData=new URLSearchParams();
            formData.append('id',id);
            formData.append('cantidad',cantidad);

            fetch('actualizar_carrito.php',{
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById(`subtotal-${id}`).textContent=data.subtotalItem;
                        document.getElementById('total-general').textContent=data.totalGeneral;

                        if(data.carritoVacio) {
                            location.reload();
                        }
                    }
                })
                .catch(error => console.error('Error al actualizar el carrito:',error));
        });
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>