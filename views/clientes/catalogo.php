<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Categoria.php';
require_once __DIR__ . '/../../models/wishlist.php';
require_once __DIR__ . '/../../models/Carrito.php';

$productoModel = new Producto();
$categoriaModel = new Categoria();

$categorias = $categoriaModel->obtenerTodas();

$busqueda = trim($_GET['busqueda'] ?? '');
$id_categoria = intval($_GET['categoria'] ?? 0);
$orden_precio = trim($_GET['orden'] ?? '');
$solo_stock = isset($_GET['solo_stock']) ? true : false;

// Capturar el ID del producto a resaltar proveniente de la wishlist
$highlight_id = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;

// Calcular precios mínimos y máximos globales reales de la base de datos
$todosLosProds = $productoModel->obtenerTodos();
$precioGlobalMin = 0;
$precioGlobalMax = 1000;

if (!empty($todosLosProds)) {
    $precios = array_column($todosLosProds, 'precio');
    $precioGlobalMin = floor(min($precios));
    $precioGlobalMax = ceil(max($precios));
    if ($precioGlobalMin == $precioGlobalMax) {
        $precioGlobalMax = $precioGlobalMin + 100;
    }
}

$precio_min = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? floatval($_GET['precio_min']) : $precioGlobalMin;
$precio_max = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? floatval($_GET['precio_max']) : $precioGlobalMax;

if (!empty($busqueda)) {
    $productos = $productoModel->buscar($busqueda);
} elseif ($id_categoria > 0) {
    $productos = $productoModel->obtenerPorCategoria($id_categoria);
} else {
    $productos = $todosLosProds;
}

// Filtrar por precio y stock
if (!empty($productos)) {
    $productos = array_filter($productos, function ($p) use ($precio_min, $precio_max, $solo_stock) {
        if (floatval($p['precio']) < $precio_min || floatval($p['precio']) > $precio_max) {
            return false;
        }
        if ($solo_stock && intval($p['cantidad']) <= 0) {
            return false;
        }
        return true;
    });
}

// Ordenar por precio si se solicita
if (!empty($productos)) {
    usort($productos, function ($a, $b) use ($orden_precio) {
        if ($orden_precio === 'asc') {
            return floatval($a['precio']) <=> floatval($b['precio']);
        } elseif ($orden_precio === 'desc') {
            return floatval($b['precio']) <=> floatval($a['precio']);
        }
        return 0;
    });
}

