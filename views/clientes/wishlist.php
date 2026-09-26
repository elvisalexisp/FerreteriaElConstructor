<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']) || isset($_SESSION['id_usuario']);

if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'] ?? $_SESSION['id_usuario'] ?? $_SESSION['usuario'] ?? 1;
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'] ?? 1;
}

$isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($isLocal) {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "https://ferreteriaelconstructor.gt.tc/";
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/wishlist.php';

$mensaje_alerta = '';
$tipo_alerta = '';

if (isset($_REQUEST['accion']) && $_REQUEST['accion'] === 'eliminar') {
    $id_wishlist = intval($_REQUEST['id_wishlist'] ?? $_GET['id'] ?? 0);
    if (Wishlist::eliminar($id_wishlist, $id_usuario)) {
        header("Location: wishlist.php?eliminado=1");
        exit();
    } else {
        header("Location: wishlist.php?error=eliminacion");
        exit();
    }
}

if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1) {
    $mensaje_alerta = "Producto eliminado de tu lista de deseos correctamente.";
    $tipo_alerta = "success";
}

if (isset($_GET['error']) && $_GET['error'] == 'eliminacion') {
    $mensaje_alerta = "Hubo un error al quitar el producto de tu lista. Por favor, intenta de nuevo.";
    $tipo_alerta = "error";
}

try {
    $favoritos = Wishlist::obtenerPorUsuario($id_usuario);
} catch (Exception $e) {
    $favoritos = [];
}

include __DIR__ . '/../layouts/header_cliente.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/wishlist.css">

<div class="main-content-container cart-container">
    <h2 class="cart-title">❤️ Mi Lista de Deseos</h2>

    <?php if (!empty($mensaje_alerta)): ?>
        <div style="background: <?php echo ($tipo_alerta === 'success') ? '#dcfce7' : '#fee2e2'; ?>; 
                    color: <?php echo ($tipo_alerta === 'success') ? '#166534' : '#dc2626'; ?>; 
                    padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">
            <?php echo ($tipo_alerta === 'success') ? '✅ ' : '⚠️ '; ?>
            <?php echo $mensaje_alerta; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($favoritos)): ?>
        <div class="card cart-empty-card"
            style="text-align: center; padding: 50px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <div style="font-size: 3rem; margin-bottom: 10px;">🤍</div>
            <p class="cart-empty-text" style="font-size: 1.2rem; color: #64748b; margin-bottom: 15px;">Tu lista de deseos
                está vacía actualmente.</p>
            <a href="catalogo.php" class="btn btn-primary"
                style="background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">Explorar
                Catálogo</a>
        </div>
    <?php else: ?>
        <div class="card cart-content-card">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($favoritos as $fav): ?>
                        <tr>
                            <td class="product-name-col" style="display: flex; align-items: center; gap: 12px;">
                                <div
                                    style="width: 50px; height: 50px; background: #f8fafc; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?php if (!empty($fav['imagen'])): ?>
                                        <img src="<?php echo $base_url . 'assets/img/productos/' . htmlspecialchars($fav['imagen']); ?>"
                                            alt="<?php echo htmlspecialchars($fav['nombre']); ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span>🛠️</span>
                                    <?php endif; ?>
                                </div>
                                <span style="font-weight: 600; color: #1e293b;">
                                    <?php echo htmlspecialchars($fav['nombre']); ?>
                                </span>
                            </td>
                            <td class="product-desc-col" style="color: #64748b; font-size: 0.9rem;">
                                <?php echo htmlspecialchars(substr($fav['descripcion'] ?? '', 0, 60)); ?>...
                            </td>
                            <td class="product-price-col" style="font-weight: 700; color: #0f172a;">
                                Q
                                <?php echo number_format($fav['precio'], 2); ?>
                            </td>
                            <td class="text-center"
                                style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <a href="catalogo.php" class="btn"
                                    style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    🛒 Ver
                                </a>
                                <a href="wishlist.php?accion=eliminar&id_wishlist=<?php echo $fav['id_wishlist']; ?>"
                                    class="btn-delete"
                                    style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    🗑️ Quitar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-footer-wrapper"
                style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                <a href="catalogo.php" class="btn btn-secondary"
                    style="background: #f1f5f9; color: #334155; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                    ← Seguir Explorando
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>