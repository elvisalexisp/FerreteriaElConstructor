<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "/FerreteriaElConstructor1.0/";

require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../models/Resena.php';

$pedidoModel = new Pedido();
$resenaModel = new Resena();

// --- PROCESAR ACCIONES DE CAMBIO DE ESTADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiar_estado') {
        $id_pedido = intval($_POST['id_pedido'] ?? 0);
        $nuevo_estado = trim($_POST['estado'] ?? '');
        if ($id_pedido > 0 && !empty($nuevo_estado)) {
            $pedidoModel->actualizarEstado($id_pedido, $nuevo_estado);
        }
        header('Location: index.php?vista=admin_consultas');
        exit();
    }
}

// --- PROCESAR ACCIONES DE ELIMINACIÓN (GET) ---
if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];

    if ($accion === 'eliminar_resena' && isset($_GET['id'])) {
        $id_resena = intval($_GET['id']);
        $resenaModel->eliminar($id_resena);
        header('Location: index.php?vista=admin_consultas');
        exit();
    }

    if ($accion === 'eliminar_pedido' && isset($_GET['id'])) {
        $id_pedido = intval($_GET['id']);
        $pedidoModel->eliminar($id_pedido);
        header('Location: index.php?vista=admin_consultas');
        exit();
    }
}

$todosLosPedidos = $pedidoModel->obtenerTodos() ?? [];
$todasLasResenas = $resenaModel->obtenerTodas() ?? [];

// Calcular estadísticas rápidas
$totalVentas = 0;
$totalPedidos = count($todosLosPedidos);
$pendientesCount = 0;
$procesandoCount = 0;
$completadosCount = 0;

foreach ($todosLosPedidos as $p) {
    $estado = trim($p['estado'] ?? '');
    if ($estado !== 'Cancelado') {
        $totalVentas += floatval($p['total'] ?? 0);
    }
    if ($estado === 'Pendiente') {
        $pendientesCount++;
    } elseif ($estado === 'Procesando') {
        $procesandoCount++;
    } elseif ($estado === 'Completado') {
        $completadosCount++;
    }
}

