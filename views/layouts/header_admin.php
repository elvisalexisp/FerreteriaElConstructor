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

$isAdmin = false;
if (
    (isset($_SESSION['correo']) && $_SESSION['correo'] === 'admin@ferreteria.com') ||
    (isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'admin@ferreteria.com')
) {
    $isAdmin = true;
} else {
    $rol = $_SESSION['rol'] ?? $_SESSION['tipo_usuario'] ?? '';
    $rolLower = strtolower(trim($rol));
    if (in_array($rolLower, ['admin', 'administrador', '1'])) {
        $isAdmin = true;
    }
}

if (!$isAdmin) {
    header("Location: " . $base_url . "views/login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Administrador';
$paginaActual = basename($_SERVER['PHP_SELF']);

$esDashboardAdmin = ($paginaActual === 'index.php' && strpos($_SERVER['PHP_SELF'], 'consultas') === false);

switch ($paginaActual) {
    case 'admin.php':
    case 'index.php':
        if (strpos($_SERVER['PHP_SELF'], 'consultas') !== false) {
            $tituloHeader = "📋 Centro Avanzado de Consultas y Reportes";
        } else {
            $tituloHeader = "⚙️ Panel de Administración — Gestión Global del Sistema";
        }
        break;
    case 'productos.php':
        $tituloHeader = "🧱 Gestión de Catálogo — Administra y controla tus productos";
        break;
    case 'categorias.php':
        $tituloHeader = "🏷️ Gestión de Categorías — Clasificación del inventario";
        break;
    case 'usuarios.php':
        $tituloHeader = "👥 Gestión de Usuarios — Control de cuentas y roles";
        break;
    case 'pedidos.php':
        $tituloHeader = "📦 Supervisión de Pedidos — Control general de compras";
        break;
    case 'perfil.php':
        $tituloHeader = "👤 Mi Perfil — Configuración de cuenta de Administrador";
        break;
    default:
        $tituloHeader = "🚀 Panel de Control — Ferretería El Constructor";
        break;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Ferretería El Constructor</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/estilos.css">
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            🛠️ Panel Administrador
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo $base_url; ?>views/admin/index.php"
                    class="<?php echo $esDashboardAdmin ? 'active' : ''; ?>">
                    📊 Dashboard Admin
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/admin/productos.php"
                    class="<?php echo ($paginaActual === 'productos.php') ? 'active' : ''; ?>">
                    🧱 Administrar Productos
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/admin/categorias.php"
                    class="<?php echo ($paginaActual === 'categorias.php') ? 'active' : ''; ?>">
                    🏷️ Administrar Categorías
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/admin/usuarios.php"
                    class="<?php echo ($paginaActual === 'usuarios.php') ? 'active' : ''; ?>">
                    👥 Administrar Usuarios
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/admin/consultas/index.php"
                    class="<?php echo (strpos($_SERVER['PHP_SELF'], 'consultas') !== false) ? 'active' : ''; ?>">
                    📋 Supervisar Pedidos / Consultas
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>views/logout.php">
                    🚪 Cerrar Sesión
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <h2>
                <?php echo $tituloHeader; ?>
            </h2>
            <div class="user-profile-info">
                <div class="user-info-container">
                    <span class="user-logged">🛡️ Admin:
                        <?php echo htmlspecialchars($nombreUsuario); ?>
                    </span>
                    <a href="<?php echo $base_url; ?>views/logout.php" class="header-logout-btn"
                        title="Cerrar Sesión">Salir</a>
                </div>
            </div>
        </header>