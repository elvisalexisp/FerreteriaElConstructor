<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Pedido.php';

$pedidoModel = new Pedido();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id_pedido = $_GET['id'] ?? null;
    $id_usuario = $_GET['usuario'] ?? null;

    if ($id_pedido) {
        $pedido = $pedidoModel->obtenerPorId($id_pedido);
        echo json_encode(["status" => "success", "data" => $pedido]);
    } else if ($id_usuario) {
        $pedidos = $pedidoModel->obtenerPorCliente($id_usuario);
        echo json_encode(["status" => "success", "data" => $pedidos]);
    } else {
        $pedidos = $pedidoModel->obtenerTodos();
        echo json_encode(["status" => "success", "data" => $pedidos]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}