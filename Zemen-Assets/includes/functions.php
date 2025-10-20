<?php
require_once 'config.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /property-platform/auth/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: /property-platform/index.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Utility functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice($price) {
    return 'ETB' . number_format($price, 1);
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function uploadImage($file, $directory = 'uploads/') {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type');
    }
    
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $directory . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    throw new Exception('Failed to upload file');
}

// Database helper functions
function getListings($status = 'approved', $limit = null) {
    $db = getDBConnection();
    $sql = "SELECT l.*, u.name as seller_name, 
            (SELECT image_path FROM listing_images WHERE listing_id = l.id LIMIT 1) as primary_image
            FROM listings l 
            JOIN users u ON l.user_id = u.id 
            WHERE l.status = ?
            ORDER BY l.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

function getListingById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT l.*, u.name as seller_name, u.phone as seller_phone, u.email as seller_email, image_url
        FROM listings l 
        JOIN users u ON l.user_id = u.id 
        WHERE l.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getListingImages($listingId) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM listing_images WHERE listing_id = ? ORDER BY is_primary DESC");
    $stmt->execute([$listingId]);
    return $stmt->fetchAll();
}

function getListingReviews($listingId) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM reviews WHERE listing_id = ? ORDER BY created_at DESC");
    $stmt->execute([$listingId]);
    return $stmt->fetchAll();
}

function searchListings($filters = []) {
    $db = getDBConnection();
    $conditions = ["l.status = 'approved'"];
    $params = [];
    
    if (!empty($filters['category'])) {
        $conditions[] = "l.category = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['location'])) {
        $conditions[] = "l.location LIKE ?";
        $params[] = '%' . $filters['location'] . '%';
    }
    
    if (!empty($filters['min_price'])) {
        $conditions[] = "l.price >= ?";
        $params[] = $filters['min_price'];
    }
    
    if (!empty($filters['max_price'])) {
        $conditions[] = "l.price <= ?";
        $params[] = $filters['max_price'];
    }
    
    if (!empty($filters['listing_type'])) {
        $conditions[] = "l.listing_type = ?";
        $params[] = $filters['listing_type'];
    }
    
    // $sql = "SELECT l.*, u.name as seller_name,
    //         (SELECT image_path FROM listing_images WHERE listing_id = l.id LIMIT 1) as primary_image
    //         FROM listings l 
    //         JOIN users u ON l.user_id = u.id 
    //         WHERE " . implode(' AND ', $conditions) . "
    //         ORDER BY l.created_at DESC";

    $sql = "SELECT l.*, u.name as seller_name, l.image_url
            FROM listings l 
            JOIN users u ON l.user_id = u.id 
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY l.created_at DESC";

    // $sql = "SELECT l.*,COUNT(o.id) as inquiry_count
    //             FROM listings l 
    //             LEFT JOIN orders o ON l.id = o.listing_id
    //             WHERE l.user_id = ? 
    //             GROUP BY l.id
    //             ORDER BY l.created_at DESC 
    //             LIMIT 10";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>