<?php
// Validar si hay una sesión iniciada con privilegios de administrador
if (isset($_SESSION['tipo_usuario']) && in_array($_SESSION['tipo_usuario'], ['admin', 'subadmin'])) {
    // Asegurarnos de no mostrar este botón flotante DENTRO de las vistas del panel admin (si ya tienen su propia barra)
    $vistaActual = $_GET['vista'] ?? 'home';
    $esVistaAdmin = in_array($vistaActual, ['admin', 'admin_productos', 'admin_categorias', 'admin_usuarios', 'admin_consultas']);

    if (!$esVistaAdmin) {
        $urlAdmin = $directorio_raiz . "index.php?vista=admin";
        ?>
        <!-- Botón Flotante de Retorno al Panel Admin -->
        <div class="admin-floating-bar">
            <a href="<?php echo $urlAdmin; ?>" class="btn-regresar-admin" title="Volver al Panel Administrativo">
                <i class="fa-solid fa-user-shield"></i>
                <span>Regresar al Panel Admin</span>
            </a>
        </div>

        <style>
            .admin-floating-bar {
                position: fixed;
                bottom: 25px;
                right: 25px;
                z-index: 99999;
                animation: fadeInPulse 0.4s ease-in-out;
            }

            .btn-regresar-admin {
                display: flex;
                align-items: center;
                gap: 10px;
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: #ffffff;
                padding: 12px 20px;
                border-radius: 50px;
                font-family: inherit;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                box-shadow: 0 4px 15px rgba(217, 119, 6, 0.4);
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border: 2px solid rgba(255, 255, 255, 0.2);
            }

            .btn-regresar-admin i {
                font-size: 18px;
            }

            .btn-regresar-admin:hover {
                background: linear-gradient(135deg, #d97706, #b45309);
                transform: translateY(-3px) scale(1.02);
                box-shadow: 0 6px 20px rgba(217, 119, 6, 0.6);
                color: #ffffff;
            }

            @keyframes fadeInPulse {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        <?php
    }
}
?>