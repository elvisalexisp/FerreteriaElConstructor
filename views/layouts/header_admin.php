<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Seguridad estricta de rol
if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'subadmin'])) {
    header("Location: " . $directorio_raiz . "index.php?vista=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo | Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/estilos.css">
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/headerAdmin.css">
    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/<?php echo $extra_css; ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="<?php echo $directorio_raiz; ?>assets/js/app.js" defer></script>
</head>

<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Capa oscura de fondo para móviles (Overlay) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar de Navegación Admin -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <div class="brand-texts">
                    <h2>El Constructor</h2>
                    <span>Panel Admin</span>
                </div>
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" title="Contraer/Expandir menú">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin" title="Dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_productos" title="Productos">
                        <i class="fas fa-boxes"></i>
                        <span class="menu-text">Productos</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_categorias" title="Categorías">
                        <i class="fas fa-tags"></i>
                        <span class="menu-text">Categorías</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_usuarios" title="Usuarios">
                        <i class="fas fa-users"></i>
                        <span class="menu-text">Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=admin_consultas"
                        title="Consultas / Pedidos">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="menu-text">Consultas / Pedidos</span>
                    </a>
                </li>
                <li class="sidebar-logout">
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=logout" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="menu-text">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Contenido Principal Admin -->
        <div class="admin-main-content">
            <header class="admin-topbar">
                <!-- Botón hamburguesa exclusivo para móviles -->
                <button type="button" class="mobile-sidebar-toggle" id="mobileSidebarToggle"
                    aria-label="Abrir menú móvil">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="admin-topbar-title">
                    <h3>Bienvenido,
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                        (<?php echo ucfirst($_SESSION['tipo_usuario']); ?>)
                    </h3>
                </div>
                <div class="admin-topbar-actions">
                    <a href="<?php echo $directorio_raiz; ?>index.php" target="_blank" class="btn-view-site">
                        <i class="fas fa-globe"></i> <span>Ver Sitio Web</span>
                    </a>
                </div>
            </header>