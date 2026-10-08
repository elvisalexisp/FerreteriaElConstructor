<?php
require_once __DIR__ . '/../../models/wishlist.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir al login si no ha iniciado sesión utilizando la ruta raíz
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ' . $directorio_raiz . 'index.php?vista=login');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$wishlistModel = new Wishlist();

$productosWishlist = $wishlistModel->obtenerPorUsuario($id_usuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lista de Deseos - Ferretería El Constructor</title>
    <!-- Hoja de estilos principal y específica de la wishlist -->
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/wishlist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include_once __DIR__ . '/../layouts/header_cliente.php'; ?>

    <main class="main-container">
        <div id="toast-container" class="toast-container"></div>

        <!-- Modal de Confirmación Personalizado -->
        <div id="modal-confirmacion" class="modal-overlay">
            <div class="modal-box">
                <i class="fas fa-exclamation-circle"></i>
                <h3>¿Estás seguro?</h3>
                <p>¿Deseas quitar este producto de tu lista de deseos?</p>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancelar"
                        onclick="cerrarModalConfirmacion()">Cancelar</button>
                    <button type="button" id="btn-confirmar-accion" class="modal-btn modal-btn-confirmar">Sí,
                        quitar</button>
                </div>
            </div>
        </div>

        <section class="seccion-titulo">
            <h1>Mi Lista de Deseos</h1>
            <p>Guarda tus productos favoritos para comprarlos más adelante.</p>
        </section>

        <section class="wishlist-contenido">
            <?php if (empty($productosWishlist)): ?>
                <div class="alerta-vacia">
                    <p>Tu lista de deseos está vacía actualmente.</p>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo" class="btn-primario">Explorar
                        Catálogo</a>
                </div>
            <?php else: ?>
                <div class="wishlist-grid">
                    <?php foreach ($productosWishlist as $item): ?>
                        <div class="wishlist-card" id="wishlist-item-<?php echo $item['id_wishlist']; ?>">
                            <div class="imagen-producto">
                                <img src="<?php echo $directorio_raiz; ?>assets/img/productos/<?php echo !empty($item['imagen']) ? htmlspecialchars($item['imagen']) : 'default.png'; ?>"
                                    alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                            </div>
                            <h3>
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </h3>
                            <p class="precio">Q
                                <?php echo number_format($item['precio'], 2); ?>
                            </p>
                            <p class="descripcion">
                                <?php echo htmlspecialchars(substr($item['descripcion'], 0, 80)); ?>...
                            </p>

                            <div class="acciones-card">
                                <!-- CORREGIDO: Se usa $directorio_raiz, index.php?vista=catalogo y la variable correcta $item -->
                                <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo&highlight=<?php echo $item['id_producto']; ?>#producto-<?php echo $item['id_producto']; ?>"
                                    class="btn-ver-producto">
                                    Ver producto
                                </a>

                                <button type="button" class="btn-eliminar"
                                    onclick="confirmarEliminacion(<?php echo $item['id_wishlist']; ?>)">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        const DIRECTORIO_RAIZ = "<?php echo $directorio_raiz; ?>";
        let idItemAEliminar = null;

        function mostrarToast(mensaje, tipo = 'exito') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast-notificacion ${tipo}`;
            toast.innerHTML = `
                <i class="fas ${tipo === 'exito' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                <span>${mensaje}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        function confirmarEliminacion(idWishlist) {
            idItemAEliminar = idWishlist;
            document.getElementById('modal-confirmacion').classList.add('activo');
        }

        function cerrarModalConfirmacion() {
            idItemAEliminar = null;
            document.getElementById('modal-confirmacion').classList.remove('activo');
        }

        document.getElementById('btn-confirmar-accion').addEventListener('click', function () {
            if (!idItemAEliminar) return;

            const idWishlist = idItemAEliminar;
            cerrarModalConfirmacion();

            fetch(DIRECTORIO_RAIZ + 'api/wish.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_wishlist=${idWishlist}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const card = document.getElementById(`wishlist-item-${idWishlist}`);
                        if (card) {
                            card.style.transition = 'opacity 0.3s ease';
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.remove();
                                if (document.querySelectorAll('.wishlist-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        mostrarToast('Producto removido de la lista de deseos', 'exito');
                    } else {
                        mostrarToast(data.error || 'No se pudo quitar el producto', 'advertencia');
                    }
                })
                .catch(() => mostrarToast('Error de conexión con el servidor', 'advertencia'));
        });
    </script>

</body>

</html>