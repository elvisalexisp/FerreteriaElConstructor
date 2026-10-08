<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Recuperar Contraseña | Ferretería El Constructor";
$extra_css = "login.css";
include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<main class="login-main-container">
    <div class="login-card">
        <div class="login-header">
            <h2>¿Olvidaste tu contraseña?</h2>
            <p>No te preocupes. Ingresa tu correo electrónico registrado y te ayudaremos a restablecerla.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> El correo ingresado no está registrado en el sistema.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['exito']) && $_GET['exito'] === 'actualizada'): ?>
            <div class="alert-success" role="alert">
                <i class="fa-solid fa-circle-check"></i> ¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.
            </div>
        <?php endif; ?>

        <form action="index.php?vista=procesar_recuperacion" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">Correo Electrónico Registrado</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                    placeholder="tucorreo@ejemplo.com">
            </div>

            <button type="submit" class="btn-submit">
                <span class="btn-text">Verificar Correo</span>
            </button>
        </form>

        <div class="login-footer" style="margin-top: 20px; text-align: center;">
            <p><a href="<?php echo $directorio_raiz; ?>index.php?vista=login"><i class="fa-solid fa-arrow-left"></i>
                    Volver al inicio de sesión</a></p>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>