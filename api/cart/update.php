<?php
session_start();

require_once '../../classes/User.php';
$user = new User();
if (!$user->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in']);
    exit;
}
require_once '../../config/database.php';
require_once '../../classes/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$productId = $input['product_id'] ?? null;
$quantity = $input['quantity'] ?? 0;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

$cart = new Cart();
$result = $cart->update($productId, $quantity);
echo json_encode($result);
