<?php
// classes/User.php

require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = db();
    }

    // ==================== REGISTRATION ====================

    public function register($data)
    {
        // Validate required fields
        $required = ['email', 'password', 'first_name', 'last_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "$field is required"];
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => "Invalid email format"];
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => "Email already registered"];
        }

        // Validate password strength
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => "Password must be at least 6 characters"];
        }

        // Hash password securely
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        // Insert new user
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (email, password_hash, first_name, last_name, phone, address, city, country, postal_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['email'],
                $passwordHash,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null
            ]);

            $userId = $this->db->lastInsertId();

            return [
                'success' => true,
                'message' => "Registration successful",
                'user_id' => $userId
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Registration failed: " . $e->getMessage()];
        }
    }

    // ==================== LOGIN ====================

    public function login($email, $password)
    {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => "Email and password are required"];
        }

        // Find user by email
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => "Invalid email or password"];
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => "Invalid email or password"];
        }

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['logged_in'] = true;

        return [
            'success' => true,
            'message' => "Login successful",
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'is_admin' => $user['is_admin']
            ]
        ];
    }

    // ==================== LOGOUT ====================

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => "Logged out successfully"];
    }

    // ==================== CHECK AUTH ====================

    public function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'is_admin' => $_SESSION['is_admin']
        ];
    }

    // ==================== GET USER BY ID ====================

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT id, email, first_name, last_name, phone, address, city, country, postal_code, created_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ==================== UPDATE PROFILE ====================

    public function updateProfile($userId, $data)
    {
        $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'city', 'country', 'postal_code'];
        $updates = [];
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => "No fields to update"];
        }

        $values[] = $userId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            return ['success' => true, 'message' => "Profile updated successfully"];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Update failed: " . $e->getMessage()];
        }
    }

    // ==================== CHANGE PASSWORD ====================

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        // Verify current password
        $stmt = $this->db->prepare("SELECT password_hash FROM {$this->table} WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => "Current password is incorrect"];
        }

        // Validate new password
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => "New password must be at least 6 characters"];
        }

        // Update password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password_hash = ? WHERE id = ?");

        try {
            $stmt->execute([$newHash, $userId]);
            return ['success' => true, 'message' => "Password changed successfully"];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Failed to change password"];
        }
    }
}
