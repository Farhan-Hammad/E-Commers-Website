<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Cart.php';
require_once '../../classes/Order.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$cart = new Cart();
$cartItems = $cart->getItems();

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Validate required fields
$required = ['name', 'email', 'phone', 'address', 'city', 'country', 'postal'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "$field is required"]);
        exit;
    }
}

$shippingData = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'address' => $_POST['address'],
    'city' => $_POST['city'],
    'country' => $_POST['country'],
    'postal' => $_POST['postal'],
    'notes' => $_POST['notes'] ?? null
];

$total = $cart->subtotal();
$userId = $_SESSION['user_id'] ?? null;

$order = new Order();
$result = $order->create($userId, $cartItems, $shippingData, $total);

if ($result['success']) {
    // Clear cart after successful order
    $cart->clear();
}

echo json_encode($result);
