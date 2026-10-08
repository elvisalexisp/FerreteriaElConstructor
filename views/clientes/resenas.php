<?php
require_once __DIR__ . '/../../models/Resena.php';
require_once __DIR__ . '/../../models/Producto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$resenaModel = new Resena();
$productoModel = new Producto();

$id_usuario_actual = $_SESSION['id_usuario'] ?? null;

// Procesar envío de formulario (Crear o Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id_usuario_actual) {
        $accion = $_POST['accion'] ?? 'crear';
        $id_producto = intval($_POST['id_producto'] ?? 0);
        $calificacion = intval($_POST['calificacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($accion === 'editar' && isset($_POST['id_resena'])) {
            $id_resena = intval($_POST['id_resena']);
            // Opcional: verificar que la reseña pertenezca al usuario antes de actualizar en el modelo si es necesario
            $resenaModel->editar($id_resena, $calificacion, $comentario);
        } else {
            if ($id_producto > 0 && $calificacion >= 1 && $calificacion <= 5 && !empty($comentario)) {
                $resenaModel->crear($id_usuario_actual, $id_producto, $calificacion, $comentario);
            }
        }
        header('Location: ' . $directorio_raiz . 'index.php?vista=resenas');
        exit();
    } else {
        header('Location: ' . $directorio_raiz . 'index.php?vista=login');
        exit();
    }
}

// Procesar eliminación mediante solicitud POST o GET controlada
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id']) && $id_usuario_actual) {
    $id_resena = intval($_GET['id']);
    $resenaModel->eliminar($id_resena,);
    header('Location: ' . $directorio_raiz . 'index.php?vista=resenas');
    exit();
}

