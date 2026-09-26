<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProductoController
{

    public static function listarCatalogo($busqueda = '', $id_categoria = 0, $orden = 'recientes')
    {
        try {
            $pdo = Database::conectar();
            $sql = "SELECT * FROM productos WHERE 1=1";
            $params = [];

            if (!empty($busqueda)) {
                $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
                $params[] = "%$busqueda%";
                $params[] = "%$busqueda%";
            }

            if ($id_categoria > 0) {
                $sql .= " AND id_categoria = ?";
                $params[] = $id_categoria;
            }

            if ($orden === 'precio_asc') {
                $sql .= " ORDER BY precio ASC";
            } elseif ($orden === 'precio_desc') {
                $sql .= " ORDER BY precio DESC";
            } else {
                $sql .= " ORDER BY id_producto DESC";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>