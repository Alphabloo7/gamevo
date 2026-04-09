<?php
/**
 * GAMEVO - Admin Dashboard
 */
require_once '../../includes/admin_auth.php';

// Require admin login
requireAdminLogin();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$admin = getCurrentAdmin();
$stats = getSalesStatistics();
$today_stats = getTodaysSalesSummary();
$recent_orders = getRecentOrders(10);
$latest_transactions = getLatestTransactions(15);
$daily_sales = getDailySalesData(30);
$sales_by_game = getSalesByGame(10);
$sales_by_status = getSalesByStatus();

// Format currency
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
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
        
        .sidebar-menu li {
            margin: 0;
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
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
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
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .topbar-title h1 {
            font-size: 28px;
            font-weight: 600;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .admin-info {
            display: flex;
            flex-direction: column;
        }
        
        .admin-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .admin-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
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
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(244, 67, 54, 0.3);
        }
        
        .content {
            padding: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .stat-title {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }
        
        .stat-icon {
            font-size: 24px;
            width: 40px;
            height: 40px;
            background: rgba(102, 126, 234, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .orders-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            overflow-x: auto;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .view-all-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table thead {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .orders-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .orders-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }
        
        .orders-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #fff9c4;
        }
        
        .status-processing {
            background: rgba(63, 81, 181, 0.2);
            color: #c5cae9;
        }
        
        .status-completed {
            background: rgba(76, 175, 80, 0.2);
            color: #c8e6c9;
        }
        
        .status-cancelled {
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .chart-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-title i {
            color: #667eea;
            font-size: 20px;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
            margin-bottom: 10px;
        }
        
        .chart-wrapper canvas {
            max-height: 300px;
        }
        
        .realtime-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-left: auto;
        }
        
        .realtime-dot {
            width: 8px;
            height: 8px;
            background: #4caf50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
            }
            50% {
                opacity: 0.8;
                box-shadow: 0 0 0 6px rgba(76, 175, 80, 0);
            }
        }
        
        .today-stats-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .today-stats-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .today-stats-title i {
            color: #667eea;
        }
        
        .today-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .today-stat-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .today-stat-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(102, 126, 234, 0.5);
        }
        
        .today-stat-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .today-stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
            }
            
            .topbar {
                flex-direction: column;
                gap: 15px;
            }
            
            .topbar-title h1 {
                font-size: 22px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .orders-table {
                font-size: 12px;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 10px;
            }
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
                <li><a href="admin_dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Daftar Order</a></li>
                <li><a href="admin_packages.php"><i class="fas fa-box"></i> Paket Game</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> Kelola Users</a></li>
                <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Dashboard</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="admin-profile">
                        <div class="admin-avatar">
                            <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
                        </div>
                        <div class="admin-info">
                            <div class="admin-name"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                            <div class="admin-role"><?php echo ucfirst($admin['role']); ?></div>
                        </div>
                    </div>
                    <a href="admin_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <!-- Content -->
            <div class="content">
                <!-- Today's Sales Section -->
                <div class="today-stats-section">
                    <div class="today-stats-title">
                        <i class="fas fa-fire"></i> Penjualan Hari Ini
                        <div class="realtime-indicator">
                            <div class="realtime-dot"></div>
                            <span>Real-time</span>
                        </div>
                    </div>
                    <div class="today-stats-grid">
                        <div class="today-stat-item">
                            <div class="today-stat-label">Total Order</div>
                            <div class="today-stat-value" id="today-total-orders"><?php echo $today_stats['total_orders']; ?></div>
                        </div>
                        <div class="today-stat-item">
                            <div class="today-stat-label">Selesai</div>
                            <div class="today-stat-value" id="today-completed"><?php echo $today_stats['completed']; ?></div>
                        </div>
                        <div class="today-stat-item">
                            <div class="today-stat-label">Pending</div>
                            <div class="today-stat-value" id="today-pending"><?php echo $today_stats['pending']; ?></div>
                        </div>
                        <div class="today-stat-item">
                            <div class="today-stat-label">Revenue</div>
                            <div class="today-stat-value" id="today-revenue"><?php echo formatCurrency($today_stats['revenue']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Transaksi Hari Ini</div>
                            <div class="stat-icon">📦</div>
                        </div>
                        <div class="stat-value" id="card-total-orders"><?php echo $today_stats['total_orders']; ?></div>
                        <div class="stat-change"><?php echo $today_stats['completed']; ?> selesai, <?php echo $today_stats['pending']; ?> pending</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Revenue Hari Ini</div>
                            <div class="stat-icon">💰</div>
                        </div>
                        <div class="stat-value" id="card-today-revenue"><?php echo formatCurrency($today_stats['revenue']); ?></div>
                        <div class="stat-change">Dari transaksi completed</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Users</div>
                            <div class="stat-icon">👥</div>
                        </div>
                        <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                        <div class="stat-change">Terdaftar di sistem</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">All-Time Revenue</div>
                            <div class="stat-icon">📊</div>
                        </div>
                        <div class="stat-value"><?php echo formatCurrency($stats['total_revenue']); ?></div>
                        <div class="stat-change">Total dari semua transaksi</div>
                    </div>
                </div>
                
                <!-- Charts Section -->
                <div class="charts-container">
                    <!-- Daily Sales Chart -->
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-chart-line"></i> Penjualan Harian (30 Hari)
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Sales by Game Chart -->
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-gamepad"></i> Penjualan Per Game
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="gamesSalesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Sales by Status Chart -->
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-chart-pie"></i> Order Berdasarkan Status
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="orders-section">
                    <div class="section-title">
                        <span>Pesanan Terbaru</span>
                        <a href="admin_orders.php" class="view-all-btn">Lihat Semua</a>
                    </div>
                    
                    <?php if (!empty($recent_orders)): ?>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID Order</th>
                                    <th>Username</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name'] ?? 'Produk Dihapus'); ?></td>
                                        <td><?php echo formatCurrency($order['total_price']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <p>Belum ada order untuk ditampilkan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Chart.js configuration
        Chart.defaults.color = 'rgba(255, 255, 255, 0.6)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.05)';
        
        // Function to format currency
        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
        
        // Real-time data update function
        function updateRealtimeData() {
            fetch('api_realtime_data.php?action=today')
                .then(response => response.json())
                .then(data => {
                    // Update today's stats section
                    document.getElementById('today-total-orders').textContent = data.total_orders;
                    document.getElementById('today-completed').textContent = data.completed;
                    document.getElementById('today-pending').textContent = data.pending;
                    document.getElementById('today-revenue').textContent = formatCurrency(data.revenue);
                    
                    // Update statistics cards
                    document.getElementById('card-total-orders').textContent = data.total_orders;
                    document.getElementById('card-today-revenue').textContent = formatCurrency(data.revenue);
                })
                .catch(error => console.error('Error fetching real-time data:', error));
        }
        
        // Auto-refresh every 30 seconds
        setInterval(updateRealtimeData, 30000);
        
        // Also update on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initial update after 5 seconds to ensure page is fully loaded
            setTimeout(updateRealtimeData, 5000);
        });
        
        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($daily_sales['labels']); ?>,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: <?php echo json_encode($daily_sales['data']); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#667eea',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#764ba2'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            font: { size: 12, weight: '600' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        
        // Sales by Game Chart
        const gameCtx = document.getElementById('gamesSalesChart').getContext('2d');
        new Chart(gameCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($sales_by_game['labels']); ?>,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: <?php echo json_encode($sales_by_game['data']); ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(237, 100, 166, 0.8)',
                        'rgba(255, 154, 158, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(63, 81, 181, 0.8)',
                        'rgba(233, 30, 99, 0.8)',
                        'rgba(3, 169, 244, 0.8)',
                        'rgba(156, 39, 176, 0.8)'
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        
        // Sales by Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($sales_by_status['labels']); ?>,
                datasets: [{
                    data: <?php echo json_encode($sales_by_status['data']); ?>,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(63, 81, 181, 0.8)',
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(244, 67, 54, 0.8)'
                    ],
                    borderColor: '#0a0e27',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            font: { size: 12, weight: '600' },
                            padding: 15
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
