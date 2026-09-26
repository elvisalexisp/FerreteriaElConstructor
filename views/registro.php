<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($isLocal) {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "https://ferreteriaelconstructor.gt.tc/";
}

require_once '../config/conexion.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if (!empty($nombre) && !empty($correo) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            try {
                $pdo = Conexion::conectar();
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, correo, password, telefono, direccion, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, 'cliente')");
                $stmt->execute([$nombre, $apellido, $correo, $passwordHash, $telefono, $direccion]);
                $mensaje = "✅ Cuenta creada con éxito. Ya puedes iniciar sesión.";
            } catch (PDOException $e) {
                $error = "❌ El correo electrónico ya está registrado.";
            }
        } else {
            $error = "⚠️ Las contraseñas no coinciden.";
        }
    } else {
        $error = "⚠️ Rellena todos los campos obligatorios.";
    }
}

include 'layouts/header_cliente.php';
?>

<div class="register-wrapper" style="padding: 20px; display: flex; justify-content: center; align-items: center;">
    <div class="card register-card-enhanced" style="width: 100%; max-width: 600px;">
        <div class="register-header-icon" style="text-align: center; font-size: 2rem; margin-bottom: 10px;">📝</div>
        <h2 class="register-title" style="text-align: center; margin-bottom: 5px;">Registro de Cliente</h2>
        <p class="register-subtitle" style="text-align: center; color: #64748b; margin-bottom: 20px;">Crea tu cuenta
            para comenzar a realizar pedidos</p>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-success-custom"
                style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center;">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-error-custom"
                style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="register-form">
            <div class="form-row-grid"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Tu nombre"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Apellido *</label>
                    <input type="text" name="apellido" class="form-control" required placeholder="Tu apellido"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Correo Electrónico *</label>
                <input type="email" name="correo" class="form-control" required placeholder="ejemplo@correo.com"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contraseña *</label>
                <div class="password-container" style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" required
                        placeholder="••••••••"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">👁️</button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Confirmar Contraseña *</label>
                <div class="password-container" style="position: relative;">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required
                        placeholder="••••••••"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">👁️</button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Teléfono</label>
                <input type="tel" name="telefono" class="form-control" placeholder="0000-0000"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2" placeholder="Dirección de entrega"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
            </div>

            <button type="submit" class="btn btn-register-pulse"
                style="width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Crear
                Cuenta</button>
        </form>

        <div class="register-footer-login" style="text-align: center; margin-top: 20px;">
            <p style="color: #64748b; margin-bottom: 5px;">¿Ya tienes una cuenta?</p>
            <a href="login.php" class="link-login-highlight"
                style="color: #2563eb; text-decoration: none; font-weight: bold;">🔑 Inicia sesión aquí</a>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId,btn) {
        const input=document.getElementById(fieldId);
        if(input.type==="password") {
            input.type="text";
            btn.textContent="🙈";
        } else {
            input.type="password";
            btn.textContent="👁️";
        }
    }
</script>

<?php
include 'layouts/footer.php';
?>