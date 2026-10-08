<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$es_local = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
$directorio_raiz = $es_local ? "/FerreteriaElConstructor1.0/" : "/";

$vista_actual = isset($_GET['vista']) ? $_GET['vista'] : 'home';
$es_catalogo_o_home = ($vista_actual === 'catalogo');

$usuario_logueado = isset($_SESSION['id_usuario']) || isset($_SESSION['id']) || isset($_SESSION['user_id']) || isset($_SESSION['email']);
$nombre_usuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? $_SESSION['user_name'] ?? 'Cliente';
$foto_usuario = $_SESSION['foto'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($page_title) ? $page_title : "Ferretería El Constructor | Materiales y Herramientas en Cobán"; ?>
    </title>
    <meta name="description"
        content="<?php echo isset($page_desc) ? $page_desc : "Tu ferretería de confianza en Cobán, Alta Verapaz. Venta de materiales de construcción, herramientas eléctricas, plomería, electricidad y acabados profesionales al mejor precio."; ?>">
    <meta name="keywords"
        content="ferreteria en coban, materiales de construccion coban, herramientas, ferreteria el constructor, plomeria, electricidad alta verapaz">
    <meta name="author" content="Ferretería El Constructor">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="Ferretería El Constructor | Todo para tu Construcción en Cobán">
    <meta property="og:description"
        content="Encuentra herramientas profesionales, materiales de construcción y asesoría experta en Cobán, Alta Verapaz.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_GT">

    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/estilos.css">
    <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/headerCliente.css">

    <?php if ($vista_actual === 'catalogo'): ?>
        <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/catalogo.css">
    <?php elseif ($vista_actual === 'home' || $vista_actual === 'index'): ?>
        <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/indexCliente.css">
    <?php endif; ?>

    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?php echo $directorio_raiz; ?>assets/css/<?php echo $extra_css; ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="<?php echo $directorio_raiz; ?>assets/js/app.js"></script>
</head>

<body class="cliente-body">
    <header class="cliente-header">
        <!-- Barra Superior de Utilidad (Visible en PC) -->
        <div class="header-top-bar">
            <div class="header-container">
                <span class="welcome-text"><i class="fas fa-tools"></i> Bienvenidos a Ferretería El Constructor - Cobán,
                    Alta Verapaz</span>
                <div class="top-bar-links">
                    <?php if ($usuario_logueado): ?>
                        <span class="user-greeting">Hola, <strong>
                                <?php echo htmlspecialchars($nombre_usuario); ?>
                            </strong></span>

                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=perfil"
                            class="top-link <?php echo ($vista_actual == 'perfil') ? 'active' : ''; ?>">
                            <?php if (!empty($foto_usuario) && file_exists($_SERVER['DOCUMENT_ROOT'] . $directorio_raiz . "uploads/" . $foto_usuario)): ?>
                                <img src="<?php echo $directorio_raiz; ?>uploads/<?php echo htmlspecialchars($foto_usuario); ?>"
                                    alt="Miniatura" class="user-avatar-mini">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                            Mi Perfil
                        </a>

                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=logout" class="top-link logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=login"
                            class="top-link <?php echo ($vista_actual == 'login') ? 'active' : ''; ?>"><i
                                class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                        <a href="<?php echo $directorio_raiz; ?>index.php?vista=registro"
                            class="top-link <?php echo ($vista_actual == 'registro') ? 'active' : ''; ?>"><i
                                class="fas fa-user-plus"></i> Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navegación Principal y Marca -->
        <div class="header-main-nav">
            <div class="header-container">
                <div class="logo-brand">
                    <a href="<?php echo $directorio_raiz; ?>index.php">
                        <h1>El Constructor</h1>
                        <span>Ferretería & Construcción</span>
                    </a>
                </div>

                <nav class="main-navigation">
                    <ul>
                        <li><a href="<?php echo $directorio_raiz; ?>index.php"
                                class="<?php echo ($vista_actual == 'home' || $vista_actual == 'index') ? 'active' : ''; ?>">Inicio</a>
                        </li>
                        <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=catalogo"
                                class="<?php echo ($vista_actual == 'catalogo') ? 'active' : ''; ?>">Catálogo</a></li>

                        <?php if ($usuario_logueado): ?>
                            <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=pedidos"
                                    class="<?php echo ($vista_actual == 'pedidos') ? 'active' : ''; ?>"><i
                                        class="fas fa-box"></i> Mis Pedidos</a></li>
                            <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=wishlist"
                                    class="<?php echo ($vista_actual == 'wishlist') ? 'active' : ''; ?>"><i
                                        class="fas fa-heart"></i> Wishlist</a></li>
                            <li><a href="<?php echo $directorio_raiz; ?>index.php?vista=resenas"
                                    class="<?php echo ($vista_actual == 'resenas') ? 'active' : ''; ?>"><i
                                        class="fas fa-star"></i> Reseñas</a></li>

                            <!-- Opciones de cuenta adaptadas para el menú móvil -->
                            <li class="mobile-only-link"><a href="<?php echo $directorio_raiz; ?>index.php?vista=perfil"
                                    class="<?php echo ($vista_actual == 'perfil') ? 'active' : ''; ?>"><i
                                        class="fas fa-user"></i> Mi Perfil</a></li>
                            <li class="mobile-only-link"><a href="<?php echo $directorio_raiz; ?>index.php?vista=logout"
                                    style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li class="mobile-only-link"><a href="<?php echo $directorio_raiz; ?>index.php?vista=login"><i
                                        class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                            <li class="mobile-only-link"><a
                                    href="<?php echo $directorio_raiz; ?>index.php?vista=registro"><i
                                        class="fas fa-user-plus"></i> Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="header-actions">
                    <a href="<?php echo $directorio_raiz; ?>index.php?vista=carrito"
                        class="cart-icon-btn <?php echo ($vista_actual == 'carrito') ? 'active' : ''; ?>"
                        title="Ver Carrito de Compras">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>

        <?php if ($es_catalogo_o_home): ?>
            <div class="header-extended-bar">
                <div class="header-container">
                    <div class="header-search-filter-wrapper" style="justify-content: center; width: 100%;">
                        <form action="<?php echo $directorio_raiz; ?>index.php" method="GET" class="header-search-form"
                            style="width: 100%; max-width: 700px;">
                            <input type="hidden" name="vista" value="catalogo">
                            <input type="text" name="busqueda" placeholder="¿Qué herramienta o material buscas hoy?"
                                class="search-input" autocomplete="off">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i> Buscar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>