<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ? $db : Conexion::conectar();
    }

    public function obtenerTodos()
    {
        $stmt = $this->db->query("SELECT id_usuario, nombre, apellido, correo, foto, telefono, direccion, tipo_usuario FROM usuarios ORDER BY id_usuario DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT id_usuario, nombre, apellido, correo, foto, telefono, direccion, tipo_usuario FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCorreo($correo)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $apellido, $correo, $password, $telefono, $direccion, $tipo_usuario)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, apellido, correo, password, telefono, direccion, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $apellido, $correo, $passwordHash, $telefono, $direccion, $tipo_usuario]);
    }

    public function actualizar($id_usuario, $nombre, $apellido, $correo, $telefono, $direccion, $tipo_usuario)
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, telefono = ?, direccion = ?, tipo_usuario = ? WHERE id_usuario = ?");
        return $stmt->execute([$nombre, $apellido, $correo, $telefono, $direccion, $tipo_usuario, $id_usuario]);
    }

    public function actualizarPassword($id_usuario, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
        return $stmt->execute([$passwordHash, $id_usuario]);
    }

    public function eliminar($id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        return $stmt->execute([$id_usuario]);
    }
}
?>