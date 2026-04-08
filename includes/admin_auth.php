<?php
/**
 * Admin Authentication Functions for GAMEVO
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Login admin
 */
function loginAdmin($username, $password) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi'];
    }
    
    // Get admin
    $stmt = $conn->prepare("SELECT id, username, password, full_name, role, is_active FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    // Check if admin is active
    if (!$admin['is_active']) {
        return ['success' => false, 'message' => 'Akun admin telah dinonaktifkan'];
    }
    
    // Verify password
    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_full_name'] = $admin['full_name'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['admin_logged_in'] = true;
    
    // Log admin login
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $stmt = $conn->prepare("INSERT INTO admin_login_history (admin_id, ip_address, user_agent) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $admin['id'], $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
    
    return ['success' => true, 'message' => 'Login berhasil!'];
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current admin
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'full_name' => $_SESSION['admin_full_name'],
        'role' => $_SESSION['admin_role']
    ];
}

/**
 * Logout admin
 */
function logoutAdmin() {
    if (isset($_SESSION['admin_id'])) {
        $admin_id = $_SESSION['admin_id'];
        global $conn;
        
        // Update logout time
        $stmt = $conn->prepare("UPDATE admin_login_history SET logout_time = NOW() WHERE admin_id = ? ORDER BY login_time DESC LIMIT 1");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
    }
    
    session_destroy();
    return ['success' => true, 'message' => 'Logout berhasil'];
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: admin_login.php");
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfAdminLoggedIn() {
    if (isAdminLoggedIn()) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

/**
 * Get sales statistics
 */
function getSalesStatistics() {
    global $conn;
    
    $stats = [];
    
    // Total revenue
    $result = $conn->query("SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'completed'");
    $stats['total_revenue'] = $result->fetch_assoc()['total_revenue'] ?? 0;
    
    // Total orders
    $result = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total_orders'] ?? 0;
    
    // Completed orders
    $result = $conn->query("SELECT COUNT(*) as completed_orders FROM orders WHERE status = 'completed'");
    $stats['completed_orders'] = $result->fetch_assoc()['completed_orders'] ?? 0;
    
    // Pending orders
    $result = $conn->query("SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $result->fetch_assoc()['pending_orders'] ?? 0;
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total_users FROM users");
    $stats['total_users'] = $result->fetch_assoc()['total_users'] ?? 0;
    
    // Revenue today
    $result = $conn->query("SELECT SUM(total_price) as today_revenue FROM orders WHERE status = 'completed' AND DATE(completed_date) = CURDATE()");
    $stats['today_revenue'] = $result->fetch_assoc()['today_revenue'] ?? 0;
    
    return $stats;
}

/**
 * Get recent orders
 */
function getRecentOrders($limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            o.id, 
            o.order_date, 
            o.status, 
            o.total_price,
            u.username,
            p.name as product_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN products p ON o.product_id = p.id
        ORDER BY o.order_date DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    
    return $orders;
}

/**
 * Update order status
 */
function updateOrderStatus($order_id, $status) {
    global $conn;
    
    $allowed_status = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $allowed_status)) {
        return ['success' => false, 'message' => 'Status tidak valid'];
    }
    
    $completed_date = ($status === 'completed') ? date('Y-m-d H:i:s') : NULL;
    
    if ($completed_date) {
        $stmt = $conn->prepare("UPDATE orders SET status = ?, completed_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $completed_date, $order_id);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Status order berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui status order'];
    }
}

/**
 * Get all orders with filters
 */
function getAllOrders($status = null, $limit = 50, $offset = 0) {
    global $conn;
    
    if ($status) {
        $stmt = $conn->prepare("
            SELECT 
                o.id, 
                o.order_date, 
                o.status, 
                o.total_price,
                u.username,
                p.name as product_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN products p ON o.product_id = p.id
            WHERE o.status = ?
            ORDER BY o.order_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sii", $status, $limit, $offset);
    } else {
        $stmt = $conn->prepare("
            SELECT 
                o.id, 
                o.order_date, 
                o.status, 
                o.total_price,
                u.username,
                p.name as product_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN products p ON o.product_id = p.id
            ORDER BY o.order_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    
    return $orders;
}
?>
