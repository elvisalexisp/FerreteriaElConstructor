<?php
/**
 * Vista Admin Categorías - Ferretería El Constructor
 */
require_once __DIR__ . '/../../controllers/CategoriaController.php';
$categoriaController = new CategoriaController();
$categorias = $categoriaController->listar();

// Si se solicita editar una categoría específica
$editando = false;
$catActual = ['id_categoria' => '', 'nombre' => '', 'descripcion' => ''];
if (isset($_GET['editar'])) {
    $idEdit = intval($_GET['editar']);
    $modeloTemp = new Categoria();
    $resultadoEdit = $modeloTemp->obtenerPorId($idEdit);
    if ($resultadoEdit) {
        $editando = true;
        $catActual = $resultadoEdit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Panel Admin</title>
    <!-- Hojas de estilos del panel de administración -->
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/adminCategorias.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include_once __DIR__ . '/../layouts/header_admin.php'; ?>

    <main class="admin-main-container">
        <div class="admin-container">
            <div class="admin-header-section">
                <h2>Gestión de Categorías</h2>
                <p>Administra las líneas de productos disponibles en el catálogo de la ferretería</p>
            </div>

            <!-- Alertas del sistema -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php
                    if ($_GET['exito'] === 'creado')
                        echo "Categoría creada exitosamente.";
                    elseif ($_GET['exito'] === 'actualizado')
                        echo "Categoría actualizada correctamente.";
                    elseif ($_GET['exito'] === 'eliminado')
                        echo "Categoría eliminada del sistema.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> Ocurrió un error en la operación. Verifique los datos.
                </div>
            <?php endif; ?>

            <div class="admin-content-grid">
                <!-- Formulario de Creación / Edición -->
                <div class="admin-form-card">
                    <h3>
                        <?php echo $editando ? 'Editar Categoría' : 'Nueva Categoría'; ?>
                    </h3>
                    <form action="index.php?controller=Categoria&action=guardar" method="POST" class="form-admin">
                        <input type="hidden" name="id_categoria" value="<?php echo $catActual['id_categoria']; ?>">

                        <div class="form-group">
                            <label for="nombre">Nombre de la Categoría</label>
                            <input type="text" id="nombre" name="nombre" required
                                value="<?php echo htmlspecialchars($catActual['nombre']); ?>"
                                placeholder="Ej. Herramientas Eléctricas">
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="3"
                                placeholder="Breve detalle de la categoría"><?php echo htmlspecialchars($catActual['descripcion']); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <?php echo $editando ? 'Actualizar Categoría' : 'Guardar Categoría'; ?>
                            </button>
                            <?php if ($editando): ?>
                                <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_categorias"
                                    class="btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Listado -->
                <div class="admin-table-card">
                    <h3>Categorías Existentes (
                        <?php echo count($categorias); ?>)
                    </h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categorias)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No hay categorías registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <tr>
                                            <td>#
                                                <?php echo $cat['id_categoria']; ?>
                                            </td>
                                            <td><strong>
                                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                                </strong></td>
                                            <td>
                                                <?php echo htmlspecialchars($cat['descripcion']); ?>
                                            </td>
                                            <td class="actions-cell">
                                                <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_categorias&editar=<?php echo $cat['id_categoria']; ?>"
                                                    class="btn-icon edit" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <!-- Botón que activa el modal personalizado en lugar del alert nativo -->
                                                <button type="button" class="btn-icon delete" title="Borrar"
                                                    onclick="abrirModalEliminar('<?php echo $directorio_raiz; ?>index.php?controller=Categoria&action=eliminar&id=<?php echo $cat['id_categoria']; ?>', '<?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES); ?>')">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Confirmación Personalizado -->
    <div id="modalEliminar" class="ferre-modal-overlay">
        <div class="ferre-modal-box">
            <div class="ferre-modal-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3>¿Eliminar categoría?</h3>
            <p>Estás a punto de eliminar la categoría <strong id="nombreCatModal"></strong>. Esta acción no se puede
                deshacer.</p>
            <div class="ferre-modal-actions">
                <button type="button" class="ferre-btn-modal-cancel" onclick="cerrarModalEliminar()">Cancelar</button>
                <a id="btnConfirmarEliminar" href="#" class="ferre-btn-modal-confirm">Sí, eliminar</a>
            </div>
        </div>
    </div>

    <script>
        function abrirModalEliminar(urlEliminar, nombreCategoria) {
            const modal = document.getElementById('modalEliminar');
            const spanNombre = document.getElementById('nombreCatModal');
            const btnConfirmar = document.getElementById('btnConfirmarEliminar');

            spanNombre.innerText = `"${nombreCategoria}"`;
            btnConfirmar.href = urlEliminar;
            modal.style.display = 'flex';
        }

        function cerrarModalEliminar() {
            const modal = document.getElementById('modalEliminar');
            modal.style.display = 'none';
        }

        // Cerrar modal si hacen clic fuera de la caja blanca
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('modalEliminar');
            if (e.target === modal) {
                cerrarModalEliminar();
            }
        });
    </script>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>

</body>

</html>