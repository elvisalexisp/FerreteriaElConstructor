<?php
require_once __DIR__ . '/../../controllers/ProductoController.php';

$controller = new ProductoController();

// Mensajes de feedback para la interfaz
$mensaje = '';
$tipoMensaje = '';

// Manejo de peticiones POST (Crear o Actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller->guardar();
        // Redirigir para limpiar el POST y volver a la vista principal de administración de productos
        header("Location: index.php?vista=admin_productos&mensaje=exito");
        exit();
    } catch (Exception $e) {
        $mensaje = "Error al guardar el producto: " . $e->getMessage();
        $tipoMensaje = "error";
    }
}

// Manejo de mensajes por parámetro GET tras redirección exitosa
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito') {
    $mensaje = "¡Operación realizada exitosamente!";
    $tipoMensaje = "success";
}

if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    try {
        $controller->eliminar();
        header("Location: index.php?vista=admin_productos&mensaje=eliminado");
        exit();
    } catch (Exception $e) {
        $mensaje = "Error al eliminar el producto: " . $e->getMessage();
        $tipoMensaje = "error";
    }
}

if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'eliminado') {
    $mensaje = "¡Producto eliminado correctamente!";
    $tipoMensaje = "success";
}

// Capturar parámetros de búsqueda y filtro
$busqueda = trim($_GET['busqueda'] ?? $_GET['q'] ?? '');
$filtroCategoria = intval($_GET['cat'] ?? 0);

// Obtener listas
$productos = $controller->listar();
$categorias = $controller->listarCategorias();

// Filtrado dinámico en PHP robusto
if (!empty($productos)) {
    $productos = array_filter($productos, function ($p) use ($busqueda, $filtroCategoria) {
        if (!empty($busqueda)) {
            if (stripos($p['nombre'], $busqueda) === false) {
                return false;
            }
        }
        if ($filtroCategoria > 0) {
            $prodCat = intval($p['id_categoria'] ?? 0);
            if ($prodCat !== $filtroCategoria) {
                return false;
            }
        }
        return true;
    });
}

