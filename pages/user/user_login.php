<?php
/**
 * GAMEVO - Login Page (Unified Login for Users & Admin)
 */

// Set cache prevention headers FIRST
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../../includes/auth.php';
require_once '../../includes/admin_auth.php';

// Check if explicitly trying to access a form/login, then don't redirect
$is_login_attempt = ($_SERVER['REQUEST_METHOD'] === 'POST');

// Redirect if already logged in (but only if not attempting login)
if (!$is_login_attempt) {
    if (isLoggedIn()) {
        header("Location: ../../index.php");
        exit();
    }
    if (isAdminLoggedIn()) {
        header("Location: ../admin/admin_dashboard.php");
        exit();
    }
}

$message = '';
$message_type = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Clear any existing sessions to prevent conflicts
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
    }
    
    $result = unifiedLogin($username, $password);
    
    if ($result['success']) {
        // Regenerate session ID for security
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        
        // Clear cache headers again before redirect
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Small delay to ensure session is properly set
        usleep(100000); // 0.1 second delay
        
        // Redirect based on login type
        if ($result['type'] === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } else {
            header("Location: ../../index.php");
        }
        exit();
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-header h1 {
            color: white;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .auth-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .auth-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
        
        .auth-footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.success {
            background: rgba(76, 175, 80, 0.3);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #c8e6c9;
        }
        
        .message.error {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: #ffcdd2;
        }
        
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <a href="../../index.php" class="back-link">← Kembali ke Beranda</a>
        
        <div class="auth-header">
            <h1>GAMEVO</h1>
            <p>Masuk ke akun Anda</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="user_login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="auth-btn">Login</button>
        </form>
        
        <div class="auth-footer">
            Belum punya akun? <a href="user_register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
