<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']) || isset($_SESSION['id_usuario']);

$id_usuario = 0;
if ($isLoggedIn) {
    $id_usuario = $_SESSION['usuario']['id_usuario'] ?? $_SESSION['id_usuario'] ?? $_SESSION['usuario'] ?? 1;
    if (is_array($id_usuario)) {
        $id_usuario = $id_usuario['id_usuario'] ?? 1;
    }
}

$isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($isLocal) {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "https://ferreteriaelconstructor.gt.tc/";
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/wishlist.php';
require_once __DIR__ . '/../../models/Categoria.php';

$mensaje_toast = '';

$busqueda = trim($_GET['q'] ?? '');
$id_categoria = intval($_GET['categoria'] ?? 0);
$orden = $_GET['orden'] ?? 'recientes';

if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    if (!$isLoggedIn) {
        $mensaje_toast = "Debes crear una cuenta o iniciar sesión para agregar productos al carrito.";
    } else {
        $id_producto_cart = intval($_POST['id_producto'] ?? 0);
        $nombre = $_POST['nombre'] ?? 'Producto';
        $precio = floatval($_POST['precio'] ?? 0);
        $cantidad_agregar = max(1, intval($_POST['cantidad'] ?? 1));

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        if (isset($_SESSION['carrito'][$id_producto_cart])) {
            $_SESSION['carrito'][$id_producto_cart]['cantidad'] += $cantidad_agregar;
        } else {
            $_SESSION['carrito'][$id_producto_cart] = [
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => $cantidad_agregar
            ];
        }

        $mensaje_toast = "¡$cantidad_agregar x '$nombre' agregado(s) al carrito exitosamente!";
    }
}

if (isset($_POST['accion']) && $_POST['accion'] === 'wishlist') {
    if (!$isLoggedIn) {
        $mensaje_toast = "Debes crear una cuenta o iniciar sesión para agregar productos a tu lista de deseos.";
    } else {
        $id_prod_wish = intval($_POST['id_producto'] ?? 0);
        $nombre_prod = $_POST['nombre'] ?? 'Producto';

        $favoritos_actuales = Wishlist::obtenerPorUsuario($id_usuario);
        $ids_actuales = array_column($favoritos_actuales, 'id_producto');

        if (in_array($id_prod_wish, $ids_actuales)) {
            Wishlist::eliminar($id_usuario, $id_prod_wish);
            $mensaje_toast = "¡'$nombre_prod' ha sido eliminado de tu lista de deseos!";
        } else {
            Wishlist::agregar($id_usuario, $id_prod_wish);
            $mensaje_toast = "¡'$nombre_prod' ha sido agregado a tu lista de deseos exitosamente!";
        }
    }
}

$categorias = Categoria::obtenerTodas();
$pdo = Database::conectar();

$sql = "SELECT p.*, c.nombre AS nombre_categoria 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($id_categoria > 0) {
    $sql .= " AND p.id_categoria = ?";
    $params[] = $id_categoria;
}