// Editando registro
$productoEditar = null;
if (isset($_GET['action']) && $_GET['action'] === 'editar' && isset($_GET['id'])) {
    require_once __DIR__ . '/../../models/Producto.php';
    $tempModel = new Producto();
    $productoEditar = $tempModel->obtenerPorId(intval($_GET['id']));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/adminProductos.css">
    <!-- FontAwesome para iconos modernos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include_once __DIR__ . '/../layouts/header_admin.php'; ?>

    <main class="admin-container">
        <div class="admin-header-title">
            <h1><i class="fa-solid fa-boxes-stacked"></i> Gestión de Productos</h1>
            <p>Administra el inventario, precios, stock y categorías de la ferretería.</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <i
                    class="fa-solid <?php echo $tipoMensaje === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Creación / Edición -->
        <div class="form-card">
            <h2>
                <i class="fa-solid <?php echo $productoEditar ? 'fa-pen-to-square' : 'fa-plus-circle'; ?>"></i>
                <?php echo $productoEditar ? 'Editar Producto #' . $productoEditar['id_producto'] : 'Agregar Nuevo Producto'; ?>
            </h2>

            <!-- Cambiamos la acción para que apunte correctamente mediante el enrutador principal index.php -->
            <form action="<?php echo $directorio_raiz; ?>index.php?vista=admin_productos" method="POST"
                enctype="multipart/form-data" class="producto-form">
                <input type="hidden" name="id_producto" value="<?php echo $productoEditar['id_producto'] ?? ''; ?>">
                <input type="hidden" name="imagen_actual"
                    value="<?php echo $productoEditar['imagen'] ?? 'default.png'; ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre"><i class="fa-solid fa-tag"></i> Nombre del Producto:</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej. Taladro Percutor 1/2"
                            value="<?php echo htmlspecialchars($productoEditar['nombre'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="id_categoria"><i class="fa-solid fa-folder-open"></i> Categoría:</label>
                        <select id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" <?php echo (isset($productoEditar) && $productoEditar['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion"><i class="fa-solid fa-align-left"></i> Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="2"
                        placeholder="Detalles técnicos o uso del producto..."
                        required><?php echo htmlspecialchars($productoEditar['descripcion'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="precio"><i class="fa-solid fa-money-bill-wave"></i> Precio (Q):</label>
                        <input type="number" step="0.01" id="precio" name="precio" placeholder="0.00" required
                            value="<?php echo $productoEditar['precio'] ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="cantidad"><i class="fa-solid fa-warehouse"></i> Cantidad en Stock:</label>
                        <input type="number" id="cantidad" name="cantidad" placeholder="0" required
                            value="<?php echo $productoEditar['cantidad'] ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="estado"><i class="fa-solid fa-toggle-on"></i> Estado:</label>
                        <select id="estado" name="estado">
                            <option value="activo" <?php echo (isset($productoEditar) && $productoEditar['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo (isset($productoEditar) && $productoEditar['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagen"><i class="fa-solid fa-image"></i> Imagen del Producto:</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="imagen" name="imagen" accept="image/*">
                    </div>
                    <?php if (!empty($productoEditar['imagen'])): ?>
                        <small class="text-muted"><i class="fa-solid fa-circle-info"></i> Imagen actual:
                            <?php echo htmlspecialchars($productoEditar['imagen']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-guardar">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <?php echo $productoEditar ? 'Actualizar Producto' : 'Guardar Producto'; ?>
                    </button>
                    <?php if ($productoEditar): ?>
                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_productos" class="btn-cancelar"><i
                                class="fa-solid fa-xmark"></i>
                            Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabla de Listado de Productos -->
        <div class="table-card">
            <div class="table-header-flex">
                <h2><i class="fa-solid fa-clipboard-list"></i> Inventario de Productos</h2>

                <!-- Barra de Búsqueda y Filtros -->
                <form method="GET" action="index.php" class="filter-form" onsubmit="return false;">
                    <input type="hidden" name="vista" value="admin_productos">

                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="busqueda" id="input-busqueda-admin" placeholder="Buscar producto..."
                            value="<?php echo htmlspecialchars($busqueda); ?>" class="input-search">
                    </div>

                    <select name="cat" class="select-filter">
                        <option value="0">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $filtroCategoria == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($busqueda) || $filtroCategoria > 0): ?> <a
                            href="<?php echo $directorio_raiz; ?>index.php?vista=admin_productos"
                            class="btn-limpiar-filtro">
                            <i class="fa-solid fa-rotate-right"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-box-open"></i>
                                        <p>No se encontraron productos registrados.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td><strong>#<?php echo $prod['id_producto']; ?></strong></td>
                                    <td>
                                        <img src="<?php echo $directorio_raiz; ?>assets/img/productos/<?php echo !empty($prod['imagen']) ? htmlspecialchars($prod['imagen']) : 'default.png'; ?>"
                                            alt="Prod" class="img-thumbnail">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($prod['nombre']); ?></strong></td>
                                    <td><span
                                            class="badge-cat"><?php echo htmlspecialchars($prod['categoria_nombre'] ?? 'Sin Categoría'); ?></span>
                                    </td>
                                    <td class="precio-col">Q <?php echo number_format($prod['precio'], 2); ?></td>
                                    <td>
                                        <span class="stock-badge <?php echo ($prod['cantidad'] <= 5) ? 'stock-bajo' : ''; ?>">
                                            <?php echo $prod['cantidad']; ?> un.
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?php echo $prod['estado'] === 'activo' ? 'badge-activo' : 'badge-inactivo'; ?>">
                                            <?php echo ucfirst($prod['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_productos&action=editar&id=<?php echo $prod['id_producto']; ?>"
                                            class="btn-accion btn-editar" title="Editar">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </a>
                                        <button type="button" class="btn-accion btn-eliminar"
                                            onclick="abrirModalEliminar(<?php echo $prod['id_producto']; ?>, '<?php echo addslashes($prod['nombre']); ?>')"
                                            title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de Confirmación de Eliminación Personalizado -->
    <div id="modalEliminar" class="custom-modal-overlay">
        <div class="custom-modal">
            <div class="modal-icon-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3>¿Estás seguro?</h3>
            <p id="modalTextoProducto">Estás a punto de eliminar este producto del inventario. Esta acción no se puede
                deshacer.</p>
            <div class="modal-buttons">
                <button type="button" class="btn-modal-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
                <a id="btnConfirmarEliminar" href="#" class="btn-modal-confirmar">Sí, eliminar</a>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>

    <!-- Scripts de Filtrado Dinámico y Control de Modal -->
    <script>
        const directorioRaiz = "<?php echo $directorio_raiz; ?>";

        // Modal de Eliminación
        function abrirModalEliminar(id, nombre) {
            const modal = document.getElementById('modalEliminar');
            const btnConfirmar = document.getElementById('btnConfirmarEliminar');
            const texto = document.getElementById('modalTextoProducto');

            texto.innerHTML = `Estás a punto de eliminar el producto: <strong>"${nombre}"</strong>. Esta acción no se puede deshacer.`;
            btnConfirmar.href = `${directorioRaiz}index.php?vista=admin_productos&action=eliminar&id=${id}`;
            modal.classList.add('active');
        }

        function cerrarModalEliminar() {
            const modal = document.getElementById('modalEliminar');
            modal.classList.remove('active');
        }

        window.addEventListener('click', function (e) {
            const modal = document.getElementById('modalEliminar');
            if (e.target === modal) {
                cerrarModalEliminar();
            }
        });

        // Filtrado en tiempo real sin recargar página
        const inputBusquedaAdmin = document.getElementById('input-busqueda-admin');
        const selectCategoriaAdmin = document.querySelector('.select-filter');

        function actualizarTablaAdmin() {
            let query = inputBusquedaAdmin ? inputBusquedaAdmin.value.trim() : '';
            let categoriaSelect = selectCategoriaAdmin ? selectCategoriaAdmin.value : '0';

            let url = `${directorioRaiz}index.php?vista=admin_productos&busqueda=${encodeURIComponent(query)}&cat=${categoriaSelect}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let nuevaTabla = doc.querySelector('.table-responsive');

                    if (nuevaTabla) {
                        document.querySelector('.table-responsive').innerHTML = nuevaTabla.innerHTML;
                    }
                })
                .catch(err => console.error('Error en búsqueda dinámica:', err));
        }

        if (inputBusquedaAdmin) {
            let timeoutId;
            inputBusquedaAdmin.addEventListener('input', function () {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(actualizarTablaAdmin, 300);
            });
        }

        if (selectCategoriaAdmin) {
            selectCategoriaAdmin.addEventListener('change', function () {
                actualizarTablaAdmin();
            });
        }
    </script>
</body>

</html>