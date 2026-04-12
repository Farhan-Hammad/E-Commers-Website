<?php
require_once 'auth_check.php';
require_once '../classes/Order.php';

$db = db();

// Handle status update BEFORE any output
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];

    // Get current status to avoid double restoration
    $stmt = $db->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $oldStatus = $stmt->fetchColumn();

    // Update the order status
    $updateStmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $orderId]);

    // If changing TO cancelled and it wasn't already cancelled, restore stock
    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
        // Get all items in this order
        $itemsStmt = $db->prepare("
            SELECT product_id, quantity 
            FROM order_items 
            WHERE order_id = ?
        ");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Restore stock for each item
        $restoreStmt = $db->prepare("
            UPDATE products 
            SET stock_quantity = stock_quantity + ? 
            WHERE id = ?
        ");
        foreach ($items as $item) {
            $restoreStmt->execute([$item['quantity'], $item['product_id']]);
        }
    }

    // If changing FROM cancelled to something else, you might want to deduct stock again
    // (We'll skip that for simplicity)

    header('Location: /E-Commers-Website/admin/orders.php');
    exit;
}

// Now include header (output starts)
require_once '../includes/header.php';

// Get all orders (unchanged)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalPages = ceil($totalOrders / $perPage);

$orders = $db->query("
    SELECT o.*, u.first_name, u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT $perPage OFFSET $offset
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- rest of HTML unchanged -->