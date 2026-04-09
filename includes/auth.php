<?php
/**
 * Authentication Functions for GAMEVO
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Register new user
 */
function registerUser($username, $email, $password, $full_name) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        return ['success' => false, 'message' => 'Semua field harus diisi'];
    }
    
    // Check username length
    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username minimal 3 karakter'];
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email tidak valid'];
    }
    
    // Check password strength
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password minimal 6 karakter'];
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    $stmt->close();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Email sudah terdaftar'];
    }
    $stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $conn->error];
    }
}

/**
 * Login user
 */
function loginUser($username, $password) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi'];
    }
    
    // Get user
    $stmt = $conn->prepare("SELECT id, username, password, full_name, is_active FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if user is active
    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'Akun Anda telah dinonaktifkan'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;
    
    // Log login history
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $stmt = $conn->prepare("INSERT INTO login_history (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
    
    return ['success' => true, 'message' => 'Login berhasil!'];
}

/**
 * Unified Login - Handle both user and admin login
 */
function unifiedLogin($username, $password) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi', 'type' => null];
    }
    
    // First, check if it's an admin
    $stmt = $conn->prepare("SELECT id, username, password, full_name, role, is_active FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        // Check if admin is active
        if (!$admin['is_active']) {
            return ['success' => false, 'message' => 'Akun admin telah dinonaktifkan', 'type' => null];
        }
        
        // Verify password
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Username atau password salah', 'type' => null];
        }
        
        // Clear any existing user session to prevent conflicts
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['logged_in']);
        
        // Set admin session
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
        
        return ['success' => true, 'message' => 'Login admin berhasil!', 'type' => 'admin'];
    }
    $stmt->close();
    
    // Check if it's a regular user
    $stmt = $conn->prepare("SELECT id, username, password, full_name, is_active FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Username atau password salah', 'type' => null];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if user is active
    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'Akun Anda telah dinonaktifkan', 'type' => null];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah', 'type' => null];
    }
    
    // Clear any existing admin session to prevent conflicts
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_full_name']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_logged_in']);
    
    // Set user session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;
    
    // Log user login
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $stmt = $conn->prepare("INSERT INTO login_history (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
    
    return ['success' => true, 'message' => 'Login berhasil!', 'type' => 'user'];
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name']
    ];
}

/**
 * Logout user
 */
function logoutUser() {
    // Clear all session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    return ['success' => true, 'message' => 'Logout berhasil'];
}

/**
 * Redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: user_login.php");
        exit();
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}
?>
