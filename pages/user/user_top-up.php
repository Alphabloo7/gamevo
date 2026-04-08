<?php
/**
 * GAMEVO - Top Up Product Page
 * Dynamic product top-up page with payment and login requirement
 */
require_once '../../includes/auth.php';

// Require user login
requireLogin();

$user = getCurrentUser();
global $conn;

// Create uploads directory if not exists
$uploads_dir = '../../assets/uploads/payment_proofs';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $game_name = isset($_POST['game']) ? trim($_POST['game']) : '';
    $package_name = isset($_POST['package']) ? trim($_POST['package']) : '';
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
    $game_account = isset($_POST['game_account']) ? trim($_POST['game_account']) : '';
    
    // Validate inputs
    if (empty($game_name) || empty($package_name) || empty($total_price) || empty($game_account)) {
        $message = 'Mohon lengkapi semua field';
        $message_type = 'error';
    } elseif (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE) {
        $message = 'Mohon upload bukti pembayaran';
        $message_type = 'error';
    } else {
        // Handle file upload
        $file = $_FILES['payment_proof'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        
        if (!in_array($file['type'], $allowed_types)) {
            $message = 'Format file harus JPG, PNG, GIF, atau PDF';
            $message_type = 'error';
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            $message = 'Ukuran file tidak boleh lebih dari 5MB';
            $message_type = 'error';
        } else {
            // Generate unique filename
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = 'payment_' . $user['id'] . '_' . time() . '.' . $file_ext;
            $file_path = $uploads_dir . '/' . $file_name;
            $db_file_path = 'assets/uploads/payment_proofs/' . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Insert order into database
                $query = "INSERT INTO orders (user_id, product_id, quantity, total_price, status, payment_method, payment_proof, notes, order_date) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    $message = 'Database error: ' . $conn->error;
                    $message_type = 'error';
                } else {
                    // Use product_id = 1 for now (we'll use it to track game name in notes)
                    $status = 'pending';
                    $payment_method = 'qris';
                    $quantity = 1;
                    $product_id = 1;
                    $notes = json_encode([
                        'game' => $game_name,
                        'package' => $package_name,
                        'game_account' => $game_account
                    ]);
                    
                    $stmt->bind_param('iiiisss', $user['id'], $product_id, $quantity, $total_price, $status, $payment_method, $db_file_path, $notes);
                    
                    if ($stmt->execute()) {
                        $message = '✓ Pesanan berhasil dibuat! Admin akan memverifikasi bukti pembayaran Anda.';
                        $message_type = 'success';
                        // Clear form after success
                        header("refresh:3;url=../../index.php");
                    } else {
                        $message = 'Gagal menyimpan pesanan: ' . $stmt->error;
                        $message_type = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $message = 'Gagal upload file, silakan coba lagi';
                $message_type = 'error';
            }
        }
    }
}

// Fetch game data from database
$game_slug = isset($_GET['game']) ? trim($_GET['game']) : null;
$gameData = null;
$packages = [];

if ($game_slug) {
    // Get game info
    $stmt = $conn->prepare("SELECT id, slug, name, title, description, currency, image_url FROM games WHERE slug = ? AND is_active = TRUE");
    $stmt->bind_param("s", $game_slug);
    $stmt->execute();
    $game_result = $stmt->get_result();
    
    if ($game_result->num_rows > 0) {
        $game = $game_result->fetch_assoc();
        $game_id = $game['id'];
        
        // Get packages for this game
        $pkg_stmt = $conn->prepare("SELECT id, name, amount, price FROM game_packages WHERE game_id = ? AND is_active = TRUE ORDER BY amount ASC");
        $pkg_stmt->bind_param("i", $game_id);
        $pkg_stmt->execute();
        $pkg_result = $pkg_stmt->get_result();
        
        while ($pkg = $pkg_result->fetch_assoc()) {
            $packages[] = [
                'name' => $pkg['name'],
                'price' => floatval($pkg['price']),
                'price_formatted' => 'Rp ' . number_format($pkg['price'], 0, ',', '.')
            ];
        }
        $pkg_stmt->close();
        
        // Format game data
        $gameData = [
            'name' => $game['name'],
            'title' => $game['title'],
            'description' => $game['description'],
            'currency' => $game['currency'],
            'image' => $game['image_url'],
            'packages' => $packages
        ];
    }
    $stmt->close();
}

