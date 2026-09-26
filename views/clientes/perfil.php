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

require_once __DIR__ . '/../../models/Database.php';

$mensaje = '';
$error = '';

try {
    $pdo = Database::conectar();
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
                        $error = "❌ Error al mover la imagen cargada al servidor.";
                    }
                } else {
                    $error = "❌ Formato de imagen no permitido. Usa JPG, JPEG, PNG o WEBP.";
                }
            }

            if (empty($error)) {
                if (!empty($password_nueva)) {
                    if (empty($password_actual)) {
                        $error = "⚠️ Debes ingresar tu contraseña actual para poder establecer una nueva.";
                    } elseif ($password_nueva !== $password_confirmar) {
                        $error = "❌ Las nuevas contraseñas no coinciden.";
                    } else {
                        if (password_verify($password_actual, $datosActuales['password'] ?? '')) {
                            $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

                            $update = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, password = ?, foto = ? WHERE id_usuario = ?");
                            $update->execute([$nombre, $apellido, $correo, $nuevo_hash, $nombre_archivo_db, $id_usuario]);

                            $_SESSION['nombre'] = $nombre . ' ' . $apellido;
                            $mensaje = "✅ Datos, contraseña y foto actualizados correctamente.";
                        } else {
                            $error = "❌ La contraseña actual que ingresaste es incorrecta.";
                        }
                    }
                } else {
                    $update = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, foto = ? WHERE id_usuario = ?");
                    $update->execute([$nombre, $apellido, $correo, $nombre_archivo_db, $id_usuario]);

                    $_SESSION['nombre'] = $nombre . ' ' . $apellido;
                    $mensaje = "✅ Información personal y foto actualizadas correctamente.";
                }
            }
        } catch (Exception $e) {
            $error = "❌ Error al actualizar los datos: " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Los campos de nombre, apellido y correo son obligatorios.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $usuario = ['nombre' => '', 'apellido' => '', 'correo' => '', 'foto' => ''];
}

include __DIR__ . '/../layouts/header_cliente.php';
$base_url = "http://localhost/FerreteriaElConstructor/";
?>

<style>
    .perfil-dashboard-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .perfil-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .perfil-title {
        color: #0f172a;
        margin-top: 0;
        font-size: 1.6rem;
    }

    .perfil-subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 20px;
    }

    .perfil-alert-success {
        background: #ecfdf5;
        color: #047857;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .perfil-alert-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .perfil-foto-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
    }

    .perfil-avatar-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        background: #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 2px solid #cbd5e1;
    }

    .perfil-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .perfil-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .perfil-form-group {
        margin-bottom: 15px;
    }

    .perfil-label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .perfil-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.95px;
        box-sizing: border-box;
        outline: none;
    }

    .perfil-input:focus {
        border-color: #2563eb;
    }

    .perfil-divider {
        border: 0;
        border-top: 1px solid #e2e8f0;
        margin: 25px 0;
    }

    .perfil-section-title {
        color: #1e293b;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .perfil-btn-submit {
        background: #2563eb;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        font-size: 1rem;
        transition: background 0.2s;
    }

    .perfil-btn-submit:hover {
        background: #1d4ed8;
    }
</style>

<div class="perfil-dashboard-container">
    <div class="perfil-card">
        <h2 class="perfil-title">👤 Configuración de Mi Perfil</h2>
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
                        <img src="<?php echo $base_url . 'uploads/' . htmlspecialchars($usuario['foto']); ?>" alt="Foto"
                            class="perfil-img">
                    <?php else: ?>
                        <div class="perfil-avatar-placeholder">👤</div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="perfil-label">Actualizar Fotografía</label>
                    <input type="file" name="foto_perfil" accept="image/png, image/jpeg, image/jpg, image/webp">
                    <small style="color: #64748b; display: block; margin-top: 4px;">Formatos: JPG, PNG, WEBP.</small>
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

            <h3 class="perfil-section-title">🔒 Seguridad (Cambiar Contraseña)</h3>
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

            <button type="submit" class="perfil-btn-submit">💾 Guardar Cambios</button>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>