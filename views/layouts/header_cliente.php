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

$paginaActual = basename($_SERVER['PHP_SELF']);

$isLoggedIn = isset($_SESSION['usuario']) || isset($_SESSION['nombre']) || isset($_SESSION['correo']);
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Invitado';

if ($isLoggedIn) {
    switch ($paginaActual) {
        case 'catalogo.php':
        case 'productos.php':
            $tituloHeader = "🧱 Catálogo de Productos — Encuentra el material ideal para tu obra";
            break;
        case 'carrito.php':
            $tituloHeader = "🛒 Tu Carrito — Revisa tus materiales antes de ordenar";
            break;
        case 'pedidos.php':
            $tituloHeader = "📦 Tus Pedidos — Historial y seguimiento de tus compras";
            break;
        case 'perfil.php':
            $tituloHeader = "👤 Mi Perfil — Actualiza tus datos personales y contraseña";
            break;
        case 'resenas.php':
            $tituloHeader = "⭐ Tus Reseñas — Opiniones de tus productos adquiridos";
            break;
        case 'wishlist.php':
            $tituloHeader = "❤️ Tu Wishlist — Materiales guardados como favoritos";
            break;
        case 'index.php':
            $tituloHeader = "👋 ¡Hola, " . htmlspecialchars($nombreUsuario) . "! Explora nuestros productos";
            break;
        default:
            $tituloHeader = "🛠️ Ferretería El Constructor";
            break;
    }
} else {
    $tituloHeader = "🛠️ Bienvenido a Ferretería El Constructor";
    if ($paginaActual === 'login.php') {
        $tituloHeader = "🔒 Acceso al Sistema — Inicia sesión para continuar";
    } elseif ($paginaActual === 'registro.php') {
        $tituloHeader = "📝 Registro de Cliente — Únete a nuestra plataforma";
    } elseif ($paginaActual === 'catalogo.php') {
        $tituloHeader = "🧱 Catálogo Abierto — Explora nuestros materiales sin cuenta";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/estilos.css">
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            🛠️ Ferretería El Constructor
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo $base_url; ?>views/clientes/index.php"
                    class="<?php echo ($paginaActual === 'index.php') ? 'active' : ''; ?>">
                    🏠 Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/clientes/catalogo.php"
                    class="<?php echo ($paginaActual === 'catalogo.php') ? 'active' : ''; ?>">
                    🧱 Catálogo
                </a>
            </li>
            <?php if ($isLoggedIn): ?>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/carrito.php"
                        class="<?php echo ($paginaActual === 'carrito.php') ? 'active' : ''; ?>">
                        🛒 Carrito
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/pedidos.php"
                        class="<?php echo ($paginaActual === 'pedidos.php') ? 'active' : ''; ?>">
                        📦 Mis Pedidos
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/wishlist.php"
                        class="<?php echo ($paginaActual === 'wishlist.php') ? 'active' : ''; ?>">
                        ❤️ Wishlist
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/resenas.php"
                        class="<?php echo ($paginaActual === 'resenas.php') ? 'active' : ''; ?>">
                        ⭐ Reseñas
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/perfil.php"
                        class="<?php echo ($paginaActual === 'perfil.php') ? 'active' : ''; ?>">
                        👤 Mi Perfil
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/logout.php">
                        🚪 Cerrar Sesión
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo $base_url; ?>views/clientes/carrito.php"
                        class="<?php echo ($paginaActual === 'carrito.php') ? 'active' : ''; ?>">
                        🛒 Carrito
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/login.php"
                        class="<?php echo ($paginaActual === 'login.php') ? 'active' : ''; ?>">
                        🔑 Iniciar Sesión
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>views/registro.php"
                        class="<?php echo ($paginaActual === 'registro.php') ? 'active' : ''; ?>">
                        📝 Registrarse
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <h2>
                <?php echo $tituloHeader; ?>
            </h2>
            <div class="user-profile-info">
                <?php if ($isLoggedIn): ?>
                    <div class="user-info-container">
                        <span class="user-logged">👤
                            <?php echo htmlspecialchars($nombreUsuario); ?>
                        </span>
                        <a href="<?php echo $base_url; ?>views/logout.php" class="header-logout-btn"
                            title="Cerrar Sesión">Salir</a>
                    </div>
                <?php else: ?>
                    <div class="user-info-container">
                        <span class="user-logged">👤 Invitado</span>
                        <a href="<?php echo $base_url; ?>views/login.php" class="header-logout-btn">Ingresar</a>
                    </div>
                <?php endif; ?>
            </div>
        </header>