<?php
class Database
{
    private static $conexion = null;

    public static function conectar()
    {
        if (self::$conexion === null) {
            $isLocal = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

            if ($isLocal) {
                $host = "localhost";
                $db = "if0_42657112_ElConstructor_db";
                $usuario = "root";
                $password = "";
            } else {
                $host = "sql211.infinityfree.com";
                $db = "if0_42657112_ElConstructor_db";
                $usuario = "if0_42657112";
                $password = "tqZWhxXg20klOJ9";
            }

            try {
                self::$conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $usuario, $password);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conexion;
    }
}
?>