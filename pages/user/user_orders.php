<?php
/**
 * GAMEVO - User Order History
 * View pesanan dan status pembayaran
 */
require_once '../../includes/auth.php';

// Require user login
requireLogin();

$user = getCurrentUser();
global $conn;

// Get user's orders
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    // Decode notes JSON
    $row['order_data'] = json_decode($row['notes'], true);
    $orders[] = $row;
}
$stmt->close();

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function getStatusBadge($status) {
    $badges = [
        'pending' => ['bg' => '#ff9800', 'text' => 'Menunggu Verifikasi'],
        'processing' => ['bg' => '#2196f3', 'text' => 'Sedang Diproses'],
        'completed' => ['bg' => '#4caf50', 'text' => 'Selesai'],
        'rejected' => ['bg' => '#f44336', 'text' => 'Ditolak']
    ];
    $badge = $badges[$status] ?? ['bg' => '#999', 'text' => ucfirst($status)];
    return '<span style="background: ' . $badge['bg'] . '; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">' . $badge['text'] . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        body {
            background: #0a0e27;
            color: #fff;
        }

        .orders-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .orders-header {
            margin-bottom: 30px;
        }

        .orders-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .orders-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            transform: translateX(-5px);
        }

        .order-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }

        .order-id {
            font-weight: 600;
            font-size: 16px;
        }

        .order-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 12px;
        }

        .detail-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 500;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view-proof {
            background: #667eea;
            color: white;
        }

        .btn-view-proof:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .btn-topup {
            background: rgba(102, 126, 234, 0.2);
            color: #a8b8ff;
            border: 1px solid #667eea;
        }

        .btn-topup:hover {
            background: rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-text {
            font-size: 18px;
            margin-bottom: 30px;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            background: #0a0e27;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            color: white;
        }

        .modal-image {
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../../index.php" class="logo">
                <img src="../../assets/images/gamevo_logo.png" alt="GAMEVO" class="logo-image">
                <span class="logo-text">GAMEVO</span>
            </a>
            <div class="nav-menu">
                <a href="../../index.php" class="nav-link">BERANDA</a>
                <a href="user_profile.php" class="nav-link">PROFIL</a>
                <a href="user_orders.php" class="nav-link active">PESANAN SAYA</a>
                <a href="user_logout.php" class="nav-link">LOGOUT</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="orders-wrapper">
        <a href="../../index.php" class="back-link">← Kembali ke Beranda</a>

        <div class="orders-header">
            <h1>📦 Riwayat Pesanan</h1>
            <p>Periksa status semua pesanan top-up Anda</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <div class="empty-text">Belum ada pesanan</div>
                <a href="../../index.php" style="color: #667eea; text-decoration: none; font-weight: 600;">→ Mulai top-up sekarang</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo $order['id']; ?></div>
                            <div class="order-date"><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div>
                            <?php echo getStatusBadge($order['status']); ?>
                        </div>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <div class="detail-label">Game</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['order_data']['game'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Paket</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['order_data']['package'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">ID Game</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['order_data']['game_account'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total Bayar</div>
                            <div class="detail-value"><?php echo formatCurrency($order['total_price']); ?></div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <div style="font-size: 14px; color: rgba(255, 255, 255, 0.7);">
                            <?php if ($order['status'] === 'pending'): ?>
                                ⏳ Menunggu admin memverifikasi bukti pembayaran
                            <?php elseif ($order['status'] === 'processing'): ?>
                                ⚙️ Sedang memproses pengiriman ke akun Anda
                            <?php elseif ($order['status'] === 'completed'): ?>
                                ✅ Pesanan selesai, sudah dikirim ke akun Anda
                            <?php elseif ($order['status'] === 'rejected'): ?>
                                ❌ Pesanan ditolak, silakan hubungi support
                            <?php endif; ?>
                        </div>
                        <div class="order-actions">
                            <?php if ($order['payment_proof']): ?>
                                <button class="btn-small btn-view-proof" onclick="viewProof('<?php echo htmlspecialchars($order['payment_proof']); ?>')">
                                    📸 Lihat Bukti
                                </button>
                            <?php endif; ?>
                            <a href="../../index.php" class="btn-small btn-topup">+ Top-up Lagi</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal for viewing payment proof -->
    <div id="proofModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeProof()">&times;</span>
            <h2>📸 Bukti Pembayaran</h2>
            <div id="proofContent"></div>
        </div>
    </div>

    <script>
        function viewProof(proofPath) {
            const modal = document.getElementById('proofModal');
            const content = document.getElementById('proofContent');
            
            if (proofPath.endsWith('.pdf')) {
                content.innerHTML = '<iframe src="' + proofPath + '" style="width: 100%; height: 500px; border: none; border-radius: 8px;"></iframe>';
            } else {
                content.innerHTML = '<img src="' + proofPath + '" class="modal-image" alt="Bukti Pembayaran">';
            }
            
            modal.style.display = 'block';
        }

        function closeProof() {
            document.getElementById('proofModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('proofModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
