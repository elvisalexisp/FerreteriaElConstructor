<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Iniciar Sesión | Ferretería El Constructor";
$extra_css = "login.css";
include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<main class="login-main-container">
    <div class="login-card">
        <div class="login-header">
            <h2>¡Qué gusto verte de nuevo!</h2>
            <p>Ingresa a tu cuenta para gestionar tus pedidos y cotizaciones</p>
        </div>

        <!-- Alerta de Error de credenciales -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> Correo o contraseña incorrectos. Verifica tus datos.
            </div>
        <?php endif; ?>

        <!-- Alerta de Registro Exitoso -->
        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exito'): ?>
            <div class="alert-success" role="alert">
                <i class="fa-solid fa-circle-check"></i> ¡Registro exitoso! Por favor inicia sesión con tus datos.
            </div>
        <?php endif; ?>

        <!-- NUEVA: Alerta de Contraseña Actualizada con Éxito -->
        <?php if (isset($_GET['recuperacion']) && $_GET['recuperacion'] === 'exito'): ?>
            <div class="alert-success" role="alert">
                <i class="fa-solid fa-circle-check"></i> ¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu
                nueva clave.
            </div>
        <?php endif; ?>

        <!-- Formulario con autocompletado controlado -->
        <form action="index.php?controller=Usuario&action=login" method="POST" class="login-form" id="loginForm"
            autocomplete="on">

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                    placeholder="tucorreo@ejemplo.com">
            </div>

            <div class="form-group password-group">
                <label for="password">Contraseña</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="Tu contraseña">
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions-row">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember"> Recordar mi sesión
                </label>
                <a href="<?php echo $directorio_raiz; ?>index.php?vista=recuperar"
                    class="forgot-password-link">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="btn-text">Iniciar Sesión</span>
                <span class="spinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i>
                    Ingresando...</span>
            </button>
        </form>

        <div class="login-footer">
            <p>¿Aún no tienes una cuenta? <a href="<?php echo $directorio_raiz; ?>index.php?vista=registro">Regístrate
                    gratis aquí</a></p>
            <div class="security-badge">
                <i class="fa-solid fa-shield-halved"></i> Conexión segura SSL garantizada
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }

        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                submitBtn.disabled = true;
                const btnText = submitBtn.querySelector('.btn-text');
                const spinner = submitBtn.querySelector('.spinner');
                if (btnText) btnText.style.display = 'none';
                if (spinner) spinner.style.display = 'inline-block';
            });
        }
    });
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>