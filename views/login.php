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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (!empty($correo) && !empty($password)) {
        $pdo = Conexion::conectar();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];

            $tipoRol = isset($usuario['tipo_usuario']) ? strtolower(trim($usuario['tipo_usuario'])) : '';

            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'] ?? 'cliente';
            $_SESSION['rol'] = $_SESSION['tipo_usuario'];

            if ($correo === 'admin@ferreteria.com' || in_array($tipoRol, ['admin', 'administrador', '1'])) {
                header("Location: " . $base_url . "views/admin/index.php");
            } else {
                header("Location: " . $base_url . "views/clientes/index.php");
            }
            exit();
        } else {
            $error = "❌ Correo o contraseña incorrectos.";
        }
    } else {
        $error = "⚠️ Por favor completa todos los campos obligatorios.";
    }
}

include 'layouts/header_cliente.php';
?>

<div class="login-wrapper"
    style="padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="card login-card-enhanced" style="width: 100%; max-width: 450px;">
        <div class="login-header-icon" style="text-align: center; font-size: 2rem; margin-bottom: 10px;">
            🔐
        </div>
        <h2 class="login-title" style="text-align: center; margin-bottom: 5px;">Bienvenido de nuevo</h2>
        <p class="login-subtitle" style="text-align: center; color: #64748b; margin-bottom: 20px;">Ingresa tus
            credenciales para acceder a tu cuenta</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error-custom"
                style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form" autocomplete="off">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Correo Electrónico</label>
                <div class="input-with-icon">
                    <input type="email" name="correo" class="form-control" required placeholder="ejemplo@correo.com"
                        autocomplete="off"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contraseña</label>
                <div class="input-with-icon">
                    <input type="password" name="password" class="form-control" required placeholder="••••••••"
                        autocomplete="new-password"
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" class="btn btn-login-pulse"
                style="width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                <span>Ingresar al Sistema</span>
            </button>
        </form>

        <div class="login-footer-register" style="text-align: center; margin-top: 20px;">
            <p style="color: #64748b; margin-bottom: 5px;">¿Aún no cuentas con una cuenta?</p>
            <a href="<?php echo $base_url; ?>views/registro.php" class="link-register-highlight"
                style="color: #2563eb; text-decoration: none; font-weight: bold;">
                📝 Regístrate aquí con tus datos
            </a>
        </div>
    </div>
</div>

<?php
include 'layouts/footer.php';
?>