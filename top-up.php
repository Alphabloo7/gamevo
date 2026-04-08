<?php
/**
 * GAMEVO - Top Up Product Page
 * Dynamic product top-up page
 */

// Game data
$games = [
    'roblox' => [
        'name' => 'Roblox',
        'title' => 'Top Up Roblox - Robux',
        'description' => 'Beli Robux untuk Roblox dengan harga terbaik dan terpercaya. Proses instant, tanpa perlu menunggu lama.',
        'image' => 'assets/images/rblx_icon.jpg',
        'currency' => 'Robux',
        'packages' => [
            ['name' => '400 Robux', 'price' => 'Rp 35.000', 'robux' => 400],
            ['name' => '800 Robux', 'price' => 'Rp 68.000', 'robux' => 800],
            ['name' => '1.700 Robux', 'price' => 'Rp 150.000', 'robux' => 1700],
            ['name' => '4.500 Robux', 'price' => 'Rp 380.000', 'robux' => 4500],
        ]
    ],
    'mobile-legends' => [
        'name' => 'Mobile Legends',
        'title' => 'Top Up Mobile Legends - Diamond ML',
        'description' => 'Beli Diamond Mobile Legends dengan harga paling murah se-Indonesia. Garansi uang kembali 100%.',
        'image' => 'assets/images/ml_icon.jpg',
        'currency' => 'Diamond',
        'packages' => [
            ['name' => '50 Diamond', 'price' => 'Rp 9.000', 'diamond' => 50],
            ['name' => '126 Diamond', 'price' => 'Rp 21.000', 'diamond' => 126],
            ['name' => '259 Diamond', 'price' => 'Rp 42.000', 'diamond' => 259],
            ['name' => '869 Diamond', 'price' => 'Rp 138.000', 'diamond' => 869],
        ]
    ],
    'pubg' => [
        'name' => 'Player Unkown Battlegrounds',
        'title' => 'Top Up Player Unkown Battlegrounds - UC',
        'description' => 'Top Up UC Player Unkown Battlegrounds dengan instant delivery. Payment method lengkap dan aman.',
        'image' => 'assets/images/pubg_icon.jpg',
        'currency' => 'UC',
        'packages' => [
            ['name' => '50 UC', 'price' => 'Rp 10.000', 'uc' => 50],
            ['name' => '125 UC', 'price' => 'Rp 25.000', 'uc' => 125],
            ['name' => '325 UC', 'price' => 'Rp 65.000', 'uc' => 325],
            ['name' => '1000 UC', 'price' => 'Rp 200.000', 'uc' => 1000],
        ]
    ],
    'genshin-impact' => [
        'name' => 'Genshin Impact',
        'title' => 'Top Up Genshin Impact - Genesis Crystals',
        'description' => 'Beli Genesis Crystals Genshin Impact dengan cepat dan aman. Tersedia untuk semua server (Global, CN, Asia).',
        'image' => 'assets/images/gi_icon.jpeg',
        'currency' => 'Genesis Crystals',
        'packages' => [
            ['name' => '60 Crystals', 'price' => 'Rp 65.000', 'crystals' => 60],
            ['name' => '330 Crystals', 'price' => 'Rp 320.000', 'crystals' => 330],
            ['name' => '1090 Crystals', 'price' => 'Rp 1.030.000', 'crystals' => 1090],
            ['name' => '3080 Crystals', 'price' => 'Rp 2.890.000', 'crystals' => 3080],
        ]
    ]
];

// Get game parameter
$game = isset($_GET['game']) ? $_GET['game'] : null;
$gameData = isset($games[$game]) ? $games[$game] : null;

