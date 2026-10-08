<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Crear Cuenta | Ferretería El Constructor";
$extra_css = "registro.css";

// Definir el directorio raíz si no está definido previamente
$directorio_raiz = $directorio_raiz ?? "http://localhost/FerreteriaElConstructor/";

include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/registro.css">

<main class="registro-main-container">
    <div class="registro-card">
        <div class="registro-header">
            <h2>Crea tu Cuenta</h2>
            <p>Disfruta de beneficios exclusivos para tus proyectos de construcción en Cobán</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'email_existente'): ?>
            <div class="alert-error" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> Este correo electrónico ya está registrado. <a
                    href="<?php echo $directorio_raiz; ?>index.php?vista=login">Inicia sesión aquí</a>.
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert-error" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> Ocurrió un error al procesar tu registro. Por favor intenta
                de nuevo.
            </div>
        <?php endif; ?>

        <!-- Importante: Apuntar al enrutador central -->
        <form action="index.php?controller=Usuario&action=registrar" method="POST" class="registro-form"
            id="registroForm">

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required autocomplete="given-name" placeholder="Ej. Juan">
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" autocomplete="family-name" placeholder="Ej. Pérez">
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                    placeholder="tucorreo@ejemplo.com">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono / Celular</label>
                <input type="tel" id="telefono" name="telefono" autocomplete="tel" placeholder="Ej. 5555-5555">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección de Envío o Domicilio</label>
                <textarea id="direccion" name="direccion" rows="2"
                    placeholder="Zona, calle o referencia en Cobán"></textarea>
            </div>

            <div class="form-group password-group">
                <label for="password">Contraseña Segura</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        placeholder="Mínimo 6 caracteres">
                    <button type="button" class="toggle-password" id="togglePasswordReg"
                        aria-label="Mostrar contraseña">
                        <i class="fa-solid fa-eye" id="eyeIconReg"></i>
                    </button>
                </div>
            </div>

            <div class="form-group terms-group">
                <label class="terms-label">
                    <input type="checkbox" required name="terminos" id="terminos">
                    Acepto los <a href="#" target="_blank">Términos del Servicio</a> y la <a href="#"
                        target="_blank">Política de Privacidad</a>.
                </label>
            </div>

            <button type="submit" class="btn-submit" id="submitRegBtn">
                <span class="btn-text">Registrarme Ahora</span>
                <span class="spinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i> Creando
                    cuenta...</span>
            </button>
        </form>

        <div class="registro-footer">
            <p>¿Ya tienes una cuenta registrada? <a href="<?php echo $directorio_raiz; ?>index.php?vista=login">Inicia
                    sesión</a></p>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('togglePasswordReg');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIconReg');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }

        const registroForm = document.getElementById('registroForm');
        const submitRegBtn = document.getElementById('submitRegBtn');
        if (registroForm) {
            registroForm.addEventListener('submit', () => {
                submitRegBtn.disabled = true;
                submitRegBtn.querySelector('.btn-text').style.display = 'none';
                submitRegBtn.querySelector('.spinner').style.display = 'inline-block';
            });
        }
    });
</script>

<?php
include_once __DIR__ . '/../layouts/footer.php';
?>