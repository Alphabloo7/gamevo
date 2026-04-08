<?php
/**
 * GAMEVO - Admin Products Management
 */
require_once '../../includes/admin_auth.php';

requireAdminLogin();

global $conn;
$admin = getCurrentAdmin();

// Get all products
$result = $conn->query("SELECT id, name, category, price, created_at, is_active FROM products ORDER BY created_at DESC");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin GAMEVO</title>
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
        
        .products-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            overflow-x: auto;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .products-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }
        
        .status-active {
            background: rgba(76, 175, 80, 0.2);
            color: #c8e6c9;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="admin_dashboard.php" class="sidebar-logo">GAMEVO</a>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Daftar Order</a></li>
                <li><a href="admin_packages.php"><i class="fas fa-box"></i> Paket Game</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> Kelola Users</a></li>
                <li><a href="admin_products.php" class="active"><i class="fas fa-gamepad"></i> Kelola Produk</a></li>
                <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Kelola Produk</h1>
                </div>
                <a href="admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <!-- Content -->
            <div class="content">
                <div class="products-section">
                    <h2 style="margin-bottom: 20px;">Total Produk: <?php echo count($products); ?></h2>
                    
                    <?php if (!empty($products)): ?>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['category'] ?? '-'); ?></td>
                                        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($product['is_active']): ?>
                                                <span class="status-active">Γ£ô Aktif</span>
                                            <?php else: ?>
                                                <span style="color: #ffcdd2;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($product['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada produk terdaftar</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>


