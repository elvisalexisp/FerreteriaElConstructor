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

require_once __DIR__ . '/../../models/Pedido.php';

$usuario_sesion = $_SESSION['usuario'];
$id_usuario = is_array($usuario_sesion) ? ($usuario_sesion['id_usuario'] ?? $usuario_sesion['id'] ?? 1) : $usuario_sesion;

$pedidos = Pedido::obtenerPorUsuario($id_usuario);

include __DIR__ . '/../layouts/header_cliente.php';
?>

<div class="main-content-container" style="padding: 25px; max-width: 1000px; margin: 0 auto;">
    <h2 style="color: #1e293b; margin-bottom: 20px;">📦 Mis Pedidos Realizados</h2>

    <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
        <div
            style="background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">
            ✅ ¡Pedido realizado con éxito! Puedes consultar en la pestaña pedidos.
        </div>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
        <div class="card"
            style="padding: 40px; text-align: center; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="font-size: 1.2rem; color: #64748b; margin-bottom: 20px;">Aún no has realizado ningún pedido.</p>
            <a href="index.php" class="btn"
                style="padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">Ir
                al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="card"
            style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #64748b;">
                        <th style="padding: 12px;">N° Pedido</th>
                        <th style="padding: 12px;">Fecha</th>
                        <th style="padding: 12px;">Dirección y Facturación</th>
                        <th style="padding: 12px;">Total</th>
                        <th style="padding: 12px; text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px; font-weight: bold; color: #2563eb;">
                                #<?php echo $pedido['id_pedido'] ?? $pedido['id'] ?? 'N/D'; ?>
                            </td>
                            <td style="padding: 12px; color: #475569;">
                                <?php echo $pedido['fecha']; ?>
                            </td>
                            <td style="padding: 12px; color: #475569; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($pedido['direccion_envio']); ?>
                            </td>
                            <td style="padding: 12px; font-weight: bold; color: #0f172a;">
                                Q <?php echo number_format($pedido['total'], 2); ?>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <?php
                                $estado = $pedido['estado'] ?? 'Pendiente';
                                $bgColor = '#fef3c7';
                                $textColor = '#d97706';

                                if (strtolower($estado) === 'completado' || strtolower($estado) === 'entregado') {
                                    $bgColor = '#dcfce7';
                                    $textColor = '#16a34a';
                                } elseif (strtolower($estado) === 'cancelado') {
                                    $bgColor = '#fee2e2';
                                    $textColor = '#dc2626';
                                }
                                ?>
                                <span
                                    style="background: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                    <?php echo ucfirst($estado); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>