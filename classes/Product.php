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
        SELECT p.*, c.name as category_name, c.slug as category_slug 
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
        // Handle count-only mode when second param is true (for new products.php)
        $countOnly = ($page === true);

        $where = ["p.status = 'active'"];
        $params = [];

        // Category filter (supports both 'category' slug and 'category_slug')
        $categorySlug = $filters['category_slug'] ?? $filters['category'] ?? null;
        if (!empty($categorySlug)) {
            $where[] = "c.slug = ?";
            $params[] = $categorySlug;
        }

        // Search
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Price range (handles sale price logic)
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $where[] = "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price < p.price THEN p.sale_price ELSE p.price END) >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $where[] = "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price < p.price THEN p.sale_price ELSE p.price END) <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $whereClause = implode(' AND ', $where);

        // --- COUNT QUERY (if countOnly or we need total for pagination) ---
        $countSql = "SELECT COUNT(*) FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        if ($countOnly) {
            return $total;
        }

        // --- SORTING ---
        $sort = $filters['sort'] ?? 'newest';
        $orderBy = match ($sort) {
            'price_asc'  => "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price < p.price THEN p.sale_price ELSE p.price END) ASC",
            'price_desc' => "(CASE WHEN p.sale_price IS NOT NULL AND p.sale_price < p.price THEN p.sale_price ELSE p.price END) DESC",
            'name_asc'   => "p.name ASC",
            'name_desc'  => "p.name DESC",
            default      => "p.created_at DESC"
        };

        // --- PAGINATION ---
        // Support both new style (limit/offset) and old style (page/perPage)
        if (isset($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)($filters['offset'] ?? 0);
        } else {
            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $limit = $perPage;
            $offset = ($page - 1) * $perPage;
        }

        // --- MAIN QUERY ---
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE $whereClause 
            ORDER BY $orderBy 
            LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($params, [$limit, $offset]));
        $products = $stmt->fetchAll();

        // For backward compatibility with old calling style that expects array with pagination info
        if (!isset($filters['limit']) && func_num_args() >= 2) {
            return [
                'products'      => $products,
                'total'         => $total,
                'pages'         => ceil($total / $perPage),
                'current_page'  => $page
            ];
        }

        return $products;
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
