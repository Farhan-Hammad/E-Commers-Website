<?php
// classes/Cart.php

class Cart
{
    private $db;
    private $sessionKey = 'cart';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    /**
     * Add item to cart
     */
    public function add($productId, $quantity = 1)
    {
        $productId = (int)$productId;
        $quantity = max(1, (int)$quantity);

        // Validate product exists and is active
        $stmt = $this->db->prepare("SELECT id, stock_quantity FROM products WHERE id = ? AND status = 'active'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not available'];
        }

        // Check stock
        $currentQty = $_SESSION[$this->sessionKey][$productId] ?? 0;
        $newQty = $currentQty + $quantity;
        if ($newQty > $product['stock_quantity']) {
            return ['success' => false, 'message' => 'Not enough stock'];
        }

        $_SESSION[$this->sessionKey][$productId] = $newQty;
        return [
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $this->count(),
            'cart_total' => $this->total()
        ];
    }

    /**
     * Update item quantity
     */
    public function update($productId, $quantity)
    {
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        // Validate stock
        $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE id = ? AND status = 'active'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($quantity > $product['stock_quantity']) {
            return ['success' => false, 'message' => 'Exceeds available stock'];
        }

        $_SESSION[$this->sessionKey][$productId] = $quantity;
        return ['success' => true, 'message' => 'Cart updated'];
    }

    /**
     * Remove item from cart
     */
    public function remove($productId)
    {
        $productId = (int)$productId;
        if (isset($_SESSION[$this->sessionKey][$productId])) {
            unset($_SESSION[$this->sessionKey][$productId]);
        }
        return ['success' => true, 'message' => 'Item removed'];
    }

    /**
     * Get all cart items with product details
     */
    public function getItems()
    {
        if (empty($_SESSION[$this->sessionKey])) {
            return [];
        }

        $ids = array_keys($_SESSION[$this->sessionKey]);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id IN ($placeholders) AND p.status = 'active'
        ");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        foreach ($products as $product) {
            $productId = $product['id'];
            $quantity = $_SESSION[$this->sessionKey][$productId];
            $price = !empty($product['sale_price']) && $product['sale_price'] < $product['price']
                ? $product['sale_price']
                : $product['price'];

            $items[] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'price' => $price,
                'original_price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'stock' => $product['stock_quantity'],
                'image' => json_decode($product['images'] ?? '[]', true)[0] ?? 'assets/images/placeholder.jpg'
            ];
        }
        return $items;
    }

    /**
     * Get total number of items in cart
     */
    public function count()
    {
        return array_sum($_SESSION[$this->sessionKey]);
    }

    /**
     * Get cart subtotal
     */
    public function subtotal()
    {
        $items = $this->getItems();
        return array_reduce($items, function ($sum, $item) {
            return $sum + $item['subtotal'];
        }, 0);
    }

    /**
     * Get cart total (subtotal + tax + shipping - discounts)
     */
    public function total()
    {
        // For now, just subtotal (tax/shipping added at checkout)
        return $this->subtotal();
    }

    /**
     * Clear the cart
     */
    public function clear()
    {
        $_SESSION[$this->sessionKey] = [];
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty()
    {
        return empty($_SESSION[$this->sessionKey]);
    }
}