if ($orden === 'precio_asc') {
    $sql .= " ORDER BY p.precio ASC";
} elseif ($orden === 'precio_desc') {
    $sql .= " ORDER BY p.precio DESC";
} else {
    $sql .= " ORDER BY p.id_producto DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ids_favoritos = [];
if ($isLoggedIn) {
    $favoritos_usuario = Wishlist::obtenerPorUsuario($id_usuario);
    $ids_favoritos = array_column($favoritos_usuario, 'id_producto');
}

include __DIR__ . '/../layouts/header_cliente.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/catalogo.css">

<div class="catalogo-container">
    <div class="catalogo-header">
        <div>
            <h2>🧱 Catálogo de Productos y Materiales</h2>
            <p>Explora nuestro inventario de construcción, herramientas y acabados profesionales.</p>
        </div>
        <a href="carrito.php" class="btn-ver-carrito">
            🛒 Ver Carrito
            <span class="badge-carrito">
                <?php echo isset($_SESSION['carrito']) ? array_sum(array_column($_SESSION['carrito'], 'cantidad')) : 0; ?>
            </span>
        </a>
    </div>

    <form action="catalogo.php" method="GET" class="filtros-form">
        <div class="input-busqueda-wrapper">
            <input type="text" name="q" placeholder="Buscar herramientas, cemento, tubos..."
                value="<?php echo htmlspecialchars($busqueda); ?>" class="input-busqueda">
        </div>

        <div class="selects-wrapper">
            <select name="categoria" class="select-filtro">
                <option value="0">📂 Todas las Categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($id_categoria == $cat['id_categoria']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="orden" class="select-filtro">
                <option value="recientes" <?php echo ($orden === 'recientes') ? 'selected' : ''; ?>>🕒 Más Recientes
                </option>
                <option value="precio_asc" <?php echo ($orden === 'precio_asc') ? 'selected' : ''; ?>>💵 Precio: Menor a
                    Mayor</option>
                <option value="precio_desc" <?php echo ($orden === 'precio_desc') ? 'selected' : ''; ?>>💰 Precio: Mayor
                    a Menor</option>
            </select>

            <button type="submit" class="btn-filtrar">🔍 Filtrar</button>
            <?php if (!empty($busqueda) || $id_categoria > 0 || $orden !== 'recientes'): ?>
                <a href="catalogo.php" class="btn-limpiar" title="Limpiar filtros">❌ Limpiar</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($mensaje_toast)): ?>
        <?php
        $es_error = strpos($mensaje_toast, 'Debes crear') !== false || strpos($mensaje_toast, '⚠️') !== false;
        ?>
        <div style="background: <?php echo $es_error ? '#fee2e2' : '#dcfce7'; ?>; 
                    color: <?php echo $es_error ? '#991b1b' : '#166534'; ?>; 
                    padding: 14px 20px; 
                    border-radius: 8px; 
                    margin-bottom: 20px; 
                    text-align: center; 
                    font-weight: 500; 
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    border: 1px solid <?php echo $es_error ? '#fca5a5' : '#86efac'; ?>;">
            <span style="font-size: 1.2rem;">
                <?php echo $es_error ? '⚠️' : '✅'; ?>
            </span>
            <span>
                <?php echo htmlspecialchars(str_replace(['⚠️ ', '✅ '], '', $mensaje_toast)); ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="catalogo-vacio">
            <div class="catalogo-vacio-icon">🔍</div>
            <p class="catalogo-vacio-text">No se encontraron productos con los criterios seleccionados.</p>
            <a href="catalogo.php" class="catalogo-vacio-link">Ver todos los productos</a>
        </div>
    <?php else: ?>
        <div class="catalogo-grid">
            <?php foreach ($productos as $prod): ?>
                <?php
                $es_favorito = in_array($prod['id_producto'], $ids_favoritos);
                $imagen_url = !empty($prod['imagen']) ? $base_url . 'assets/img/productos/' . htmlspecialchars($prod['imagen']) : '';
                ?>
                <div class="card-producto">
                    <form action="catalogo.php?<?php echo htmlspecialchars($_SERVER['QUERY_STRING']); ?>" method="POST"
                        style="display:inline;">
                        <input type="hidden" name="accion" value="wishlist">
                        <input type="hidden" name="id_producto" value="<?php echo $prod['id_producto']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                        <button type="submit" class="btn-wishlist <?php echo $es_favorito ? 'favorito-activo' : ''; ?>"
                            title="Agregar a Lista de Deseos">
                            <?php echo $es_favorito ? '❤️' : '🤍'; ?>
                        </button>
                    </form>

                    <div>
                        <div class="card-img-box">
                            <?php if (!empty($prod['imagen'])): ?>
                                <img src="<?php echo $imagen_url; ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                            <?php else: ?>
                                <span class="card-icon-placeholder">🛠️</span>
                            <?php endif; ?>
                        </div>

                        <span class="card-badge-categoria">
                            <?php echo htmlspecialchars($prod['nombre_categoria'] ?? 'General'); ?>
                        </span>

                        <h3 class="card-title">
                            <?php echo htmlspecialchars($prod['nombre']); ?>
                        </h3>
                        <p class="card-desc">
                            <?php echo htmlspecialchars(substr($prod['descripcion'] ?? 'Sin descripción disponible.', 0, 75)); ?>...
                        </p>
                    </div>

                    <div>
                        <div class="card-footer-info">
                            <span class="card-precio">Q
                                <?php echo number_format($prod['precio'], 2); ?>
                            </span>
                            <span class="card-stock">Stock:
                                <?php echo $prod['cantidad'] ?? 'Disponible'; ?>
                            </span>
                        </div>

                        <!-- Formulario para agregar al carrito -->
                        <form action="catalogo.php" method="POST" class="form-agregar">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="id_producto" value="<?php echo $prod['id_producto']; ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo $prod['precio']; ?>">

                            <input type="number" name="cantidad" value="1" min="1"
                                max="<?php echo max(1, $prod['cantidad'] ?? 10); ?>" class="input-cantidad">
                            <button type="submit" class="btn-add-cart">➕ Añadir</button>
                        </form>

                        <!-- Botón Ver Detalles  -->
                        <button type="button" onclick="abrirModal(
                            '<?php echo addslashes(htmlspecialchars($prod['nombre'])); ?>', 
                            '<?php echo addslashes(htmlspecialchars($prod['descripcion'] ?? 'Sin descripción detallada.')); ?>', 
                            '<?php echo number_format($prod['precio'], 2); ?>', 
                            '<?php echo $prod['cantidad'] ?? 'Disponible'; ?>',
                            '<?php echo $imagen_url; ?>',
                            '<?php echo addslashes(htmlspecialchars($prod['nombre_categoria'] ?? 'General')); ?>'
                        )" class="btn-detalles">
                            🔍 Ver más detalles
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Detalles del Producto -->
<div id="modalDetalle" class="modal-overlay">
    <div class="modal-container">
        <button type="button" onclick="cerrarModal()" class="modal-close-btn">✕</button>

        <div class="modal-grid-layout" style="display: grid; grid-template-columns: 1fr 1.2fr; min-height: 380px;">
            <!-- Contenedor de la Imagen del Producto -->
            <div class="modal-img-container"
                style="background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 30px; border-right: 1px solid #f1f5f9; position: relative;">
                <img id="modalImagen" src="" alt="Imagen del producto"
                    style="display: none; max-width: 100%; max-height: 250px; object-fit: contain; border-radius: 8px;">
                <span id="modalPlaceholderIcon" class="modal-placeholder-icon"
                    style="display: none; font-size: 4rem;">🛠️</span>
            </div>

            <!-- Contenedor de Información -->
            <div class="modal-info-container"
                style="padding: 30px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <span id="modalCategoria" class="modal-badge-cat"
                        style="display: inline-block; background: #e0f2fe; color: #0369a1; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Categoría</span>
                    <h3 id="modalTitulo" class="modal-title-modern"
                        style="font-size: 1.35rem; font-weight: 700; color: #1e293b; margin: 0 0 12px 0; line-height: 1.3;">
                    </h3>

                    <div class="modal-price-stock-row"
                        style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <span id="modalPrecio" class="modal-price-tag"
                            style="font-size: 1.5rem; font-weight: 800; color: #0d9488;"></span>
                        <span id="modalStock" class="modal-stock-badge"
                            style="background: #f0fdf4; color: #15803d; font-size: 0.85rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; border: 1px solid #dcfce7;"></span>
                    </div>

                    <div class="modal-divider-line" style="height: 1px; background: #e2e8f0; margin: 15px 0;"></div>

                    <h4 class="modal-desc-heading"
                        style="font-size: 0.9rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin: 0 0 6px 0; letter-spacing: 0.5px;">
                        Descripción del Producto</h4>
                    <p id="modalDescripcion" class="modal-desc-content"
                        style="font-size: 0.95rem; color: #475569; line-height: 1.6; margin: 0 0 20px 0; max-height: 110px; overflow-y: auto;">
                    </p>
                </div>

                <div class="modal-footer-actions">
                    <button type="button" onclick="cerrarModal()" class="modal-btn-cerrar-modern"
                        style="background: #334155; color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; width: 100%; text-align: center;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Interacción del Modal Corregidos -->
