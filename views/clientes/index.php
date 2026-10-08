<?php
/**
 * Vista de Inicio (Index de Clientes) - Ferretería El Constructor
 * Versión Animada & Interactiva: Con Iframe de Google Maps, animaciones fluidas y diseño robusto.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']) || isset($_SESSION['id_usuario']);

$page_title = "Ferretería El Constructor | Materiales y Herramientas en Cobán";

// Definir el directorio raíz si no está definido previamente
$directorio_raiz = $directorio_raiz ?? "http://localhost/FerreteriaElConstructor/";

// Incluimos el header de cliente
include_once __DIR__ . '/../layouts/header_cliente.php';
?>

<!-- Enlace directo a la hoja de estilos externa -->
<link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/indexCliente.css">

<div class="ferre-home-wrapper">

    <!-- SECCIÓN 1: Hero Principal (Banner Izquierdo + Información de Atención Derecha) -->
    <section class="ferre-hero-grid">
        <!-- Banner Principal de Bienvenida -->
        <div class="ferre-main-banner ferre-fade-in">
            <span class="ferre-banner-badge"><i class="fa-solid fa-location-dot"></i> Cobán, Alta Verapaz</span>
            <h1>Todo para tu Construcción al Mejor Precio</h1>
            <p>
                Tu aliado estratégico en obra gris, herramientas profesionales, fontanería y acabados.
                Despacho rápido y seguro directo a tu domicilio o proyecto.
            </p>
            <div class="ferre-banner-actions">
                <a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo" class="ferre-btn-primary">
                    Explorar Catálogo General &rarr;
                </a>
                <?php if (!$isLoggedIn): ?>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=registro" class="ferre-btn-outline">
                        Registrarse Gratis
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tarjetas Laterales de Utilidad y Horarios -->
        <div class="ferre-side-cards">
            <div class="ferre-info-card ferre-slide-up">
                <div class="ferre-card-icon"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <h3>Horario de Atención</h3>
                    <p>Lunes a Sábado: 7:30 AM - 6:00 PM<br>Domingos: 8:00 AM - 1:00 PM</p>
                </div>
            </div>
            <div class="ferre-info-card ferre-slide-up" style="animation-delay: 0.1s;">
                <div class="ferre-card-icon"><i class="fa-solid fa-phone-volume"></i></div>
                <div>
                    <h3>Cotizaciones Rápidas</h3>
                    <p>¿Tienes una lista de materiales grande? Consúltala directo en sucursal o contáctanos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 2: Barra de Estadísticas de Confianza -->
    <section class="ferre-stats-bar ferre-fade-in">
        <div class="ferre-stat-item">
            <i class="fa-solid fa-shield-check"></i>
            <div>
                <strong>100% Garantizado</strong>
                <span>Marcas líderes del mercado</span>
            </div>
        </div>
        <div class="ferre-stat-item">
            <i class="fa-solid fa-truck-fast"></i>
            <div>
                <strong>Envíos Locales</strong>
                <span>Cobán y alrededores</span>
            </div>
        </div>
        <div class="ferre-stat-item">
            <i class="fa-solid fa-handshake"></i>
            <div>
                <strong>Atención Personalizada</strong>
                <span>Asesoría en obra y mostrador</span>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 3: Categorías Principales (Estáticas y sin enlaces rotos) -->
    <section class="ferre-section-container">
        <h2 class="ferre-section-title"><i class="fa-solid fa-layer-group"></i> Nuestros Departamentos</h2>
        <p class="ferre-section-subtitle">Conoce las áreas principales que conformamos para atender cada necesidad de tu
            construcción.</p>

        <div class="ferre-categories-grid">
            <div class="ferre-category-item ferre-hover-scale">
                <div class="ferre-category-icon"><i class="fa-solid fa-house-chimney"></i></div>
                <h4>Materiales Pesados</h4>
                <p>Estructuras, bases y obra gris.</p>
            </div>
            <div class="ferre-category-item ferre-hover-scale">
                <div class="ferre-category-icon"><i class="fa-solid fa-toolbox"></i></div>
                <h4>Herramientas Eléctricas</h4>
                <p>Taladros, sierras y equipos de poder.</p>
            </div>
            <div class="ferre-category-item ferre-hover-scale">
                <div class="ferre-category-icon"><i class="fa-solid fa-faucet"></i></div>
                <h4>Fontanería y Tubos</h4>
                <p>PVC, conexiones y accesorios sanitarios.</p>
            </div>
            <div class="ferre-category-item ferre-hover-scale">
                <div class="ferre-category-icon"><i class="fa-solid fa-bolt"></i></div>
                <h4>Electricidad e Iluminación</h4>
                <p>Cableado, tableros y luminarias LED.</p>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 4: Historia, Misión y Visión -->
    <section class="ferre-section-container">
        <div class="ferre-corporate-grid">
            <div class="ferre-corp-box ferre-slide-up">
                <div class="ferre-corp-icon"><i class="fa-solid fa-landmark"></i></div>
                <h3>Nuestra Historia</h3>
                <p>Fundados en el corazón de Cobán, Alta Verapaz, nacimos con el firme propósito de convertirnos en el
                    socio de confianza para constructores, maestros de obra y hogares guatemaltecos, ofreciendo
                    soluciones integrales bajo un mismo techo.</p>
            </div>
            <div class="ferre-corp-box ferre-slide-up" style="animation-delay: 0.15s;">
                <div class="ferre-corp-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Misión</h3>
                <p>Proveer materiales de construcción y herramientas de la más alta calidad con un servicio eficiente,
                    honesto y cercano, garantizando el éxito de cada proyecto que construyen nuestros clientes en la
                    región de las Verapaces.</p>
            </div>
            <div class="ferre-corp-box ferre-slide-up" style="animation-delay: 0.3s;">
                <div class="ferre-corp-icon"><i class="fa-solid fa-eye"></i></div>
                <h3>Visión</h3>
                <p>Ser reconocidos como la ferretería líder e innovadora en la zona norte del país, destacándonos por la
                    excelencia en el servicio al cliente, la variedad de marcas y nuestro compromiso inquebrantable con
                    el desarrollo local.</p>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 5: Ubicación Física y Mapa Interactivo (Cobán, Alta Verapaz) -->
    <section class="ferre-section-container">
        <div class="ferre-location-card ferre-fade-in">
            <div class="ferre-location-content">
                <h2><i class="fa-solid fa-map-location-dot"></i> Visítanos en Cobán, Alta Verapaz</h2>
                <p>Te esperamos en nuestra sucursal principal equipada con amplio estacionamiento, asesoría técnica
                    especializada y un inventario completo listo para entrega inmediata o recolección en tienda.</p>
                <ul class="ferre-location-list">
                    <li><i class="fa-solid fa-check"></i> Ubicación céntrica y de fácil acceso para vehículos pesados y
                        particulares.</li>
                    <li><i class="fa-solid fa-check"></i> Cobertura de entrega a proyectos residenciales y comerciales
                        en todo el municipio.</li>
                    <li><i class="fa-solid fa-check"></i> Facturación electrónica y atención a cuentas corporativas o
                        constructoras.</li>
                </ul>
            </div>
            <div class="ferre-map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3843.834015694247!2d-90.38139552499127!3d15.47067888514109!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x858913b860827299%3A0x629c42c2be95a5f9!2sCob%C3%A1n%2C%20Alta%20Verapaz%2C%20Guatemala!5e0!3m2!1ses!2sus!4v1710000000000!5m2!1ses!2sus"
                    width="100%" height="240" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 6: Banner de Registro (Visible solo para Invitados) -->
    <?php if (!$isLoggedIn): ?>
        <section class="ferre-auth-banner ferre-fade-in">
            <div class="ferre-auth-text">
                <h3>¿Listo para cotizar y gestionar tus pedidos de forma digital?</h3>
                <p>Crea tu cuenta gratuita en segundos y accede a herramientas pensadas para facilitar tus compras.</p>
            </div>
            <div>
                <a href="<?php echo $directorio_raiz; ?>index.php?vista=registro" class="ferre-btn-light">
                    Crear Cuenta Ahora
                </a>
            </div>
        </section>
    <?php endif; ?>

</div>

<?php
// Incluimos el footer utilizando rutas robustas
include_once __DIR__ . '/../layouts/footer.php';
?>