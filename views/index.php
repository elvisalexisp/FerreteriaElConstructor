<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/conexion.php';

$rolActual = strtolower(trim($_SESSION['tipo_usuario'] ?? $_SESSION['rol'] ?? ''));
$correoActual = $_SESSION['correo'] ?? '';

if ($correoActual !== 'admin@ferreteria.com' && !in_array($rolActual, ['admin', 'administrador', '1'])) {
    header("Location: ../login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? 'Administrador General';

$pdo = Conexion::conectar();

try {
    $totalProductos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?? 0;
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn() ?? 0;
    $totalPedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn() ?? 0;
} catch (Exception $e) {
    $totalProductos = 0;
    $totalUsuarios = 0;
    $totalPedidos = 0;
}

include __DIR__ . '/../layouts/header_admin.php';
?>

<div class="admin-dashboard-container" style="padding: 20px;">
    <div class="card"
        style="margin-bottom: 25px; padding: 25px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h1 style="color: var(--primary-color, #1e293b); margin-bottom: 10px;">📊 Panel de Control General</h1>
        <p style="font-size: 1.05rem; color: #64748b;">
            Bienvenido de nuevo, <strong>
                <?php echo htmlspecialchars($nombreUsuario); ?>
            </strong>. Aquí tienes un resumen general del sistema y accesos rápidos de administración.
        </p>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card"
            style="padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="color: #64748b; font-size: 0.95rem; margin-bottom: 10px;">Total de Productos</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #0f172a; margin-bottom: 15px;">
                <?php echo $totalProductos; ?>
            </p>
            <a href="<?php echo $base_url; ?>views/admin/productos.php"
                style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver detalles &rarr;</a>
        </div>

        <div class="card"
            style="padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="color: #64748b; font-size: 0.95rem; margin-bottom: 10px;">Usuarios Registrados</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #0f172a; margin-bottom: 15px;">
                <?php echo $totalUsuarios; ?>
            </p>
            <a href="<?php echo $base_url; ?>views/admin/usuarios.php"
                style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver detalles &rarr;</a>
        </div>

        <div class="card"
            style="padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="color: #64748b; font-size: 0.95rem; margin-bottom: 10px;">Pedidos Totales</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #0f172a; margin-bottom: 15px;">
                <?php echo $totalPedidos; ?>
            </p>
            <a href="<?php echo $base_url; ?>views/admin/consultas/index.php"
                style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver detalles &rarr;</a>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="card"
        style="padding: 25px; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 15px; color: #1e293b;">⚡ Accesos Rápidos de Gestión</h3>
        <ul
            style="list-style: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <li><a href="<?php echo $base_url; ?>views/admin/productos.php" class="btn"
                    style="display: block; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; text-decoration: none; font-weight: 500;">🧱
                    Gestionar Productos</a></li>
            <li><a href="<?php echo $base_url; ?>views/admin/categorias.php" class="btn"
                    style="display: block; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; text-decoration: none; font-weight: 500;">🏷️
                    Gestionar Categorías</a></li>
            <li><a href="<?php echo $base_url; ?>views/admin/usuarios.php" class="btn"
                    style="display: block; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; text-decoration: none; font-weight: 500;">👥
                    Gestionar Usuarios</a></li>
            <li><a href="<?php echo $base_url; ?>views/admin/consultas/index.php" class="btn"
                    style="display: block; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; text-decoration: none; font-weight: 500;">📋
                    Revisar Pedidos</a></li>
        </ul>
    </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>