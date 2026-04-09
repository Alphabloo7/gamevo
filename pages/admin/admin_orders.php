<?php
/**
 * GAMEVO - Admin Orders Management
 */
require_once '../../includes/admin_auth.php';

// Clear cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

requireAdminLogin();

$admin = getCurrentAdmin();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$limit = 20;
$offset = ($page - 1) * $limit;

$orders = getAllOrders($status_filter, $limit, $offset);

// Handle status update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $result = updateOrderStatus($_POST['order_id'], $_POST['status']);
    $message = $result['message'];
    $orders = getAllOrders($status_filter, $limit, $offset);
}

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Orders - Admin GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
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
        
        .content {
            padding: 30px;
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: #667eea;
            border-color: #667eea;
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
        
        .orders-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            overflow-x: auto;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
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
        
        .select-status {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .select-status:hover {
            background: rgba(102, 126, 234, 0.2);
            border-color: rgba(102, 126, 234, 0.5);
        }
        
        .select-status:focus {
            outline: none;
            background: rgba(102, 126, 234, 0.3);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Style for select options */
        .select-status option {
            background: #0a0e27;
            color: #fff;
            padding: 8px 12px;
            margin: 4px 0;
            border: none;
        }
        
        .select-status option:hover {
            background: #667eea;
            color: white;
        }
        
        .select-status option:checked {
            background: linear-gradient(#667eea, #667eea);
            color: white;
        }
        
        .select-status option:disabled {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
        }
        
        .update-form {
            display: flex;
            gap: 8px;
        }
        
        .update-btn {
            background: #667eea;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .update-btn:hover {
            background: #764ba2;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
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
                <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Daftar Order</a></li>
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
                    <h1>Kelola Orders</h1>
                </div>
                <a href="admin_logout.php" class="logout-btn">Logout</a>
            </div>
            
            <!-- Content -->
            <div class="content">
                <?php if (!empty($message)): ?>
                    <div class="message success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                
                <!-- Filters -->
                <div class="filters">
                    <a href="admin_orders.php" class="filter-btn <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                    <a href="admin_orders.php?status=pending" class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="admin_orders.php?status=processing" class="filter-btn <?php echo $status_filter === 'processing' ? 'active' : ''; ?>">Processing</a>
                    <a href="admin_orders.php?status=completed" class="filter-btn <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                    <a href="admin_orders.php?status=cancelled" class="filter-btn <?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                </div>
                
                <!-- Orders Table -->
                <div class="orders-section">
                    <?php if (!empty($orders)): ?>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID Order</th>
                                    <th>Username</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name'] ?? 'Produk Dihapus'); ?></td>
                                        <td><?php echo formatCurrency($order['total_price']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if (!empty($order['payment_proof'])): ?>
                                                    <button class="view-proof-btn" onclick="viewPaymentProof('<?php echo htmlspecialchars($order['payment_proof']); ?>')">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($order['status'] === 'completed' || $order['status'] === 'cancelled'): ?>
                                                    <!-- Status locked - cannot be changed -->
                                                    <div class="status-locked">
                                                        <i class="fas fa-lock"></i>
                                                        <span><?php echo $order['status'] === 'completed' ? 'Selesai (Terkunci)' : 'Dibatalkan (Terkunci)'; ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Status can be changed -->
                                                    <form method="POST" class="update-form">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <select name="status" class="select-status">
                                                            <option value="">Ubah Status</option>
                                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'disabled' : ''; ?>>Pending</option>
                                                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'disabled' : ''; ?>>Processing</option>
                                                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'disabled' : ''; ?>>Completed</option>
                                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'disabled' : ''; ?>>Cancelled</option>
                                                        </select>
                                                        <button type="submit" class="update-btn">Update</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada order untuk ditampilkan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Proof Modal -->
    <div id="proofModal" class="proof-modal">
        <div class="proof-modal-content">
            <div class="proof-modal-header">
                <h2>Bukti Pembayaran</h2>
                <button class="proof-modal-close" onclick="closeProofModal()">&times;</button>
            </div>
            <div class="proof-modal-body">
                <div id="proofContent" class="proof-content"></div>
            </div>
        </div>
    </div>

    <style>
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .view-proof-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: background-color 0.3s ease;
        }

        .view-proof-btn:hover {
            background-color: #45a049;
        }

        .status-locked {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.5);
            border-radius: 6px;
            color: #ffcdd2;
            font-size: 12px;
            font-weight: 600;
        }

        .status-locked i {
            font-size: 14px;
        }

        .proof-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .proof-modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .proof-modal-content {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        .proof-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            background-color: #f8f9fa;
        }

        .proof-modal-header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .proof-modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
        }

        .proof-modal-close:hover {
            color: #333;
        }

        .proof-modal-body {
            padding: 20px;
        }

        .proof-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
        }

        .proof-content img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .proof-content embed,
        .proof-content iframe {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 4px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <script>
        function viewPaymentProof(proofPath) {
            const modal = document.getElementById('proofModal');
            const proofContent = document.getElementById('proofContent');
            const extension = proofPath.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                proofContent.innerHTML = '<img src="../../' + proofPath + '" alt="Payment Proof">';
            } else if (extension === 'pdf') {
                proofContent.innerHTML = '<embed src="../../' + proofPath + '" type="application/pdf">';
            } else {
                proofContent.innerHTML = '<p>Format file tidak didukung untuk preview</p>';
            }

            modal.classList.add('show');
        }

        function closeProofModal() {
            const modal = document.getElementById('proofModal');
            modal.classList.remove('show');
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('proofModal');
            if (event.target === modal) {
                closeProofModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeProofModal();
            }
        });

        // Handle status change - auto submit form
        document.querySelectorAll('.select-status').forEach(select => {
            select.addEventListener('change', function() {
                if (this.value !== '') {
                    this.closest('.update-form').submit();
                }
            });
        });
    </script>



