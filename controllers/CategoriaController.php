<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
{
    public function listar()
    {
        try {
            return Categoria::obtenerTodas();
        } catch (Exception $e) {
            return [];
        }
    }
}
?>