$wishlistIds = [];
if (isset($_SESSION['id_usuario'])) {
    $wishlistModel = new Wishlist();
    $itemsWish = $wishlistModel->obtenerPorUsuario($_SESSION['id_usuario']);
    foreach ($itemsWish as $w) {
        $wishlistIds[] = $w['id_producto'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/catalogo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include_once __DIR__ . '/../layouts/header_cliente.php'; ?>

    <main class="catalogo-main">
        <div id="toast-container" class="toast-container"></div>

        <div class="catalogo-layout-principal">
            <aside class="sidebar-filtros">
                <h3><i class="fas fa-filter"></i> Filtrar Productos</h3>

                <!-- Formulario de Búsqueda y Filtros unificados -->
                <form action="index.php" method="GET">
                    <input type="hidden" name="vista" value="catalogo">

                    <!-- Campo de búsqueda que retiene el valor -->
                    <div class="filtro-grupo">
                        <label for="busqueda">Buscar Producto</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="busqueda" id="busqueda"
                                value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Ej. Taladro, Cemento..."
                                style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
                            <button type="submit"
                                style="background: #f59e0b; border: none; padding: 0 12px; border-radius: 4px; cursor: pointer; color: #000;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="filtro-grupo">
                        <label for="categoria">Categoría</label>
                        <select name="categoria" id="categoria">
                            <option value="0">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $id_categoria == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Ordenar por precio -->
                    <div class="filtro-grupo">
                        <label for="orden">Ordenar Precio</label>
                        <select name="orden" id="orden">
                            <option value="">Por defecto</option>
                            <option value="asc" <?php echo $orden_precio === 'asc' ? 'selected' : ''; ?>>Menor a Mayor
                                Precio</option>
                            <option value="desc" <?php echo $orden_precio === 'desc' ? 'selected' : ''; ?>>Mayor a Menor
                                Precio</option>
                        </select>
                    </div>

                    <!-- Filtro adicional: Solo en stock -->
                    <div class="filtro-grupo checkbox-grupo">
                        <label>
                            <input type="checkbox" name="solo_stock" value="1" <?php echo $solo_stock ? 'checked' : ''; ?>>
                            Solo productos disponibles
                        </label>
                    </div>

                    <!-- Filtro por Rango de Precio con Slider Interactivo -->
                    <div class="filtro-grupo">
                        <label>Rango de Precio (Q)</label>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px;">
                            <span id="label-precio-min">Q <?php echo number_format($precio_min, 2); ?></span>
                            <span id="label-precio-max">Q <?php echo number_format($precio_max, 2); ?></span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <input type="range" id="slider-precio-min" min="<?php echo $precioGlobalMin; ?>"
                                max="<?php echo $precioGlobalMax; ?>" step="1" value="<?php echo $precio_min; ?>"
                                oninput="actualizarSliderPrecio()">
                            <input type="range" id="slider-precio-max" min="<?php echo $precioGlobalMin; ?>"
                                max="<?php echo $precioGlobalMax; ?>" step="1" value="<?php echo $precio_max; ?>"
                                oninput="actualizarSliderPrecio()">
                        </div>
                        <input type="hidden" name="precio_min" id="input-precio-min" value="<?php echo $precio_min; ?>">
                        <input type="hidden" name="precio_max" id="input-precio-max" value="<?php echo $precio_max; ?>">
                    </div>

                    <button type="submit" class="btn-aplicar-filtros">Aplicar Filtros</button>
                    <a href="index.php?vista=catalogo" class="btn-limpiar-filtros">Limpiar todos los filtros</a>
                </form>
            </aside>

            <section class="catalogo-grid-container">

                <!-- Alerta visual si hay una búsqueda activa con opción de limpiar -->
                <?php if (!empty($busqueda)): ?>
                    <div class="busqueda-activa-banner">
                        <span><i class="fas fa-search"></i> Resultados de búsqueda para:
                            <strong>"<?php echo htmlspecialchars($busqueda); ?>"</strong></span>
                        <a href="index.php?vista=catalogo" class="btn-limpiar-busqueda"><i class="fas fa-times"></i> Limpiar
                            búsqueda</a>
                    </div>
                <?php endif; ?>

                <?php if (empty($productos)): ?>
                    <div class="estado-vacio">
                        <i class="fas fa-box-open"></i>
                        <h3>No se encontraron productos</h3>
                        <p>Intenta con otra búsqueda o ajusta los filtros seleccionados.</p>
                        <a href="index.php?vista=catalogo" class="btn-primario">Ver todo el catálogo</a>
                    </div>
                <?php else: ?>
                    <div class="grid-productos">
                        <?php foreach ($productos as $p):
                            $enWishlist = in_array($p['id_producto'], $wishlistIds);
                            $productoJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                            $imagenSrc = !empty($p['imagen']) ? $directorio_raiz . 'assets/img/productos/' . htmlspecialchars($p['imagen']) : $directorio_raiz . 'assets/img/productos/default.png';

                            $esResaltado = ($highlight_id === intval($p['id_producto']));
                            $claseResaltado = $esResaltado ? ' highlight-product' : '';
                            ?>
                            <div class="card-producto<?php echo $claseResaltado; ?>"
                                id="producto-<?php echo $p['id_producto']; ?>" data-id="<?php echo $p['id_producto']; ?>">
                                <div class="card-header-acciones">
                                    <button type="button" class="btn-wishlist-toggle <?php echo $enWishlist ? 'activo' : ''; ?>"
                                        onclick="toggleWishlist(<?php echo $p['id_producto']; ?>, this)"
                                        title="Agregar a Lista de Deseos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>

                                <div class="card-imagen">
                                    <img src="<?php echo $imagenSrc; ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                                </div>

                                <div class="card-cuerpo">
                                    <span class="producto-stock <?php echo $p['cantidad'] > 0 ? 'en-stock' : 'agotado'; ?>">
                                        <?php echo $p['cantidad'] > 0 ? 'Disponibles: ' . $p['cantidad'] : 'Agotado'; ?>
                                    </span>
                                    <h3>
                                        <?php echo htmlspecialchars($p['nombre']); ?>
                                    </h3>
                                    <p class="precio">Q
                                        <?php echo number_format($p['precio'], 2); ?>
                                    </p>
                                    <p class="descripcion-corta">
                                        <?php echo htmlspecialchars(substr($p['descripcion'], 0, 70)); ?>...
                                    </p>
                                </div>

                                <div class="card-footer-acciones">
                                    <button type="button" class="btn-secundario"
                                        onclick='abrirDetalleModal(<?php echo $productoJson; ?>)'>
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                    <button type="button" class="btn-primario"
                                        onclick="agregarAlCarrito(<?php echo $p['id_producto']; ?>, 1, this)" <?php echo $p['cantidad'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-shopping-cart"></i> Comprar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <!-- Modales de detalle e imagen -->
    <div id="modal-detalle" class="modal-overlay" style="display: none;">
        <div class="modal-contenido">
            <button type="button" class="modal-cerrar" onclick="cerrarDetalleModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-grid">
                <div class="modal-imagen-container" onclick="abrirImagenCompleta()"
                    title="Haz clic para ver la imagen en grande">
                    <img id="modal-img" src="" alt="Imagen Ampliada">
                    <span class="zoom-hint"><i class="fas fa-search-plus"></i> Clic para ampliar</span>
                </div>
                <div class="modal-info">
                    <h2 id="modal-nombre"></h2>
                    <p id="modal-precio" class="modal-precio"></p>
                    <p id="modal-descripcion" class="modal-descripcion"></p>
                    <div class="modal-stock-info">
                        <strong>Estado de Inventario: </strong> <span id="modal-stock"></span>
                    </div>
                    <div class="modal-cantidad-grupo">
                        <label for="cantidad-input">Cantidad:</label>
                        <input type="number" id="cantidad-input" value="1" min="1" max="100">
                    </div>
                    <div class="modal-acciones">
                        <button type="button" id="modal-btn-carrito" class="btn-primario">
                            <i class="fas fa-shopping-cart"></i> Agregar al Carrito
                        </button>
                        <button type="button" id="modal-btn-wishlist" class="btn-secundario">
                            <i class="fas fa-heart"></i> <span id="modal-wishlist-text">Wishlist</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-imagen-fullscreen" class="modal-overlay" style="display: none;" onclick="cerrarImagenCompleta()">
        <div class="modal-contenido">
            <button type="button" class="modal-cerrar" onclick="cerrarImagenCompleta()">
                <i class="fas fa-times"></i>
            </button>
            <div style="display: flex; justify-content: center; align-items: center; padding: 20px;">
                <img id="img-fullscreen-src" src="" alt="Imagen a tamaño completo"
                    style="max-width: 100%; max-height: 75vh; object-fit: contain;">
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        const DIRECTORIO_RAIZ = "<?php echo $directorio_raiz; ?>";
        let urlImagenActual = "";

        document.addEventListener('DOMContentLoaded', () => {
            obtenerContadorInicial();

            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight');
            if (highlightId) {
                const tarjeta = document.getElementById('producto-' + highlightId);
                if (tarjeta) {
                    tarjeta.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        tarjeta.classList.remove('highlight-product');
                        const nuevaUrl = window.location.pathname + window.location.search.replace(/&?highlight=\d+/, '').replace(/^\?&/, '?');
                        window.history.replaceState({}, document.title, nuevaUrl);
                    }, 4000);
                }
            }
        });

        function obtenerContadorInicial() {
            fetch(DIRECTORIO_RAIZ + 'api/carrito.php', { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if (data && data.status === 'success' && data.data && typeof data.data.cantidad_total !== 'undefined') {
                        actualizarTodosLosContadores(data.data.cantidad_total);
                    }
                })
                .catch(() => { });
        }

        function actualizarSliderPrecio() {
            let sliderMin = document.getElementById('slider-precio-min');
            let sliderMax = document.getElementById('slider-precio-max');
            let valMin = parseFloat(sliderMin.value);
            let valMax = parseFloat(sliderMax.value);

            if (valMin > valMax) {
                let temp = valMin;
                sliderMin.value = valMax;
                sliderMax.value = temp;
                valMin = parseFloat(sliderMin.value);
                valMax = parseFloat(sliderMax.value);
            }

            document.getElementById('label-precio-min').innerText = 'Q ' + valMin.toFixed(2);
            document.getElementById('label-precio-max').innerText = 'Q ' + valMax.toFixed(2);
            document.getElementById('input-precio-min').value = valMin;
            document.getElementById('input-precio-max').value = valMax;
        }

        function mostrarToast(mensaje, tipo = 'exito') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-notificacion ${tipo}`;
            toast.innerHTML = `<i class="fas ${tipo === 'exito' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i><span>${mensaje}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        function toggleWishlist(idProducto, btnElement = null) {
            fetch(DIRECTORIO_RAIZ + 'api/wish.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_producto=${idProducto}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const cardBtn = document.querySelector(`.card-producto[data-id="${idProducto}"] .btn-wishlist-toggle`);
                        const modalBtn = document.getElementById('modal-btn-wishlist');

                        let estaActivo = false;
                        if (cardBtn) {
                            cardBtn.classList.toggle('activo');
                            estaActivo = cardBtn.classList.contains('activo');
                        }

                        if (modalBtn && modalBtn.getAttribute('data-id') == idProducto) {
                            modalBtn.classList.toggle('activo', estaActivo);
                            const textSpan = document.getElementById('modal-wishlist-text');
                            if (textSpan) {
                                textSpan.innerText = estaActivo ? 'En Wishlist' : 'Wishlist';
                            }
                        }

                        mostrarToast(estaActivo ? 'Producto añadido a la lista de deseos' : 'Producto removido de la lista de deseos', 'exito');
                    } else {
                        mostrarToast(data.error || 'Inicia sesión para usar la lista de deseos', 'advertencia');
                    }
                })
                .catch(() => mostrarToast('Error de conexión con el servidor', 'advertencia'));
        }

        function agregarAlCarrito(idProducto, cantidad = 1, btnElement = null) {
            fetch(DIRECTORIO_RAIZ + 'api/carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_producto=${idProducto}&cantidad=${cantidad}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (btnElement) {
                            animarVueloAlCarrito(btnElement);
                            transformarBotonIrAlPago(btnElement);
                        }

                        const totalItems = data.data && typeof data.data.cantidad_total !== 'undefined' ? data.data.cantidad_total : 0;
                        actualizarTodosLosContadores(totalItems);

                        mostrarToast('¡Producto agregado al carrito con éxito!', 'exito');
                    } else {
                        mostrarToast(data.error || 'No se pudo agregar al carrito', 'advertencia');
                    }
                })
                .catch(() => mostrarToast('Error al procesar el carrito', 'advertencia'));
        }

        function transformarBotonIrAlPago(btn) {
            btn.innerHTML = `<i class="fas fa-arrow-right"></i> Ir al pago`;
            btn.className = 'btn-ir-pago';
            btn.onclick = function () {
                window.location.href = DIRECTORIO_RAIZ + 'index.php?vista=carrito';
            };
        }

        function animarVueloAlCarrito(btn) {
            const card = btn.closest('.card-producto');
            const imgCard = card ? card.querySelector('.card-imagen img') : document.getElementById('modal-img');
            const carritoIconoNav = document.querySelector('.fa-shopping-cart, .icono-carrito, [href*="vista=carrito"]');

            if (!imgCard) return;

            let destinoRect;
            let usarBotonFlotante = false;

            if (carritoIconoNav) {
                const rectNav = carritoIconoNav.getBoundingClientRect();
                if (rectNav.top < 0 || rectNav.bottom > window.innerHeight) {
                    usarBotonFlotante = true;
                } else {
                    destinoRect = rectNav;
                }
            } else {
                usarBotonFlotante = true;
            }

            let elementoDestino;
            if (usarBotonFlotante) {
                elementoDestino = obtenerOCrearCarritoFlotante();
                destinoRect = elementoDestino.getBoundingClientRect();
            }

            const rectImg = imgCard.getBoundingClientRect();

            const flyer = document.createElement('img');
            flyer.src = imgCard.src;
            flyer.className = 'flyer-carrito';
            flyer.style.top = `${rectImg.top}px`;
            flyer.style.left = `${rectImg.left}px`;
            flyer.style.width = `${rectImg.width}px`;
            flyer.style.height = `${rectImg.height}px`;
            document.body.appendChild(flyer);

            setTimeout(() => {
                flyer.style.top = `${destinoRect.top}px`;
                flyer.style.left = `${destinoRect.left}px`;
                flyer.style.width = '30px';
                flyer.style.height = '30px';
                flyer.style.opacity = '0.4';
            }, 10);

            setTimeout(() => {
                flyer.remove();
                if (carritoIconoNav && !usarBotonFlotante) {
                    carritoIconoNav.classList.add('fa-bounce');
                    setTimeout(() => carritoIconoNav.classList.remove('fa-bounce'), 800);
                }
            }, 800);
        }

        function obtenerOCrearCarritoFlotante() {
            let flotante = document.getElementById('carrito-flotante-derecha');
            if (!flotante) {
                flotante = document.createElement('div');
                flotante.id = 'carrito-flotante-derecha';
                flotante.innerHTML = `<a href="${DIRECTORIO_RAIZ}index.php?vista=carrito" title="Ir al carrito"><i class="fas fa-shopping-cart"></i><span id="contador-flotante" class="badge-flotante">0</span></a>`;
                document.body.appendChild(flotante);
            }
            return flotante;
        }

        function actualizarTodosLosContadores(total) {
            const totalNum = parseInt(total) || 0;

            const contadoresHeader = document.querySelectorAll('#cart-count, .contador-carrito, #contador-carrito, .fa-shopping-cart + span');
            contadoresHeader.forEach(el => {
                el.innerText = totalNum;
                el.style.display = totalNum > 0 ? 'inline-block' : 'none';
            });

            const flotante = obtenerOCrearCarritoFlotante();
            const contadorFlotante = flotante.querySelector('#contador-flotante');
            if (contadorFlotante) {
                contadorFlotante.innerText = totalNum;
                contadorFlotante.style.display = totalNum > 0 ? 'inline-block' : 'none';
            }
        }

        window.addEventListener('scroll', () => {
            const carritoIconoNav = document.querySelector('.fa-shopping-cart, .icono-carrito, [href*="vista=carrito"]');
            const flotante = obtenerOCrearCarritoFlotante();

            if (carritoIconoNav) {
                const rectNav = carritoIconoNav.getBoundingClientRect();
                if (rectNav.bottom < 0) {
                    flotante.style.display = 'flex';
                } else {
                    flotante.style.display = 'none';
                }
            } else {
                if (window.scrollY > 150) {
                    flotante.style.display = 'flex';
                } else {
                    flotante.style.display = 'none';
                }
            }
        });

        function abrirDetalleModal(producto) {
            document.getElementById('modal-nombre').innerText = producto.nombre;
            document.getElementById('modal-precio').innerText = `Q ${parseFloat(producto.precio).toFixed(2)}`;
            document.getElementById('modal-descripcion').innerText = producto.descripcion;

            const imgPath = producto.imagen ? producto.imagen : 'default.png';
            urlImagenActual = DIRECTORIO_RAIZ + 'assets/img/productos/' + imgPath;
            document.getElementById('modal-img').src = urlImagenActual;

            const stockSpan = document.getElementById('modal-stock');
            stockSpan.innerText = producto.cantidad > 0 ? `${producto.cantidad} unidades disponibles` : 'Agotado';
            stockSpan.className = producto.cantidad > 0 ? 'texto-en-stock' : 'texto-agotado';

            document.getElementById('modal-btn-carrito').onclick = function () {
                const cant = parseInt(document.getElementById('cantidad-input').value) || 1;
                agregarAlCarrito(producto.id_producto, cant, this);
                cerrarDetalleModal();
            };

            const modalBtnWishlist = document.getElementById('modal-btn-wishlist');
            modalBtnWishlist.setAttribute('data-id', producto.id_producto);

            const cardBtn = document.querySelector(`.card-producto[data-id="${producto.id_producto}"] .btn-wishlist-toggle`);
            const enWish = cardBtn ? cardBtn.classList.contains('activo') : false;

            modalBtnWishlist.classList.toggle('activo', enWish);
            const textSpan = document.getElementById('modal-wishlist-text');
            if (textSpan) {
                textSpan.innerText = enWish ? 'En Wishlist' : 'Wishlist';
            }

            modalBtnWishlist.onclick = function () {
                toggleWishlist(producto.id_producto);
            };

            document.getElementById('modal-detalle').style.display = 'flex';
        }

        function cerrarDetalleModal() {
            document.getElementById('modal-detalle').style.display = 'none';
        }

        function abrirImagenCompleta() {
            if (urlImagenActual) {
                document.getElementById('img-fullscreen-src').src = urlImagenActual;
                document.getElementById('modal-imagen-fullscreen').style.display = 'flex';
            }
        }

        function cerrarImagenCompleta() {
            document.getElementById('modal-imagen-fullscreen').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target === document.getElementById('modal-detalle')) cerrarDetalleModal();
            if (event.target === document.getElementById('modal-imagen-fullscreen')) cerrarImagenCompleta();
        }
    </script>
</body>

</html>