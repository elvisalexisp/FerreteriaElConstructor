<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "/FerreteriaElConstructor1.0/";

require_once __DIR__ . '/../../config/conexion.php';

$rolActual = strtolower(trim($_SESSION['tipo_usuario'] ?? $_SESSION['rol'] ?? ''));
$correoActual = $_SESSION['correo'] ?? '';

if ($correoActual !== 'admin@ferreteria.com' && !in_array($rolActual, ['admin', 'administrador', '1'])) {
    header("Location: ../login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Administrador General';

$pdo = Conexion::conectar();

// Inicializar variables
$totalProductos = 0;
$totalUsuarios = 0;
$totalPedidos = 0;
$totalCategorias = 0;
$ultimosPedidos = [];

try {
    // Métricas generales del sistema
    $totalProductos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?? 0;
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn() ?? 0;
    $totalPedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn() ?? 0;

    // Verificamos si existe la tabla categorias para prevenir errores
    $checkCat = $pdo->query("SHOW TABLES LIKE 'categorias'")->rowCount();
    if ($checkCat > 0) {
        $totalCategorias = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn() ?? 0;
    }

    // Obtener los últimos 5 pedidos para la tabla de actividad reciente
    $stmtPedidos = $pdo->query("SELECT * FROM pedidos ORDER BY id DESC LIMIT 5");
    $ultimosPedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // En caso de error en la BD, los valores se quedan en 0
}

include __DIR__ . '/../layouts/header_admin.php';
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/adminIndex.css">

<main class="admin-main-container">
    <div class="admin-dashboard-wrapper">
        <div class="admin-header">
            <div class="header-info-content">
                <h1>Panel de Control General</h1>
                <p>
                    Bienvenido de nuevo, <strong>
                        <?php echo htmlspecialchars($nombreUsuario); ?>
                    </strong>.
                    Aquí tienes un resumen general del sistema y accesos rápidos de administración.
                </p>
            </div>
            <div class="header-date-badge">
                <i class="fa-solid fa-calendar-days"></i> <span>
                    <?php echo date('d / m / Y'); ?>
                </span>
            </div>
        </div>

        <!-- Cuadrícula de Estadísticas Ampliada -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-box blue-theme">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>Total de Productos</h3>
                    <p class="stat-number">
                        <?php echo number_format($totalProductos); ?>
                    </p>
                </div>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_productos" class="stat-link">Ver detalles
                    &rarr;</a>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box green-theme">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div class="stat-info">
                    <h3>Categorías</h3>
                    <p class="stat-number">
                        <?php echo number_format($totalCategorias); ?>
                    </p>
                </div>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_categorias" class="stat-link">Ver detalles
                    &rarr;</a>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box purple-theme">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Usuarios Registrados</h3>
                    <p class="stat-number">
                        <?php echo number_format($totalUsuarios); ?>
                    </p>
                </div>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_usuarios" class="stat-link">Ver detalles
                    &rarr;</a>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box orange-theme">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <h3>Pedidos Totales</h3>
                    <p class="stat-number">
                        <?php echo number_format($totalPedidos); ?>
                    </p>
                </div>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_consultas" class="stat-link">Ver detalles
                    &rarr;</a>
            </div>
        </div>

        <!-- Accesos Rápidos de Gestión -->
        <div class="quick-links-card">
            <h3><i class="fa-solid fa-bolt"></i> Accesos Rápidos de Gestión</h3>
            <div class="quick-links-grid">
                <a href="<?php echo $base_url; ?>index.php?vista=admin_productos" class="quick-link-item">
                    <i class="fa-solid fa-box-open"></i> Gestionar Productos
                </a>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_categorias" class="quick-link-item">
                    <i class="fa-solid fa-tags"></i> Gestionar Categorías
                </a>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_usuarios" class="quick-link-item">
                    <i class="fa-solid fa-user-gear"></i> Gestionar Usuarios
                </a>
                <a href="<?php echo $base_url; ?>index.php?vista=admin_consultas" class="quick-link-item">
                    <i class="fa-solid fa-list-check"></i> Revisar Pedidos
                </a>
            </div>
        </div>

        <!-- Sección de Actividad Reciente (Últimos Pedidos) -->
        <div class="recent-activity-card">
            <h3>
                <i class="fa-solid fa-clock-rotate-left"></i> Últimos Pedidos Registrados
            </h3>

            <?php if (!empty($ultimosPedidos)): ?>
                <div class="table-responsive">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Información / Total</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimosPedidos as $pedido): ?>
                                <tr>
                                    <td class="fw-bold">#
                                        <?php echo htmlspecialchars($pedido['id']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Intenta mostrar el total o un texto genérico si la columna varía
                                        echo htmlspecialchars($pedido['total'] ?? 'Ver detalles en sistema');
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $base_url; ?>index.php?vista=admin_consultas"
                                            class="action-btn-table">
                                            Ver <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data-text">No hay pedidos registrados recientemente.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/../layouts/footer.php';
?>