include_once __DIR__ . '/../layouts/header_admin.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/adminConsultas.css">

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-header-text">
            <h2>Panel de Consultas y Control</h2>
            <p>Supervisa las transacciones comerciales, datos de facturación y moderación de opiniones.</p>
        </div>
        <div class="admin-header-action">
            <a href="#seccion-resenas" class="btn-primario">
                <i class="fa-solid fa-comments"></i> Gestionar Reseñas
                <span class="badge-count">
                    <?php echo count($todasLasResenas); ?>
                </span>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Resumen Estadístico Avanzado -->
    <div class="admin-stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap blue-theme">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3>Total Pedidos</h3>
                <p class="stat-number">
                    <?php echo number_format($totalPedidos); ?>
                </p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap green-theme">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="stat-content">
                <h3>Ventas Acumuladas</h3>
                <p class="stat-number">Q
                    <?php echo number_format($totalVentas, 2); ?>
                </p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap orange-theme">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>Pendientes de Atención</h3>
                <p class="stat-number">
                    <?php echo number_format($pendientesCount); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Tabla principal de consultas (Pedidos) -->
    <div class="tabla-admin-wrapper">
        <div class="table-header-title">
            <h3><i class="fa-solid fa-box-archive"></i> Listado General de Pedidos</h3>
        </div>
        <div class="table-responsive">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Datos de Factura</th>
                        <th>Dirección de Envío</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($todosLosPedidos)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay registros de pedidos disponibles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todosLosPedidos as $p): ?>
                            <tr>
                                <td class="fw-bold">#
                                    <?php echo htmlspecialchars($p['id_pedido']); ?>
                                </td>
                                <td>
                                    <span class="client-name">
                                        <?php echo htmlspecialchars($p['cliente_nombre']); ?>
                                    </span><br>
                                    <span class="client-email">
                                        <?php echo htmlspecialchars($p['correo']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>NIT:</strong>
                                    <?php echo htmlspecialchars($p['nit']); ?><br>
                                    <span class="invoice-name">
                                        <?php echo htmlspecialchars($p['nombre_factura']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($p['direccion_envio']); ?>
                                </td>
                                <td><strong class="text-success-custom">Q
                                        <?php echo number_format($p['total'], 2); ?>
                                    </strong></td>
                                <td>
                                    <form id="form-estado-<?php echo $p['id_pedido']; ?>"
                                        action="index.php?vista=admin_consultas" method="POST" class="form-estado">
                                        <input type="hidden" name="accion" value="cambiar_estado">
                                        <input type="hidden" name="id_pedido" value="<?php echo $p['id_pedido']; ?>">
                                        <select name="estado" class="estado-select"
                                            onchange="mostrarModalEstado(this, '<?php echo $p['id_pedido']; ?>', '<?php echo $p['estado']; ?>')">
                                            <option value="Pendiente" <?php echo $p['estado'] === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="Procesando" <?php echo $p['estado'] === 'Procesando' ? 'selected' : ''; ?>>Procesando</option>
                                            <option value="Completado" <?php echo $p['estado'] === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                                            <option value="Cancelado" <?php echo $p['estado'] === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="date-cell">
                                    <?php echo htmlspecialchars($p['fecha']); ?>
                                </td>
                                <td>
                                    <button type="button" class="btn-accion-elegante btn-eliminar-elegante"
                                        onclick="mostrarModalEliminar('pedido', '<?php echo $p['id_pedido']; ?>')"
                                        title="Eliminar Pedido">
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

    <!-- Sección de Moderación y Gestión de Reseñas -->
    <div id="seccion-resenas" class="tabla-admin-wrapper">
        <div class="table-header-title">
            <h3><i class="fa-solid fa-comments"></i> Moderación de Reseñas y Comentarios de Clientes</h3>
        </div>
        <p class="section-desc">Aquí puedes supervisar las opiniones de los usuarios y eliminar comentarios ofensivos o
            inapropiados.</p>
        <div class="table-responsive">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($todasLasResenas)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay reseñas registradas para moderar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todasLasResenas as $r): ?>
                            <tr>
                                <td class="fw-bold">#
                                    <?php echo htmlspecialchars($r['id_resena']); ?>
                                </td>
                                <td><span class="client-name">
                                        <?php echo htmlspecialchars($r['usuario_nombre']); ?>
                                    </span></td>
                                <td>
                                    <?php echo htmlspecialchars($r['producto_nombre']); ?>
                                </td>
                                <td>
                                    <span class="stars-display">
                                        <?php echo str_repeat('⭐', intval($r['calificacion'])); ?>
                                    </span>
                                </td>
                                <td class="comment-text">
                                    <?php echo htmlspecialchars($r['comentario']); ?>
                                </td>
                                <td class="date-cell">
                                    <?php echo htmlspecialchars($r['fecha']); ?>
                                </td>
                                <td>
                                    <button type="button" class="btn-accion-elegante btn-eliminar-elegante"
                                        onclick="mostrarModalEliminar('resena', '<?php echo $r['id_resena']; ?>')"
                                        title="Eliminar Reseña">
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

<!-- ========================================== -->
<!-- MODAL DE ADVERTENCIA PERSONALIZADO (NATIVO) -->
<!-- ========================================== -->
<div id="customModal" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <div id="modalIconWrap" class="modal-icon-wrap warning">
            <i id="modalIconClass" class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 id="modalTitle">¿Estás seguro?</h3>
        <p id="modalMessage">Mensaje de advertencia aquí...</p>
        <div class="modal-actions">
            <button type="button" id="modalBtnCancelar" class="btn-modal-cancelar"
                onclick="cerrarModalCustom()">Cancelar</button>
            <button type="button" id="modalBtnAceptar" class="btn-modal-aceptar"
                onclick="ejecutarAccionModal()">Aceptar</button>
        </div>
    </div>
</div>

<script>
    let callbackAceptar = null;
    let callbackCancelar = null;

    function abrirModalCustom(titulo, mensaje, tipo, textoAceptar, colorBtnAceptar, fnAceptar, fnCancelar) {
        document.getElementById('modalTitle').innerText = titulo;
        document.getElementById('modalMessage').innerText = mensaje;

        const iconWrap = document.getElementById('modalIconWrap');
        const iconClass = document.getElementById('modalIconClass');
        const btnAceptar = document.getElementById('modalBtnAceptar');

        iconWrap.className = 'modal-icon-wrap ' + tipo;
        if (tipo === 'warning') {
            iconClass.className = 'fa-solid fa-triangle-exclamation';
        } else {
            iconClass.className = 'fa-solid fa-circle-question';
        }

        btnAceptar.innerText = textoAceptar;
        btnAceptar.style.backgroundColor = colorBtnAceptar;

        callbackAceptar = fnAceptar;
        callbackCancelar = fnCancelar;

        document.getElementById('customModal').classList.add('active');
    }

    function cerrarModalCustom() {
        document.getElementById('customModal').classList.remove('active');
        if (typeof callbackCancelar === 'function') {
            callbackCancelar();
        }
    }

    function ejecutarAccionModal() {
        document.getElementById('customModal').classList.remove('active');
        if (typeof callbackAceptar === 'function') {
            callbackAceptar();
        }
    }

    // Lógica de cambio de estado de pedido con modal nativo
    function mostrarModalEstado(selectElement, idPedido, estadoAnterior) {
        const nuevoEstado = selectElement.value;

        abrirModalCustom(
            '¿Actualizar Estado?',
            `¿Deseas cambiar el estado del pedido #${idPedido} a "${nuevoEstado}"?`,
            'question',
            'Sí, actualizar',
            '#e67e22',
            function () {
                document.getElementById('form-estado-' + idPedido).submit();
            },
            function () {
                selectElement.value = estadoAnterior; // Revierte visualmente si cancela
            }
        );
    }

    // Lógica de eliminación con modal nativo
    function mostrarModalEliminar(tipoItem, id) {
        let titulo = tipoItem === 'pedido' ? '¿Eliminar Pedido?' : '¿Eliminar Reseña?';
        let mensaje = tipoItem === 'pedido'
            ? `Se eliminará permanentemente el registro del pedido #${id}. Esta acción no se puede deshacer.`
            : `¿Estás seguro de eliminar esta reseña por contenido ofensivo o inapropiado?`;

        abrirModalCustom(
            titulo,
            mensaje,
            'warning',
            'Sí, eliminar',
            '#e53e3e',
            function () {
                if (tipoItem === 'pedido') {
                    window.location.href = `index.php?vista=admin_consultas&accion=eliminar_pedido&id=${id}`;
                } else {
                    window.location.href = `index.php?vista=admin_consultas&accion=eliminar_resena&id=${id}`;
                }
            },
            null
        );
    }
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>