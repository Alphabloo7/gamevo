<?php
/**
 * GAMEVO - User Profile Page
 */
require_once '../../includes/auth.php';

// Require login
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        body {
            background: #0a0e27;
            color: #fff;
        }
        
        .navbar {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .profile-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 20px;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 600;
            margin: 15px 0 5px;
        }
        
        .profile-username {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .profile-info {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }
        
        .info-value {
            font-weight: 500;
            font-size: 16px;
        }
        
        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-logout {
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            border: 1px solid rgba(244, 67, 54, 0.5);
        }
        
        .btn-logout:hover {
            background: rgba(244, 67, 54, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../../index.php" class="logo">
                <span class="logo-text">GAMEVO</span>
            </a>
            <div class="nav-menu">
                <a href="../../index.php" class="nav-link">BERANDA</a>
                <a href="user_profile.php" class="nav-link active">PROFIL</a>
                <a href="user_orders.php" class="nav-link">PESANAN</a>
                <a href="user_logout.php" class="nav-link">LOGOUT</a>
            </div>
        </div>
    </nav>
    
    <div class="profile-container">
        <div class="profile-content">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            
            <div class="profile-info">
                <div class="info-row">
                    <span class="info-label">ID Pengguna</span>
                    <span class="info-value">#<?php echo $user['id']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="color: #4caf50;">✓ Aktif</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <button class="btn btn-primary">Edit Profil</button>
                <a href="user_logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
