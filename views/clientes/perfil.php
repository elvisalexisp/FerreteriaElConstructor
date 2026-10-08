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

require_once __DIR__ . '/../../config/conexion.php';

$mensaje = '';
$error = '';

try {
    $pdo = conexion::conectar();
} catch (Exception $e) {
    die("Error de conexión a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';

    if (!empty($nombre) && !empty($apellido) && !empty($correo)) {
        try {
            $stmtUser = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmtUser->execute([$id_usuario]);
            $datosActuales = $stmtUser->fetch(PDO::FETCH_ASSOC);

            $nombre_archivo_db = $datosActuales['foto'] ?? '';

            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $archivoTmp = $_FILES['foto_perfil']['tmp_name'];
                $nombreOriginal = $_FILES['foto_perfil']['name'];
                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($extension, $permitidas)) {
                    $nuevoNombreFoto = "perfil_" . $id_usuario . "_" . time() . "." . $extension;
                    $rutaDestino = __DIR__ . "/../../uploads/" . $nuevoNombreFoto;

                    if (!is_dir(__DIR__ . "/../../uploads/")) {
                        mkdir(__DIR__ . "/../../uploads/", 0777, true);
                    }

                    if (move_uploaded_file($archivoTmp, $rutaDestino)) {
                        if (!empty($datosActuales['foto']) && file_exists(__DIR__ . "/../../uploads/" . $datosActuales['foto'])) {
                            @unlink(__DIR__ . "/../../uploads/" . $datosActuales['foto']);
                        }
                        $nombre_archivo_db = $nuevoNombreFoto;
                    } else {
                        $error = "Error al mover la imagen cargada al servidor.";
                    }
                } else {
                    $error = "Formato de imagen no permitido. Usa JPG, JPEG, PNG o WEBP.";
                }
            }

            if (empty($error)) {
                if (!empty($password_nueva)) {
                    if (empty($password_actual)) {
                        $error = "Debes ingresar tu contraseña actual para poder establecer una nueva.";
                    } elseif ($password_nueva !== $password_confirmar) {
                        $error = "Las nuevas contraseñas no coinciden.";
                    } else {
                        if (password_verify($password_actual, $datosActuales['password'] ?? '')) {
                            $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

                            $update = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, password = ?, foto = ? WHERE id_usuario = ?");
                            $update->execute([$nombre, $apellido, $correo, $nuevo_hash, $nombre_archivo_db, $id_usuario]);

                            $_SESSION['nombre'] = $nombre . ' ' . $apellido;
                            $_SESSION['foto'] = $nombre_archivo_db;
                            $mensaje = "Datos, contraseña y foto actualizados correctamente.";
                        } else {
                            $error = "La contraseña actual que ingresaste es incorrecta.";
                        }
                    }
                } else {
                    $update = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, foto = ? WHERE id_usuario = ?");
                    $update->execute([$nombre, $apellido, $correo, $nombre_archivo_db, $id_usuario]);

                    $_SESSION['nombre'] = $nombre . ' ' . $apellido;
                    $_SESSION['foto'] = $nombre_archivo_db;
                    $mensaje = "Información personal y foto actualizadas correctamente.";
                }
            }
        } catch (Exception $e) {
            $error = "Error al actualizar los datos: " . $e->getMessage();
        }
    } else {
        $error = "Los campos de nombre, apellido y correo son obligatorios.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $usuario = ['nombre' => '', 'apellido' => '', 'correo' => '', 'foto' => ''];
} else {
    // Asegurar que la sesión tenga la foto actualizada
    $_SESSION['foto'] = $usuario['foto'];
}

$directorio_raiz = $directorio_raiz ?? "http://localhost/FerreteriaElConstructor/";

include __DIR__ . '/../layouts/header_cliente.php';
?>

<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/perfil.css">

<div class="perfil-dashboard-container">
    <div class="perfil-card">
        <h2 class="perfil-title">Configuración de Mi Perfil</h2>
        <p class="perfil-subtitle">Actualiza tu información personal, fotografía o cambia tu contraseña de acceso.</p>

        <?php if (!empty($mensaje)): ?>
            <div class="perfil-alert-success">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="perfil-alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="perfil-foto-section">
                <div class="perfil-avatar-wrapper">
                    <?php if (!empty($usuario['foto']) && file_exists(__DIR__ . "/../../uploads/" . $usuario['foto'])): ?>
                        <img src="<?php echo $directorio_raiz . 'uploads/' . htmlspecialchars($usuario['foto']); ?>"
                            alt="Foto" class="perfil-img">
                    <?php else: ?>
                        <div class="perfil-avatar-placeholder">Perfil</div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="perfil-label">Actualizar Fotografía</label>
                    <input type="file" name="foto_perfil" accept="image/png, image/jpeg, image/jpg, image/webp">
                    <small class="perfil-small-text">Formatos: JPG, PNG, WEBP.</small>
                </div>
            </div>

            <div class="perfil-grid-2">
                <div>
                    <label class="perfil-label">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>"
                        required class="perfil-input">
                </div>
                <div>
                    <label class="perfil-label">Apellido</label>
                    <input type="text" name="apellido"
                        value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>" required
                        class="perfil-input">
                </div>
            </div>

            <div class="perfil-form-group">
                <label class="perfil-label">Correo Electrónico</label>
                <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>"
                    required class="perfil-input">
            </div>

            <hr class="perfil-divider">

            <h3 class="perfil-section-title">Seguridad (Cambiar Contraseña)</h3>
            <p class="perfil-subtitle">Déjalo en blanco si no deseas modificar tu contraseña.</p>

            <div class="perfil-form-group">
                <label class="perfil-label">Contraseña Actual (Obligatoria para cambiar contraseña)</label>
                <input type="password" name="password_actual" placeholder="••••••••" class="perfil-input">
            </div>

            <div class="perfil-grid-2">
                <div>
                    <label class="perfil-label">Nueva Contraseña</label>
                    <input type="password" name="password_nueva" placeholder="••••••••" class="perfil-input">
                </div>
                <div>
                    <label class="perfil-label">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmar" placeholder="••••••••" class="perfil-input">
                </div>
            </div>

            <button type="submit" class="perfil-btn-submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>