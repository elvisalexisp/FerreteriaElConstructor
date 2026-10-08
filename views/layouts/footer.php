<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sesionIniciada = isset($_SESSION['id_usuario']);

if (!isset($directorio_raiz)) {
    $directorio_raiz = "/FerreteriaElConstructor1.0/";
}
?>

<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/footer.css">

<footer class="footer-container">
    <div class="footer-content">
        <!-- Sección 1: Información de la empresa -->
        <div class="footer-section">
            <h3><i class="fas fa-tools"></i> Ferretería El Constructor</h3>
            <p>Tu aliado confiable en materiales de construcción, herramientas y acabados profesionales en Cobán, Alta
                Verapaz.</p>
            <div class="footer-sociales">
                <a href="#" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Sección 2: Enlaces Rápidos Dinámicos -->
        <div class="footer-section">
            <h4>Enlaces Rápidos</h4>
            <ul>
                <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo"><i class="fas fa-angle-right"></i>
                        Catálogo de Productos</a></li>
                <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=resenas"><i class="fas fa-angle-right"></i>
                        Reseñas y Opiniones</a></li>

                <?php if ($sesionIniciada): ?>
                    <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=perfil"><i class="fas fa-angle-right"></i>
                            Mi Perfil</a></li>
                    <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=carrito"><i class="fas fa-angle-right"></i>
                            Mi Carrito</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=login"><i class="fas fa-angle-right"></i>
                            Iniciar Sesión</a></li>
                    <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=registro"><i class="fas fa-angle-right"></i>
                            Crear Cuenta</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sección 3: Contacto -->
        <div class="footer-section">
            <h4>Contacto</h4>
            <p><i class="fas fa-map-marker-alt"></i> Cobán Centro, Alta Verapaz</p>
            <p><i class="fas fa-phone"></i> +502 5555-5555</p>
            <p><i class="fas fa-envelope"></i> contacto@ferreteriaelconstructor.com</p>
            <p><i class="fas fa-clock"></i> Lun - Sáb: 8:00 AM - 6:00 PM</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Ferretería El Constructor 1.0. Todos los derechos reservados.</p>
    </div>
</footer>

<?php
include_once __DIR__ . '/admin_flotante.php';
?>