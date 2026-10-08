<?php
// Iniciar sesión para control de usuarios y carrito
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir la ruta raíz absoluta de forma dinámica y segura
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$directorio_raiz = $protocolo . "://" . $host . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

// Incluir archivo de conexión global
require_once __DIR__ . '/config/conexion.php';

// Obtener la vista o el controlador solicitados por la URL
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'home';
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// 1. Manejo de Controladores Clásicos (MVC)
if ($controllerName) {
    $controllerClassName = ucfirst($controllerName) . 'Controller';
    $controllerFile = __DIR__ . '/controllers/' . $controllerClassName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerClassName)) {
            $controller = new $controllerClassName();
            if (method_exists($controller, $action)) {
                $controller->$action();
                exit;
            }
        }
    }
    http_response_code(404);
    echo "<h1>Error 404: Controlador o acción no encontrada</h1>";
    exit;
}

// 2. Mapeo completo de Vistas del Sistema
$fileToInclude = null;

switch ($vista) {
    // --- VISTAS PÚBLICAS ---
    case 'home':
    case 'index':
        $fileToInclude = __DIR__ . '/views/clientes/index.php';
        break;
    case 'catalogo':
        $fileToInclude = __DIR__ . '/views/clientes/catalogo.php';
        break;
    case 'login':
        $fileToInclude = __DIR__ . '/views/auth/login.php';
        break;
    case 'registro':
        $fileToInclude = __DIR__ . '/views/auth/registro.php';
        break;
    case 'logout':
        $fileToInclude = __DIR__ . '/views/auth/logout.php';
        break;
    case 'recuperar':
        $fileToInclude = __DIR__ . '/views/auth/recuperar.php';
        break;
    case 'procesar_recuperacion':
        $fileToInclude = __DIR__ . '/views/auth/procesar_recuperacion.php';
        break;

    // --- VISTAS PROTEGIDAS: CLIENTES ---
    case 'carrito':
    case 'actualizar_carrito':
    case 'wishlist':
    case 'ajax_wishlist':
    case 'perfil':
    case 'pedidos':
    case 'resenas':
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?vista=login");
            exit();
        }

        if ($vista == 'actualizar_carrito') {
            $fileToInclude = __DIR__ . '/views/clientes/actualizar_carrito.php';
        } elseif ($vista == 'ajax_wishlist') {
            $fileToInclude = __DIR__ . '/views/clientes/ajax_wishlist.php';
        } else {
            $fileToInclude = __DIR__ . "/views/clientes/{$vista}.php";
        }
        break;

    // --- VISTAS PROTEGIDAS: ADMINISTRACIÓN ---
    case 'admin':
    case 'admin_productos':
    case 'admin_categorias':
    case 'admin_usuarios':
    case 'admin_consultas':
        if (!isset($_SESSION['tipo_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'subadmin'])) {
            header("Location: index.php?vista=login");
            exit();
        }

        if ($vista == 'admin') {
            $fileToInclude = __DIR__ . '/views/admin/index.php';
        } else {
            $subFile = str_replace('admin_', '', $vista) . '.php';
            $fileToInclude = __DIR__ . "/views/admin/{$subFile}";
        }
        break;

    default:
        http_response_code(404);
        $fileToInclude = null;
        break;
}

// 3. Cargar la vista correspondiente o mostrar Error 404
if ($fileToInclude && file_exists($fileToInclude)) {
    include_once $fileToInclude;
} else {
    http_response_code(404);
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
    echo "<h1>404 - Página no encontrada</h1>";
    echo "<p>La ruta solicitada no existe o el archivo correspondiente no fue encontrado en el servidor.</p>";
    echo "<a href='index.php' style='color: #0066cc; text-decoration: none; font-weight: bold;'>Volver al Inicio</a>";
    echo "</div>";
}
?>