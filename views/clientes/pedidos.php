<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?vista=login');
    exit();
}

require_once __DIR__ . '/../../models/Pedido.php';
$pedidoModel = new Pedido();
$misPedidos = $pedidoModel->obtenerPorCliente($_SESSION['id_usuario']);

include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<!-- Hoja de estilos personalizada -->
<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/pedidos.css">

<div class="pedidos-cliente-container">
    <div class="pedidos-header">
        <h2>Mis Pedidos Realizados</h2>
        <p>Consulta el detalle de tus compras, el estado de entrega y comprobantes de la ferretería.</p>
    </div>

    <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
        <div class="alert-success">
            ¡<strong>Pedido Recibido con éxito!</strong> Tu requerimiento ha sido registrado en nuestro sistema. Pronto nos
            pondremos en contacto para el despacho en Cobán.
        </div>
    <?php endif; ?>

    <?php if (empty($misPedidos)): ?>
        <div class="sin-pedidos">
            <p>Aún no has registrado ningún pedido en el sistema.</p>
            <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo" class="btn-primary">Ir al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="lista-pedidos">
            <?php foreach ($misPedidos as $pedido): ?>
                <div class="card-pedido">

                    <!-- Cabecera del Pedido -->
                    <div class="pedido-info-bar">
                        <div>
                            <span class="pedido-id">Pedido # <?php echo $pedido['id_pedido']; ?></span>
                            <span class="pedido-fecha">Fecha: <?php echo $pedido['fecha']; ?></span>
                        </div>
                        <div>
                            <span class="badge-estado">
                                Estado: <?php echo htmlspecialchars($pedido['estado']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Datos Generales y Facturación -->
                    <div class="pedido-body">
                        <div class="pedido-detalles-grid">
                            <div>
                                <p><strong>Dirección de Envío:</strong><br>
                                    <?php echo htmlspecialchars($pedido['direccion_envio']); ?>
                                </p>
                            </div>
                            <div>
                                <p><strong>Datos de Facturación:</strong><br>
                                    NIT: <?php echo htmlspecialchars($pedido['nit']); ?><br>
                                    Nombre: <?php echo htmlspecialchars($pedido['nombre_factura']); ?>
                                </p>
                            </div>
                            <div>
                                <p><strong>Total a Pagar:</strong><br>
                                    <span class="pedido-total-monto">Q <?php echo number_format($pedido['total'], 2); ?></span>
                                </p>
                            </div>
                        </div>

                        <!-- Detalle de Productos Comprados (Tabla interna) -->
                        <h4 class="productos-titulo">Productos en este Pedido:</h4>
                        <div class="table-responsive">
                            <table class="tabla-productos-pedido">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Precio Unitario</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pedido['detalles'])): ?>
                                        <?php foreach ($pedido['detalles'] as $detalle): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($detalle['producto_nombre']); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $detalle['cantidad']; ?>
                                                </td>
                                                <td class="text-right">Q <?php echo number_format($detalle['precio'], 2); ?></td>
                                                <td class="text-right">Q
                                                    <?php echo number_format($detalle['cantidad'] * $detalle['precio'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No hay detalles registrados para este pedido.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pie con Información institucional de la Ferretería -->
                    <div class="pedido-footer-info">
                        <span>📍 <strong>Ferretería El Constructor</strong> - Cobán, Alta Verapaz</span>
                        <span>📞 Soporte / Consultas de Entrega: Atendiendo su solicitud de materiales</span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>