// Redirect if game not found
if (!$gameData) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $gameData['title']; ?> - GAMEVO</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/topup.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="logo">
                <img src="assets/images/gamevo_logo.png" alt="GAMEVO" class="logo-image">
                <span class="logo-text">GAMEVO</span>
            </a>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">BERANDA</a>
                <a href="#kontak" class="nav-link">KONTAK</a>
                <div class="search-box">
                    <input type="text" placeholder="Cari..." class="search-input">
                    <button class="search-btn">🔍</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section">
            <div class="breadcrumb-container">
                <a href="index.php">Beranda</a>
                <span>/</span>
                <span><?php echo $gameData['name']; ?></span>
            </div>
        </section>

        <!-- Product Header -->
        <section class="product-header">
            <div class="product-header-container">
                <div class="product-header-image">
                    <img src="<?php echo $gameData['image']; ?>" alt="<?php echo $gameData['name']; ?>">
                </div>
                <div class="product-header-info">
                    <h1><?php echo $gameData['name']; ?></h1>
                    <p class="product-description"><?php echo $gameData['description']; ?></p>
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
                <h2>Pilih Paket <?php echo $gameData['currency']; ?></h2>
                <div class="packages-grid">
                    <?php foreach ($gameData['packages'] as $package): ?>
                    <div class="package-card">
                        <div class="package-header">
                            <h3><?php echo $package['name']; ?></h3>
                            <span class="package-price"><?php echo $package['price']; ?></span>
                        </div>
                        <button class="btn-select" onclick="selectPackage('<?php echo $package['name']; ?>', '<?php echo $package['price']; ?>')">
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
                    <h2>Masukkan Data Anda</h2>
                    <form id="topup-form" class="topup-form">
                        <div class="form-group">
                            <label for="username">Username/ID Game</label>
                            <input type="text" id="username" name="username" placeholder="Masukkan username atau ID game Anda" required>
                        </div>

                        <div class="form-group">
                            <label for="selected-package">Paket Terpilih</label>
                            <input type="text" id="selected-package" name="selected-package" placeholder="Pilih paket terlebih dahulu" readonly>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" placeholder="Nama Anda" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Nomor Telepon</label>
                                <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="email@example.com" required>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <div class="payment-qris">
                                <div class="qris-display">
                                    <img src="assets/images/qris.jpg" alt="QRIS" class="qris-image">
                                    <p class="qris-label">QRIS (Quick Response Code Indonesian Standard)</p>
                                    <p class="qris-desc">Scan QRIS dengan aplikasi e-wallet Anda untuk pembayaran instant</p>
                                </div>
                                <input type="hidden" id="payment" name="payment" value="qris">
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">Lanjut ke Pembayaran</button>
                    </form>
                </div>

                <div class="form-info">
                    <h3>Informasi Penting</h3>
                    <div class="info-card">
                        <h4>⚡ Pengiriman Instan</h4>
                        <p>Setelah pembayaran dikonfirmasi, <?php echo $gameData['currency']; ?> akan langsung masuk ke akun Anda dalam hitungan detik.</p>
                    </div>
                    <div class="info-card">
                        <h4>🔒 100% Aman</h4>
                        <p>Data akun Anda dijamin aman. Kami tidak akan pernah meminta password atau token keamanan Anda.</p>
                    </div>
                    <div class="info-card">
                        <h4>💰 Harga Terbaik</h4>
                        <p>Bandingkan dengan website lainnya. Kami menawarkan harga paling kompetitif di Indonesia.</p>
                    </div>
                    <div class="info-card">
                        <h4>📞 Support 24/7</h4>
                        <p>Tim customer service kami siap membantu Anda kapan saja untuk menjawab semua pertanyaan.</p>
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

    <script src="assets/js/main.js"></script>
    <script>
        function selectPackage(packageName, price) {
            document.getElementById('selected-package').value = packageName + ' - ' + price;
            document.getElementById('selected-package').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('topup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedPackage = document.getElementById('selected-package').value;
            
            if (!selectedPackage || selectedPackage === 'Pilih paket terlebih dahulu') {
                alert('Silakan pilih paket terlebih dahulu!');
                return;
            }

            // Simulate form submission
            alert('Terima kasih! Form Anda telah dikirim. Silakan lanjut ke pembayaran.');
            // In production, this would submit to a payment processor
        });
    </script>
</body>
</html>
