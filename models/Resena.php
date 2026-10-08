<?php
require_once __DIR__ . '/../config/conexion.php';

class Resena
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ? $db : Conexion::conectar();
    }

    // Filtro de palabras ofensivas o discriminatorias
    public function limpiarPalabrasOfensivas($texto)
    {
        // Lista de palabras prohibidas o discriminatorias básicas (puedes ampliarla)
        $palabrasProhibidas = [
            'malo',
            'estupido',
            'idiota',
            'basura',
            'imbecil',
            'mierda',
            'estafa',
            'fraude',
            'odioso',
            'grosero'
            // Agrega aquí más términos según tus políticas
        ];

        // Remplazar las palabras prohibidas por asteriscos o un aviso
        foreach ($palabrasProhibidas as $palabra) {
            $patron = '/' . preg_quote($palabra, '/') . '/ui';
            $texto = preg_replace($patron, '***', $texto);
        }

        return $texto;
    }

    // Obtener todas las reseñas con detalles
    public function obtenerTodas()
    {
        $sql = "SELECT r.*, p.nombre AS producto_nombre, u.nombre AS usuario_nombre 
                FROM resenas r 
                INNER JOIN productos p ON r.id_producto = p.id_producto 
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                ORDER BY r.id_resena DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reseñas por producto específico
    public function obtenerPorProducto($id_producto)
    {
        $stmt = $this->db->prepare("SELECT r.*, u.nombre AS usuario_nombre 
                                    FROM resenas r 
                                    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    WHERE r.id_producto = ? 
                                    ORDER BY r.id_resena DESC");
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear una nueva reseña aplicando el filtro de palabras
    public function crear($id_usuario, $id_producto, $calificacion, $comentario)
    {
        $comentarioFiltrado = $this->limpiarPalabrasOfensivas($comentario);

        $stmt = $this->db->prepare("INSERT INTO resenas (id_usuario, id_producto, calificacion, comentario, fecha) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$id_usuario, $id_producto, $calificacion, $comentarioFiltrado]);
    }

    // Editar una reseña existente aplicando el filtro de palabras
    public function editar($id_resena, $calificacion, $comentario)
    {
        $comentarioFiltrado = $this->limpiarPalabrasOfensivas($comentario);

        $stmt = $this->db->prepare("UPDATE resenas SET calificacion = ?, comentario = ? WHERE id_resena = ?");
        return $stmt->execute([$calificacion, $comentarioFiltrado, $id_resena]);
    }

    // Eliminar una reseña (para moderación)
    public function eliminar($id_resena)
    {
        $stmt = $this->db->prepare("DELETE FROM resenas WHERE id_resena = ?");
        return $stmt->execute([$id_resena]);
    }
}
?>