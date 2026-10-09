<?php
class Conexion
{
    public static function conectar()
    {
        $host = "localhost";
        $db = "ferreteria-constructor1";
        $user = "root";
        $pass = "";

        try {
            $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>