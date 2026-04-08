<?php
/**
 * GAMEVO - Admin Settings
 */
require_once '../../includes/admin_auth.php';

requireAdminLogin();

$admin = getCurrentAdmin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #0a0e27;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar-menu a.active {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border-left-color: #667eea;
        }
        
        .main-content {
            margin-left: 260px;
            flex: 1;
        }
        
        .topbar {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-title h1 {
            font-size: 28px;
            font-weight: 600;
        }
        
        .logout-btn {
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            border: 1px solid rgba(244, 67, 54, 0.5);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .content {
            padding: 30px;
        }
        
        .settings-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .settings-card h2 {
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.02);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .info-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">GAMEVO</a>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Daftar Order</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Kelola Users</a></li>
                <li><a href="products.php"><i class="fas fa-gamepad"></i> Kelola Produk</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Pengaturan</h1>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            
            <!-- Content -->
            <div class="content">
                <!-- Admin Profile Settings -->
                <div class="settings-card">
                    <h2>≡ƒæñ Profil Admin</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($admin['username']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Role</div>
                            <div class="info-value"><?php echo ucfirst(str_replace('_', ' ', $admin['role'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">Γ£ô Aktif</div>
                        </div>
                    </div>
                </div>
                
                <!-- System Information -->
                <div class="settings-card">
                    <h2>Γä╣∩╕Å Informasi Sistem</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Versi PHP</div>
                            <div class="info-value"><?php echo phpversion(); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Database</div>
                            <div class="info-value">MySQL/MariaDB</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Server OS</div>
                            <div class="info-value"><?php echo php_uname('s'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Waktu Server</div>
                            <div class="info-value"><?php echo date('d M Y H:i:s'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Notes -->
                <div class="settings-card">
                    <h2>≡ƒô¥ Catatan Penting</h2>
                    <p style="line-height: 1.6; color: rgba(255, 255, 255, 0.7);">
                        Γ£ô Selamat datang di Admin Dashboard GAMEVO<br>
                        Γ£ô Anda dapat mengelola orders, users, dan produk dari sini<br>
                        Γ£ô Semua perubahan akan tercatat dalam sistem<br>
                        Γ£ô Pastikan untuk selalu logout setelah selesai<br>
                        Γ£ô Hubungi support jika ada kendala teknis
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

