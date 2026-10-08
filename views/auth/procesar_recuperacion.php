<?php
/**
 * Procesar Recuperación de Contraseña - Ferretería El Constructor
 * Valida la existencia del correo y actualiza la credencial de forma segura.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Usuario.php';

$usuarioModel = new Usuario();
$correo_verificado = "";
$paso = 1;

// 1. Si recibimos el correo del paso 1 para verificar su existencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !isset($_POST['nueva_password'])) {
    $email = trim($_POST['email']);

    // Verificamos si el método de búsqueda existe en tu modelo Usuario
    // (Asegúrate de que en tu modelo Usuario.php exista un método para buscar por correo, ej: obtenerPorCorreo o similar)
    $usuario = null;
    if (method_exists($usuarioModel, 'obtenerPorCorreo')) {
        $usuario = $usuarioModel->obtenerPorCorreo($email);
    } elseif (method_exists($usuarioModel, 'buscarPorCorreo')) {
        $usuario = $usuarioModel->buscarPorCorreo($email);
    } else {
        // Fallback genérico por si tu método se llama distinto o está integrado en otra función
        // Puedes ajustar esta consulta si prefieres llamar a una función específica de tu modelo.
        $usuario = $usuarioModel->obtenerPorCorreo($email) ?? null;
    }

    if (!$usuario) {
        // Si el correo no existe en la BD, lo devolvemos con error
        header("Location: index.php?vista=recuperar&error=no_encontrado");
        exit();
    } else {
        // Si existe, avanzamos al paso 2 (mostrar campo de nueva contraseña)
        $paso = 2;
        $correo_verificado = $email;
    }
}

// 2. Si recibimos la nueva contraseña definitiva para actualizarla en la BD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_password']) && isset($_POST['correo_oculto'])) {
    $email = trim($_POST['correo_oculto']);
    $nueva_password = trim($_POST['nueva_password']);

    if (!empty($email) && !empty($nueva_password)) {
        // Encriptar contraseña de forma segura con BCRYPT
        $password_hash = password_hash($nueva_password, PASSWORD_BCRYPT);

        // Llamamos al método de tu modelo para actualizar la contraseña. 
        // Asegúrate de tener o agregar en tu models/Usuario.php un método como actualizarPasswordPorCorreo($email, $password_hash)
        if (method_exists($usuarioModel, 'actualizarPasswordPorCorreo')) {
            $usuarioModel->actualizarPasswordPorCorreo($email, $password_hash);
        } else {
            // Método alternativo por si actualizas mediante conexión directa o ID
            // Si tu modelo usa otra función, reemplázala aquí.
            $db = (new Conexion())->conectar();
            $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE correo = ?");
            $stmt->execute([$password_hash, $email]);
        }

        // Redirigir al login con parámetro de éxito
        header("Location: index.php?vista=login&recuperacion=exito");
        exit();
    }
}

$page_title = "Restablecer Contraseña | Ferretería El Constructor";
$extra_css = "login.css";
include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<main class="login-main-container">
    <div class="login-card">
        <?php if ($paso == 2 || isset($_POST['correo_oculto'])):
            $correoActual = $_POST['correo_oculto'] ?? $correo_verificado;
            ?>
            <div class="login-header">
                <h2>Crea tu nueva contraseña</h2>
                <p>Correo verificado: <strong><?php echo htmlspecialchars($correoActual); ?></strong></p>
            </div>

            <form action="index.php?vista=procesar_recuperacion" method="POST" class="login-form">
                <input type="hidden" name="correo_oculto" value="<?php echo htmlspecialchars($correoActual); ?>">

                <div class="form-group password-group">
                    <label for="nueva_password">Nueva Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="nueva_password" name="nueva_password" required
                            autocomplete="new-password" placeholder="Mínimo 6 caracteres">
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="btn-text">Actualizar Contraseña</span>
                </button>
            </form>
        <?php else: ?>
            <!-- Seguridad: Si intentan acceder directamente por la URL sin pasar por el formulario -->
            <script>
                window.location.href = "index.php?vista=recuperar";
            </script>
        <?php endif; ?>

        <div class="login-footer" style="margin-top: 20px; text-align: center;">
            <p><a href="<?php echo $directorio_raiz; ?>index.php?vista=login"><i class="fa-solid fa-arrow-left"></i>
                    Volver al inicio de sesión</a></p>
        </div>
    </div>
</main>

<script>
    // Script opcional para mostrar/ocultar contraseña en este paso también
    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('nueva_password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>