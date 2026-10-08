<?php
/**
 * Vista Administración de Usuarios - Ferretería El Constructor
 */
require_once __DIR__ . '/../../controllers/UsuarioController.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new UsuarioController();

// Si llega una petición POST, ejecutamos el método guardar del controlador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->guardar();
}

// Si llega una petición de eliminar por GET, ejecutamos el método eliminar del controlador
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $controller->eliminar();
}

// Capturamos el mensaje de éxito que dejó el controlador en la sesión y lo limpiamos
$mensaje_exito = "";
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

$usuarios = $controller->listar();

include_once __DIR__ . '/../layouts/header_admin.php';
?>

<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/adminUsuarios.css">

<div class="admin-container">
    <!-- Header de la sección -->
    <div class="admin-header">
        <h2>Gestión de Usuarios y Roles</h2>
        <p>Administra las cuentas de clientes, administradores y personal de la ferretería.</p>
    </div>

    <!-- Mensaje de Éxito Dinámico -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert-success" id="success-alert">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                <?php echo htmlspecialchars($mensaje_exito); ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Formulario de Registro / Edición -->
    <div class="tabla-admin-wrapper" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 id="form-titulo"><i class="fa-solid fa-user-plus"></i> Registrar / Editar Usuario</h3>
        </div>

        <form action="index.php?vista=admin_usuarios" method="POST" class="form-admin-usuario">
            <input type="hidden" name="id_usuario" id="id_usuario">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Ej. Pérez">
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" placeholder="correo@ferreteria.com" required>
                </div>

                <!-- Grupo de Contraseña con el botón del "ojito" -->
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Dejar en blanco si no cambia">
                        <button type="button" class="btn-toggle-password" id="btnTogglePassword"
                            onclick="togglePasswordVisibility()" title="Mostrar u ocultar contraseña">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" placeholder="Ej. 45565445">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ej. Zona 2, Cobán">
                </div>
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuario (Rol):</label>
                    <select id="tipo_usuario" name="tipo_usuario" required>
                        <option value="cliente">Cliente</option>
                        <option value="subadmin">Subadmin</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primario" id="btn-submit">
                    <i class="fa-solid fa-save"></i> Guardar Usuario
                </button>
                <button type="button" class="btn-secundario" id="btn-cancelar" style="display:none;"
                    onclick="limpiarFormulario()">
                    <i class="fa-solid fa-xmark"></i> Cancelar Edición
                </button>
            </div>
        </form>
    </div>

    <!-- Listado de Usuarios -->
    <div class="tabla-admin-wrapper">
        <div class="card-header">
            <h3><i class="fa-solid fa-users"></i> Usuarios Registrados</h3>
        </div>

        <div class="table-responsive">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Rol</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">No hay usuarios registrados en el sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><span class="user-id">#
                                        <?php echo $u['id_usuario']; ?>
                                    </span></td>
                                <td><strong>
                                        <?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?>
                                    </strong></td>
                                <td>
                                    <?php echo htmlspecialchars($u['correo']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($u['telefono'] ?: 'N/D'); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($u['direccion'] ?: 'N/D'); ?>
                                </td>
                                <td>
                                    <span class="badge-rol <?php echo strtolower($u['tipo_usuario']); ?>">
                                        <?php echo ucfirst($u['tipo_usuario']); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button type="button" class="btn-tabla-editar"
                                        onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8'); ?>)"
                                        title="Editar Usuario">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>

                                    <button type="button" class="btn-tabla-eliminar"
                                        onclick="abrirModalEliminar('<?php echo $directorio_raiz; ?>index.php?vista=admin_usuarios&accion=eliminar&id=<?php echo $u['id_usuario']; ?>')"
                                        title="Eliminar Usuario">
                                        <i class="fa-solid fa-trash"></i> Eliminar
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

<!-- Modal Personalizado de Confirmación de Eliminación -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal-card">
        <div class="modal-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>¿Estás seguro?</h3>
        <p>Esta acción eliminará permanentemente al usuario del sistema. No se podrá deshacer.</p>
        <div class="modal-actions">
            <button type="button" class="btn-modal-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
            <a href="#" id="btnConfirmarEliminar" class="btn-modal-confirmar">Sí, Eliminar</a>
        </div>
    </div>
</div>

<script>
    function editarUsuario(user) {
        document.getElementById('id_usuario').value = user.id_usuario;
        document.getElementById('nombre').value = user.nombre;
        document.getElementById('apellido').value = user.apellido || '';
        document.getElementById('correo').value = user.correo;
        document.getElementById('password').value = '';
        document.getElementById('telefono').value = user.telefono || '';
        document.getElementById('direccion').value = user.direccion || '';
        document.getElementById('tipo_usuario').value = user.tipo_usuario;

        document.getElementById('form-titulo').innerHTML = '<i class="fa-solid fa-user-pen"></i> Editando Usuario #' + user.id_usuario;
        document.getElementById('btn-submit').innerHTML = '<i class="fa-solid fa-sync"></i> Actualizar Cambios';
        document.getElementById('btn-cancelar').style.display = 'inline-flex';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function limpiarFormulario() {
        document.querySelector('.form-admin-usuario').reset();
        document.getElementById('id_usuario').value = '';
        document.getElementById('form-titulo').innerHTML = '<i class="fa-solid fa-user-plus"></i> Registrar / Editar Usuario';
        document.getElementById('btn-submit').innerHTML = '<i class="fa-solid fa-save"></i> Guardar Usuario';
        document.getElementById('btn-cancelar').style.display = 'none';
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function abrirModalEliminar(urlEliminacion) {
        document.getElementById('btnConfirmarEliminar').setAttribute('href', urlEliminacion);
        document.getElementById('modalEliminar').classList.add('active');
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').classList.remove('active');
    }

    // Desvanecer alerta de éxito automáticamente después de 4 segundos
    setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>