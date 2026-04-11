<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Cart.php';

header('Content-Type: application/json');

$cart = new Cart();
echo json_encode([
    'items' => $cart->getItems(),
    'count' => $cart->count(),
    'subtotal' => $cart->subtotal(),
    'total' => $cart->total()
]);