// Redirect if game not found
if (!$gameData) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gameData['title']); ?> - GAMEVO</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link rel="stylesheet" href="../../assets/css/topup.css">
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
                <a href="#kontak" class="nav-link">KONTAK</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section">
            <div class="breadcrumb-container">
                <a href="../../index.php">Beranda</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($gameData['name']); ?></span>
            </div>
        </section>

        <!-- Product Header -->
        <section class="product-header">
            <div class="product-header-container">
                <div class="product-header-image">
                    <img src="../../<?php echo htmlspecialchars($gameData['image']); ?>" alt="<?php echo htmlspecialchars($gameData['name']); ?>">
                </div>
                <div class="product-header-info">
                    <h1><?php echo htmlspecialchars($gameData['name']); ?></h1>
                    <p class="product-description"><?php echo htmlspecialchars($gameData['description']); ?></p>
                    <div class="product-badge">
                        <span class="badge">⚡ Pengiriman Instan</span>
                        <span class="badge">✓ 100% Aman</span>
                        <span class="badge">💬 Support 24/7</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Packages Section -->
        <section class="topup-section">
            <div class="topup-container">
                <h2>Pilih Paket <?php echo htmlspecialchars($gameData['currency']); ?></h2>
                <div class="packages-grid">
                    <?php foreach ($packages as $package): ?>
                    <div class="package-card">
                        <div class="package-header">
                            <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                            <span class="package-price">Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></span>
                        </div>
                        <button class="btn-select" onclick="selectPackage('<?php echo htmlspecialchars($package['name']); ?>', <?php echo intval($package['price']); ?>)">
                            Pilih Paket
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Form Section -->
        <section class="form-section">
            <div class="form-container">
                <div class="form-content">
                    <h2>Konfirmasi Pesanan Top Up</h2>
                    
                    <?php if (!empty($message)): ?>
                        <div class="message <?php echo $message_type; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="topup-form" class="topup-form" method="POST" enctype="multipart/form-data">
                        <!-- Hidden fields for game and total price -->
                        <input type="hidden" id="game" name="game" value="">
                        <input type="hidden" id="total_price" name="total_price" value="">
                        
                        <div class="form-group">
                            <label for="package">Paket Terpilih</label>
                            <input type="text" id="selected-package" name="package" placeholder="Pilih paket terlebih dahulu" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="game_account">Username/ID Game Anda</label>
                            <input type="text" id="game_account" name="game_account" placeholder="Masukkan username atau ID game Anda" required>
                        </div>

                        <div class="form-group">
                            <label for="total_price_display">Total Bayar</label>
                            <input type="text" id="total_price_display" placeholder="Rp 0" readonly style="font-weight: bold; font-size: 18px;">
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <div class="payment-qris">
                                <div class="qris-display">
                                    <img src="../../assets/images/qris.jpg" alt="QRIS" class="qris-image">
                                    <p class="qris-label">QRIS (Quick Response Code Indonesian Standard)</p>
                                    <p class="qris-desc">Scan QRIS dengan smartphone Anda untuk melakukan pembayaran</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment_proof">📸 Upload Bukti Pembayaran (Screenshot QRIS)</label>
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/jpeg,image/png,image/gif,application/pdf" required>
                            <small style="display: block; margin-top: 8px; color: #999;">Format: JPG, PNG, GIF, atau PDF (Max 5MB)</small>
                        </div>

                        <button type="submit" name="submit_order" class="btn-submit">✓ Submit Pesanan & Bukti Pembayaran</button>
                    </form>
                </div>

                <div class="form-info">
                    <h3>📋 Panduan Pembayaran</h3>
                    <div class="info-card">
                        <h4>1️⃣ Pilih Paket</h4>
                        <p>Klik tombol "Pilih Paket" pada paket yang Anda inginkan</p>
                    </div>
                    <div class="info-card">
                        <h4>2️⃣ Scan QRIS</h4>
                        <p>Buka aplikasi e-wallet (GCash, Grabpay, OVO, DANA, dll) dan scan kode QRIS di atas</p>
                    </div>
                    <div class="info-card">
                        <h4>3️⃣ Verifikasi Bukti</h4>
                        <p>Ambil screenshot bukti pembayaran dan upload sebagai bukti. Admin akan verifikasi dalam hitungan menit</p>
                    </div>
                    <div class="info-card">
                        <h4>✅ Serah Terima</h4>
                        <p>Setelah admin verifikasi pembayaran, <?php echo htmlspecialchars($gameData['currency']); ?> akan langsung kami kirimkan ke akun Anda</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 GAMEVO. All rights reserved.</p>
            <p>Penuhi Semua Kebutuhan Gaming Mu Dalam Satu Tempat</p>
        </div>
    </footer>

    <script>
        // Get game name from URL
        const urlParams = new URLSearchParams(window.location.search);
        const gameName = urlParams.get('game');
        document.getElementById('game').value = gameName;
        
        function selectPackage(packageName, price) {
            // Update selected package display
            document.getElementById('selected-package').value = packageName;
            
            // If price is already numeric, use it directly
            const numericPrice = typeof price === 'number' ? price : parseInt(price.toString().replace(/\D/g, ''));
            
            // Update hidden total price field
            document.getElementById('total_price').value = numericPrice;
            
            // Update display price with proper formatting
            const formattedPrice = 'Rp ' + numericPrice.toLocaleString('id-ID');
            document.getElementById('total_price_display').value = formattedPrice;
            
            // Scroll to form
            document.getElementById('topup-form').scrollIntoView({ behavior: 'smooth' });
        }

        // Form validation before submit
        document.getElementById('topup-form').addEventListener('submit', function(e) {
            const selectedPackage = document.getElementById('selected-package').value;
            const gameAccount = document.getElementById('game_account').value;
            const totalPrice = document.getElementById('total_price').value;
            const paymentProof = document.getElementById('payment_proof').files[0];
            
            if (!selectedPackage) {
                e.preventDefault();
                alert('Silakan pilih paket terlebih dahulu!');
                return;
            }
            
            if (!gameAccount) {
                e.preventDefault();
                alert('Silakan masukkan username/ID game Anda!');
                return;
            }
            
            if (!totalPrice || totalPrice === '0') {
                e.preventDefault();
                alert('Harga total tidak valid!');
                return;
            }
            
            if (!paymentProof) {
                e.preventDefault();
                alert('Silakan upload bukti pembayaran!');
                return;
            }
            
            // All validations passed, form will submit
        });
    </script>
