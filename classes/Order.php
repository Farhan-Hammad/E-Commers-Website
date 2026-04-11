<?php
// classes/Order.php
require_once __DIR__ . '/../config/database.php';

class Order
{
    private $db;
    private $table = 'orders';
    private $itemsTable = 'order_items';

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Create a new order
     */
    public function create($userId, $cartItems, $shippingData, $total)
    {
        try {
            $this->db->beginTransaction();

            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());

            // Insert order
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (user_id, order_number, total_amount, shipping_name, shipping_email, 
                 shipping_phone, shipping_address, shipping_city, shipping_country, 
                 shipping_postal, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                $userId ?: null,
                $orderNumber,
                $total,
                $shippingData['name'],
                $shippingData['email'],
                $shippingData['phone'],
                $shippingData['address'],
                $shippingData['city'],
                $shippingData['country'],
                $shippingData['postal'],
                $shippingData['notes'] ?? null
            ]);

            $orderId = $this->db->lastInsertId();

            // Insert order items
            $itemStmt = $this->db->prepare("
                INSERT INTO {$this->itemsTable} 
                (order_id, product_id, product_name, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal']
                ]);

                // Optionally reduce stock
                $updateStock = $this->db->prepare("
                    UPDATE products SET stock_quantity = stock_quantity - ? 
                    WHERE id = ? AND stock_quantity >= ?
                ");
                $updateStock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            }

            $this->db->commit();

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get order by ID with items
     */
    public function getById($orderId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if ($order) {
            $itemStmt = $this->db->prepare("
                SELECT * FROM {$this->itemsTable} WHERE order_id = ?
            ");
            $itemStmt->execute([$orderId]);
            $order['items'] = $itemStmt->fetchAll();
        }

        return $order;
    }

    /**
     * Get orders for a user
     */
    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