$resenas = $resenaModel->obtenerTodas();
$productos = $productoModel->obtenerTodos();
$usuarioLogueado = isset($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas de Productos - Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/resenas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include_once __DIR__ . '/../layouts/header_cliente.php'; ?>

    <main class="main-container">
        <!-- Modal de Confirmación Personalizado -->
        <div id="modal-confirmacion" class="modal-overlay">
            <div class="modal-box">
                <i class="fas fa-exclamation-circle"></i>
                <h3>¿Estás seguro?</h3>
                <p>¿Deseas eliminar permanentemente esta reseña?</p>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancelar"
                        onclick="cerrarModalConfirmacion()">Cancelar</button>
                    <a id="btn-confirmar-eliminar" href="#" class="modal-btn modal-btn-confirmar"
                        style="text-decoration: none; display: inline-block; line-height: normal;">Sí, eliminar</a>
                </div>
            </div>
        </div>

        <section class="seccion-titulo">
            <h1>Reseñas y Opiniones de Clientes</h1>
            <p>Comparte tu experiencia o revisa las calificaciones de nuestros productos.</p>
        </section>

        <?php if ($usuarioLogueado): ?>
            <div class="card-formulario" id="contenedor-formulario">
                <h2 id="form-titulo">Deja tu Reseña</h2>
                <form action="<?php echo $directorio_raiz; ?>index.php?vista=resenas" method="POST" class="form-resena"
                    id="form-resena-principal">
                    <input type="hidden" name="accion" id="form-accion" value="crear">
                    <input type="hidden" name="id_resena" id="form-id-resena" value="">

                    <div class="form-group" id="grupo-producto">
                        <label for="id_producto">Selecciona el Producto:</label>
                        <select id="id_producto" name="id_producto" required>
                            <option value="">-- Elige un producto --</option>
                            <?php foreach ($productos as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="calificacion">Calificación (1 a 5 Estrellas):</label>
                        <select id="calificacion" name="calificacion" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excelente)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Muy Bueno)</option>
                            <option value="3">⭐⭐⭐ (3 - Bueno)</option>
                            <option value="2">⭐⭐ (2 - Regular)</option>
                            <option value="1">⭐ (1 - Malo)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comentario">Tu Comentario:</label>
                        <textarea id="comentario" name="comentario" rows="4" placeholder="Escribe tu opinión..."
                            required></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-primario" id="btn-submit-resena">Publicar Reseña</button>
                        <button type="button" id="btn-cancelar-edicion" class="modal-btn modal-btn-cancelar"
                            style="display: none;" onclick="cancelarEdicion()">Cancelar Edición</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alerta-login">
                <p>¿Deseas dejar una reseña? <a href="<?php echo $directorio_raiz; ?>index.php?vista=login">Inicia sesión
                        aquí</a>.</p>
            </div>
        <?php endif; ?>

        <section class="lista-resenas">
            <h2>Opiniones Recientes</h2>
            <?php if (empty($resenas)): ?>
                <p class="text-center">Aún no hay reseñas registradas.</p>
            <?php else: ?>
                <div class="grid-resenas">
                    <?php foreach ($resenas as $res): ?>
                        <div class="resena-card">
                            <div class="resena-header">
                                <strong>
                                    <?php echo htmlspecialchars($res['usuario_nombre']); ?>
                                </strong>
                                <span class="calificacion">
                                    <?php echo str_repeat('⭐', intval($res['calificacion'])); ?>
                                </span>
                            </div>
                            <p class="producto-ref">Producto: <em>
                                    <?php echo htmlspecialchars($res['producto_nombre']); ?>
                                </em></p>
                            <p class="comentario" id="comentario-texto-<?php echo $res['id_resena']; ?>">
                                <?php echo nl2br(htmlspecialchars($res['comentario'])); ?>
                            </p>
                            <small class="fecha">
                                <?php echo $res['fecha']; ?>
                            </small>

                            <!-- Mostrar botones solo si la reseña pertenece al usuario logueado -->
                            <?php if ($usuarioLogueado && isset($res['id_usuario']) && intval($res['id_usuario']) === intval($id_usuario_actual)): ?>
                                <div class="acciones-resena">
                                    <button type="button" class="btn-accion-mini btn-editar-mini" onclick="prepararEdicion(
                                            <?php echo $res['id_resena']; ?>, 
                                            <?php echo $res['id_producto']; ?>, 
                                            <?php echo $res['calificacion']; ?>, 
                                            `<?php echo addslashes($res['comentario']); ?>`
                                        )">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn-accion-mini btn-eliminar-mini"
                                        onclick="abrirModalEliminar('<?php echo $directorio_raiz; ?>index.php?vista=resenas&accion=eliminar&id=<?php echo $res['id_resena']; ?>')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        const DIRECTORIO_RAIZ = "<?php echo $directorio_raiz; ?>";

        function abrirModalEliminar(urlEliminacion) {
            document.getElementById('btn-confirmar-eliminar').href = urlEliminacion;
            document.getElementById('modal-confirmacion').classList.add('activo');
        }

        function cerrarModalConfirmacion() {
            document.getElementById('modal-confirmacion').classList.remove('activo');
        }

        function prepararEdicion(idResena, idProducto, calificacion, comentario) {
            document.getElementById('form-titulo').innerText = "Editar tu Reseña";
            document.getElementById('form-accion').value = "editar";
            document.getElementById('form-id-resena').value = idResena;

            const selectProducto = document.getElementById('id_producto');
            selectProducto.value = idProducto;
            selectProducto.disabled = true; // El producto no suele cambiarse al editar una reseña ya creada

            // Creamos un campo hidden temporal para enviar el id_producto ya que los select disabled no se envían por POST
            let inputHiddenProd = document.getElementById('hidden_id_producto');
            if (!inputHiddenProd) {
                inputHiddenProd = document.createElement('input');
                inputHiddenProd.type = 'hidden';
                inputHiddenProd.name = 'id_producto';
                inputHiddenProd.id = 'hidden_id_producto';
                document.getElementById('form-resena-principal').appendChild(inputHiddenProd);
            }
            inputHiddenProd.value = idProducto;

            document.getElementById('calificacion').value = calificacion;
            document.getElementById('comentario').value = comentario;

            document.getElementById('btn-submit-resena').innerText = "Actualizar Reseña";
            document.getElementById('btn-cancelar-edicion').style.display = "inline-block";

            // Desplazar la vista suavemente hacia el formulario
            document.getElementById('contenedor-formulario').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelarEdicion() {
            document.getElementById('form-titulo').innerText = "Deja tu Reseña";
            document.getElementById('form-accion').value = "crear";
            document.getElementById('form-id-resena').value = "";

            const selectProducto = document.getElementById('id_producto');
            selectProducto.disabled = false;
            selectProducto.value = "";

            const inputHiddenProd = document.getElementById('hidden_id_producto');
            if (inputHiddenProd) inputHiddenProd.remove();

            document.getElementById('calificacion').value = "5";
            document.getElementById('comentario').value = "";

            document.getElementById('btn-submit-resena').innerText = "Publicar Reseña";
            document.getElementById('btn-cancelar-edicion').style.display = "none";
        }
    </script>

</body>

</html>