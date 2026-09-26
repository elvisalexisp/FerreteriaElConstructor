<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($isLocal) {
    $base_url = "http://localhost/FerreteriaElConstructor/";
} else {
    $base_url = "https://ferreteriaelconstructor.gt.tc/";
}

require_once __DIR__ . '/../../models/Resena.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    $calificacion = intval($_POST['calificacion'] ?? 5);
    $id_usuario = $_SESSION['usuario'];

    if (!empty($comentario)) {
        if (Resena::crear($id_usuario, $comentario, $calificacion)) {
            $mensaje = "⭐ ¡Gracias por tu opinión! Tu reseña ha sido publicada con éxito.";
        } else {
            $error = "❌ Ocurrió un error al guardar tu reseña. Inténtalo de nuevo.";
        }
    } else {
        $error = "⚠️ El campo de comentario no puede estar vacío.";
    }
}

$resenas = Resena::obtenerTodas();

include __DIR__ . '/../layouts/header_cliente.php';
?>

<div class="main-content-container" style="padding: 25px; max-width: 900px; margin: 0 auto;">
    <h2 style="color: #1e293b; margin-bottom: 20px;">💬 Opiniones y Reseñas de Clientes</h2>

    <?php if (!empty($mensaje)): ?>
        <div
            style="background: #dcfce7; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div
            style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card"
        style="background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <h3 style="color: #1e293b; margin-bottom: 15px; font-size: 1.1rem;">✍️ Déjanos tu comentario</h3>
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label
                    style="display: block; font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">Calificación:</label>
                <select name="calificacion" class="form-control"
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
                    <option value="5">⭐⭐⭐⭐⭐ (5/5 - Excelente)</option>
                    <option value="4">⭐⭐⭐⭐ (4/5 - Muy bueno)</option>
                    <option value="3">⭐⭐⭐ (3/5 - Bueno)</option>
                    <option value="2">⭐⭐ (2/5 - Regular)</option>
                    <option value="1">⭐ (1/5 - Malo)</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">Tu Experiencia /
                    Comentario:</label>
                <textarea name="comentario" rows="3" required
                    placeholder="Cuéntanos sobre la atención, calidad de los materiales o envíos..."
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
            </div>

            <button type="submit" class="btn"
                style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Publicar
                Reseña</button>
        </form>
    </div>

    <h3 style="color: #1e293b; margin-bottom: 15px;">🗣️ Lo que dicen nuestros clientes</h3>

    <?php if (empty($resenas)): ?>
        <div class="card"
            style="padding: 30px; text-align: center; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="color: #64748b;">Aún no hay reseñas publicadas. ¡Sé el primero en dejarnos tu opinión!</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($resenas as $r): ?>
                <div class="card"
                    style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="color: #0f172a; font-size: 1rem; margin: 0;">
                            <?php echo htmlspecialchars($r['nombre'] . ' ' . $r['apellido']); ?>
                        </h4>
                        <span style="color: #eab308; font-size: 0.9rem;">
                            <?php
                            $estrellas = intval($r['calificacion']);
                            echo str_repeat('⭐', $estrellas);
                            ?>
                        </span>
                    </div>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 10px;">
                        <?php echo nl2br(htmlspecialchars($r['comentario'])); ?>
                    </p>
                    <span style="font-size: 0.8rem; color: #94a3b8;"><?php echo $r['fecha']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>