<?php
/**
 * GAMEVO - Gaming Portal Landing Page
 * Main entry point for the application
 */
require_once 'includes/auth.php';
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAMEVO - Penuhi Semua Kebutuhan Gaming Mu Dalam Satu Tempat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <script>
        // Double-click logo untuk admin login hint
        let logoClickCount = 0;
        const logoElement = document.querySelector('.logo');
        
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.logo');
            if (logo) {
                logo.addEventListener('dblclick', function(e) {
                    e.preventDefault();
                    // Simple check: if user is not logged in, redirect to login page
                    const userMenu = document.querySelector('.user-menu');
                    if (!userMenu) {
                        // User not logged in - show hint and redirect
                        if (confirm('Masuk sebagai Admin?\n\nKlik OK untuk lanjut ke halaman login.')) {
                            window.location.href = 'pages/user/user_login.php';
                        }
                    }
                });
            }
        });
    </script>
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
                <a href="#beranda" class="nav-link active">BERANDA</a>
                <a href="#kontak" class="nav-link">KONTAK</a>
                <?php if ($user): ?>
                    <!-- User logged in -->
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                            <a href="pages/user/user_profile.php"><?php echo htmlspecialchars($user['username']); ?></a>
                        </div>
                        <a href="pages/user/user_logout.php" class="logout-link">Logout</a>
                    </div>
                <?php else: ?>
                    <!-- User not logged in -->
                    <div class="auth-links">
                        <a href="pages/user/user_login.php" class="login-link">LOGIN</a>
                        <a href="pages/user/user_register.php" class="register-link">DAFTAR</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section id="beranda" class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Beranda</h1>
                <h2 class="hero-subtitle">GAMEVO — Penuhi Semua Kebutuhan Gaming Mu Dalam Satu Tempat</h2>
                <div class="hero-highlight">
                    <h3>Product Unggulan!</h3>
                    <p>tinggal klik dan nikmati kemudahan gamingmu!</p>
                </div>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="products-section">
            <div class="products-container">
                <!-- Product 1 -->
                <a href="pages/user/user_top-up.php?game=roblox" class="product-card">
                    <div class="product-image">
                        <img src="assets/images/rblx_icon.jpg" alt="Roblox">
                    </div>
                    <div class="product-info">
                        <h4>Roblox</h4>
                    </div>  
                </a>

                <!-- Product 2 -->
                <a href="pages/user/user_top-up.php?game=mobile-legends" class="product-card">
                    <div class="product-image">
                        <img src="assets/images/ml_icon.jpg" alt="Mobile Legends">
                    </div>
                    <div class="product-info">
                        <h4>Mobile Legends</h4>
                    </div>
                </a>

                <!-- Product 3 -->
                <a href="pages/user/user_top-up.php?game=pubg" class="product-card">
                    <div class="product-image">
                        <img src="assets/images/pubg_icon.jpg" alt="PUBG">
                    </div>
                    <div class="product-info">
                        <h4>Player Unknown Battlegrounds</h4>
                    </div>
                </a>

                <!-- Product 4 -->
                <a href="pages/user/user_top-up.php?game=genshin-impact" class="product-card">
                    <div class="product-image">
                        <img src="assets/images/gi_icon.jpeg" alt="Genshin Impact">
                    </div>
                    <div class="product-info">
                        <h4>Genshin Impact</h4>
                    </div>
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-container">
                <div class="features-header">
                    <h2>Website top-up game paling terpercaya di Indonesia!</h2>
                    <p>Setiap harinya, ribuan gamers di Indonesia menggunakan Bushido untuk melakukan top up game dengan lancar, tanpa perlu daftar atau login, dan diamonds/token game akan dikirimkan secara instan ke akun game anda. Top up game Mobile Legends, Free Fire, CODM, Speed Drifter dan berbagai macam game lainnya.</p>
                </div>
                <div class="features-grid">
                    <!-- Feature 1 -->
                    <div class="feature-card">
                        <div class="feature-icon">✓</div>
                        <h3>Pengiriman Instan</h3>
                        <p>Hanya butuh beberapa detik saja untuk menyelesaikan transaksi anda. Semua proses kami berjalan secara otomatis.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card">
                        <div class="feature-icon">🎧</div>
                        <h3>Promosi-promosi Menarik</h3>
                        <p>Dapatkan promo harga terbaik yang bisa anda dapatkan setiap minggunya, ikuti terus kami di sosial media.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card">
                        <div class="feature-icon">💳</div>
                        <h3>Metode Pembayaran Lengkap</h3>
                        <p>Kami menawarkan begitu banyak pilihan channel pembayaran, mulai dari bank transfer, gopay, ovo, shopee pay, dan lainnya.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card">
                        <div class="feature-icon">💼</div>
                        <h3>Jujur & Terpercaya</h3>
                        <p>Setiap hari ada ribuan transaksi top-up game atau pembelian voucher yang dilakukan oleh pelanggan kami.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card">
                        <div class="feature-icon">🎧</div>
                        <h3>Customer Care 24/7</h3>
                        <p>Custome Support kami siap membantu anda setiap hari, 7 hari dalam seminggu dan 30 hari dalam sebulan.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card">
                        <div class="feature-icon">🎧</div>
                        <h3>Pasti Lebih Murah</h3>
                        <p>Top-up game favorit anda dengan harga yang paling murah dibandingkan website top-up lainnya.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Media Section -->
        <section class="social-section">
            <div class="social-container">
                <h2>Social Media</h2>
                <h3>GAMEVO</h3>
                <div class="social-grid">
                    <!-- Instagram -->
                    <div class="social-card">
                        <div class="social-icon instagram">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.057-1.645.069-4.849.069-3.204 0-3.584-.012-4.849-.069-3.259-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                <circle cx="12" cy="12" r="3.6"/>
                                <circle cx="18.5" cy="5.5" r="1.6"/>
                            </svg>
                        </div>
                        <h4>@gamevostore.id</h4>
                    </div>

                    <!-- TikTok -->
                    <div class="social-card">
                        <div class="social-icon tiktok">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.1 1.75 2.9 2.9 0 0 1 2.31-4.64 2.84 2.84 0 0 1 .88.13V9.4a6.09 6.09 0 0 0-1-.1 6 6 0 1 0 10.86 3.1A5.9 5.9 0 0 0 19.59 6.69Z"/>
                            </svg>
                        </div>
                        <h4>GAMEVO_TEAM</h4>
                    </div>

                    <!-- YouTube -->
                    <div class="social-card">
                        <div class="social-icon youtube">
                            <svg viewBox="0 0 24 24" fill="white">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </div>
                        <h4>GAMEVO OFFICIAL</h4>
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
</body>
</html>
