<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']) || isset($_SESSION['id_usuario']);

include '../layouts/header_cliente.php';
?>

<link rel="stylesheet" href="../assets/css/index_cliente.css">

<div class="cliente-home-container">

    <!-- Hero Section / Banner Principal -->
    <section class="hero-banner">
        <div class="hero-content">
            <span class="hero-badge">🛠️ Distribuidor Autorizado de Calidad</span>
            <h1 class="hero-title">Ferretería El Constructor</h1>
            <p class="hero-subtitle">
                Tu aliado confiable en materiales de construcción, herramientas eléctricas y accesorios profesionales.
                Todo lo que necesitas para tus proyectos de obra gris y acabados en un solo lugar.
            </p>
            <div class="hero-actions">
                <a href="catalogo.php" class="btn-cta-primary">
                    📦 Explorar Catálogo
                </a>
                <a href="carrito.php" class="btn-cta-secondary">
                    🛒 Ver Carrito y Compras
                </a>
            </div>
        </div>
    </section>

    <section class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Herramientas de Calidad</h3>
            <p>Encuentra taladros, sierras y equipos de poder de las mejores marcas para garantizar resultados
                profesionales en cada obra.</p>
            <a href="catalogo.php?categoria=herramientas" class="feature-link">Ver herramientas &rarr;</a>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🧱</div>
            <h3>Materiales Pesados</h3>
            <p>Todo lo necesario para cimientos, estructuras, cemento y acabados con la máxima resistencia y durabilidad
                del mercado.</p>
            <a href="catalogo.php?categoria=materiales" class="feature-link">Ver materiales &rarr;</a>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🚚</div>
            <h3>Envíos y Pedidos Ágiles</h3>
            <p>Gestiona tus compras en línea de forma rápida, segura y lleva el control del historial completo de tus
                pedidos y cotizaciones.</p>
            <a href="carrito.php" class="feature-link">Ir al carrito &rarr;</a>
        </div>
    </section>

    <?php if (!$isLoggedIn): ?>
        <section class="auth-promo-card">
            <div class="auth-promo-text">
                <h3>¿Aún no tienes una cuenta en Ferretería El Constructor?</h3>
                <p>Regístrate hoy para guardar tus productos favoritos, realizar cotizaciones y dar seguimiento a tus
                    pedidos de forma personalizada.</p>
            </div>
            <div class="auth-promo-action">
                <a href="../registro.php" class="btn-registro-destacado">
                    📝 Crear una cuenta gratis
                </a>
            </div>
        </section>
    <?php endif; ?>

</div>

<?php
include '../layouts/footer.php';
?>