<script>
    function abrirModal(nombre,descripcion,precio,stock,imagenUrl,categoria) {
        document.getElementById('modalTitulo').innerText=nombre;
        document.getElementById('modalPrecio').innerText='Q '+precio;
        document.getElementById('modalStock').innerText='Stock: '+stock;
        document.getElementById('modalDescripcion').innerText=descripcion;
        document.getElementById('modalCategoria').innerText=categoria;

        const imgElement=document.getElementById('modalImagen');
        const iconElement=document.getElementById('modalPlaceholderIcon');

        if(imagenUrl&&imagenUrl.trim()!=='') {
            imgElement.src=imagenUrl;
            imgElement.style.display='block';
            iconElement.style.display='none';
        } else {
            imgElement.style.display='none';
            iconElement.style.display='block';
        }

        // Activamos la clase que muestra el modal según el CSS general
        const modalOverlay=document.getElementById('modalDetalle');
        modalOverlay.classList.add('active');
    }

    function cerrarModal() {
        const modalOverlay=document.getElementById('modalDetalle');
        modalOverlay.classList.remove('active');
    }

    // Permitir cerrar haciendo clic fuera de la tarjeta del modal (en el overlay oscuro)
    document.addEventListener('DOMContentLoaded',() => {
        const modalOverlay=document.getElementById('modalDetalle');
        if(modalOverlay) {
            modalOverlay.addEventListener('click',(event) => {
                if(event.target===modalOverlay) {
                    cerrarModal();
                }
            });
        }
    });

    // Cerrar también presionando la tecla ESC
    document.addEventListener('keydown',(event) => {
        if(event.key==='Escape') {
            cerrarModal();
        }
    });
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>