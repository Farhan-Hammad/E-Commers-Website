<?php
// classes/Product.php

require_once __DIR__ . '/../config/database.php';

class Product
{
    private $db;
    private $table = 'products';

    public function __construct()
    {
        $this->db = db();
    }

    // Get featured products
    public function getFeatured($limit = 8)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.featured = 1 AND p.status = 'active' 
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // Get new arrivals
    public function getNewArrivals($limit = 4)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' 
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // Get single product by slug
    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.slug = ? AND p.status = 'active' 
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    // Get all products with filters
    public function getAll($filters = [], $page = 1, $perPage = 12)
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = "c.slug = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= ?";
            $params[] = $filters['max_price'];
        }

        $whereClause = implode(' AND ', $where);

        // Get total count
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE $whereClause
        ");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        // Get products
        $offset = ($page - 1) * $perPage;
        $orderBy = !empty($filters['sort']) && $filters['sort'] === 'price'
            ? 'p.price ASC'
            : 'p.created_at DESC';

        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE $whereClause 
            ORDER BY $orderBy 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute(array_merge($params, [$perPage, $offset]));

        return [
            'products' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => ceil($total / $perPage),
            'current_page' => $page
        ];
    }

    // Create product (admin)
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (category_id, name, slug, description, short_description, price, sale_price, stock_quantity, sku, status, featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'] ?? null,
            $data['price'],
            $data['sale_price'] ?? null,
            $data['stock_quantity'] ?? 0,
            $data['sku'],
            $data['status'] ?? 'active',
            $data['featured'] ?? false
        ]);

        return $this->db->lastInsertId();
    }

    // Update product (admin)
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        $allowed = [
            'category_id',
            'name',
            'slug',
            'description',
            'short_description',
            'price',
            'sale_price',
            'stock_quantity',
            'sku',
            'status',
            'featured'
        ];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    // Delete product (admin)
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
