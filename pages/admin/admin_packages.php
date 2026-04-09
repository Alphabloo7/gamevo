<?php
/**
 * GAMEVO - Admin Game Packages Management
 * Manage game packages and prices dynamically
 */
require_once '../../includes/admin_auth.php';
require_once '../../config/database.php';

// Clear cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

requireAdminLogin();

global $conn;
$admin = getCurrentAdmin();

$message = '';
$message_type = '';

// Get all games
$games_result = $conn->query("SELECT id, slug, name, currency FROM games WHERE is_active = TRUE ORDER BY name");
$games = [];
while ($row = $games_result->fetch_assoc()) {
    $games[] = $row;
}

// Get selected game
$selected_game_id = isset($_GET['game']) ? intval($_GET['game']) : (count($games) > 0 ? $games[0]['id'] : null);

// Get packages for selected game
$packages = [];
if ($selected_game_id) {
    $stmt = $conn->prepare("SELECT id, name, amount, price, is_active FROM game_packages WHERE game_id = ? ORDER BY amount ASC");
    $stmt->bind_param("i", $selected_game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    $stmt->close();
}

// Handle add/edit package
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add_package') {
        $package_name = trim($_POST['package_name'] ?? '');
        $amount = intval($_POST['amount'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $game_id = intval($_POST['game_id'] ?? 0);
        
        if (empty($package_name) || $amount <= 0 || $price <= 0) {
            $message = 'Mohon lengkapi semua field dengan benar';
            $message_type = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO game_packages (game_id, name, amount, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isid", $game_id, $package_name, $amount, $price);
            
            if ($stmt->execute()) {
                $message = '✓ Paket berhasil ditambahkan';
                $message_type = 'success';
                header("refresh:2;url=admin_packages.php?game=$game_id");
            } else {
                $message = 'Gagal menambahkan paket: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($action === 'edit_package') {
        $package_id = intval($_POST['package_id'] ?? 0);
        $package_name = trim($_POST['package_name'] ?? '');
        $amount = intval($_POST['amount'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        
        if ($package_id <= 0 || empty($package_name) || $amount <= 0 || $price <= 0) {
            $message = 'Mohon lengkapi semua field dengan benar';
            $message_type = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE game_packages SET name = ?, amount = ?, price = ? WHERE id = ?");
            $stmt->bind_param("sidi", $package_name, $amount, $price, $package_id);
            
            if ($stmt->execute()) {
                $message = '✓ Paket berhasil diupdate';
                $message_type = 'success';
                header("refresh:2;url=admin_packages.php?game=$selected_game_id");
            } else {
                $message = 'Gagal mengupdate paket: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($action === 'delete_package') {
        $package_id = intval($_POST['package_id'] ?? 0);
        
        if ($package_id > 0) {
            $stmt = $conn->prepare("DELETE FROM game_packages WHERE id = ?");
            $stmt->bind_param("i", $package_id);
            
            if ($stmt->execute()) {
                $message = '✓ Paket berhasil dihapus';
                $message_type = 'success';
                header("refresh:2;url=admin_packages.php?game=$selected_game_id");
            } else {
                $message = 'Gagal menghapus paket: ' . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($action === 'add_game') {
        $game_slug = trim($_POST['game_slug'] ?? '');
        $game_name = trim($_POST['game_name'] ?? '');
        $game_currency = trim($_POST['game_currency'] ?? '');
        $game_description = trim($_POST['game_description'] ?? '');
        
        if (empty($game_slug) || empty($game_name) || empty($game_currency)) {
            $message = 'Field Slug, Nama Game, dan Currency wajib diisi';
            $message_type = 'error';
        } elseif (empty($_FILES['game_image']['name'])) {
            $message = 'File gambar wajib diupload';
            $message_type = 'error';
        } else {
            // Validate file upload
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            $file = $_FILES['game_image'];
            
            if (!in_array($file['type'], $allowed_types)) {
                $message = 'Hanya format JPG, PNG, GIF, atau WEBP yang diizinkan';
                $message_type = 'error';
            } elseif ($file['size'] > $max_size) {
                $message = 'Ukuran file tidak boleh lebih dari 2MB';
                $message_type = 'error';
            } else {
                // Check if slug already exists
                $check_stmt = $conn->prepare("SELECT id FROM games WHERE slug = ?");
                $check_stmt->bind_param("s", $game_slug);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows > 0) {
                    $message = 'Slug game sudah digunakan';
                    $message_type = 'error';
                    $check_stmt->close();
                } else {
                    $check_stmt->close();
                    
                    // Create upload directory if not exists
                    $upload_dir = '../../assets/uploads/games/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = 'game_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                    $file_path = $upload_dir . $new_filename;
                    $db_path = 'assets/uploads/games/' . $new_filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        $stmt = $conn->prepare("INSERT INTO games (slug, name, currency, description, image_url, is_active) VALUES (?, ?, ?, ?, ?, TRUE)");
                        $stmt->bind_param("sssss", $game_slug, $game_name, $game_currency, $game_description, $db_path);
                        if ($stmt->execute()) {
                            $message = '✓ Game berhasil ditambahkan';
                            $message_type = 'success';
                            header("refresh:2;url=admin_packages.php");
                        } else {
                            $message = 'Gagal menambahkan game: ' . $stmt->error;
                            $message_type = 'error';
                        }
                        $stmt->close();
                    } else {
                        $message = 'Gagal mengupload file gambar';
                        $message_type = 'error';
                    }
                }
            }
        }
    }
}

// Get selected game data
$selected_game = null;
foreach ($games as $game) {
    if ($game['id'] == $selected_game_id) {
        $selected_game = $game;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Game - Admin GAMEVO</title>
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
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar-menu li a.active {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border-left-color: #667eea;
        }
        
        .main-content {
            margin-left: 260px;
            flex: 1;
        }
        
        .topbar {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-title h1 {
            font-size: 24px;
        }
        
        .logout-btn {
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            border: 1px solid rgba(244, 67, 54, 0.5);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(244, 67, 54, 0.3);
        }
        
        .content {
            padding: 30px;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message.success {
            background: rgba(76, 175, 80, 0.2);
            color: #c8e6c9;
            border-left: 4px solid #4CAF50;
        }
        
        .message.error {
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            border-left: 4px solid #f44336;
        }
        
        .game-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .game-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .game-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .game-btn.active {
            background: rgba(102, 126, 234, 0.3);
            border-color: #667eea;
            color: #667eea;
        }
        
        .packages-section {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .packages-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .packages-table thead {
            background: rgba(102, 126, 234, 0.2);
        }
        
        .packages-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .packages-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .packages-table tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .price-display {
            font-size: 18px;
            font-weight: 600;
            color: #4CAF50;
        }
        
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #667eea;
            color: white;
        }
        
        .btn-edit:hover {
            background: #5568d3;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
            margin-left: 5px;
        }
        
        .btn-delete:hover {
            background: #da190b;
        }
        
        .form-section {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 30px;
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.15);
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: #0f172a;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="admin_dashboard.php" class="sidebar-logo">GAMEVO ADMIN</a>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin_packages.php" class="active"><i class="fas fa-box"></i> Paket Game</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> Kelola Users</a></li>
                <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Kelola Paket Game</h1>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button class="btn-add-game" onclick="openAddGameModal()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                        <i class="fas fa-plus"></i> Tambah Game
                    </button>
                    <a href="admin_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <!-- Content -->
            <div class="content">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($games)): ?>
                    <!-- No Games Message -->
                    <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                        <h2 style="color: #999; margin-bottom: 20px;">📭 Belum ada game</h2>
                        <p style="color: #999; margin-bottom: 30px;">Silakan jalankan setup database untuk menambahkan game default</p>
                        <a href="../../setup/import_database.php" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                            🔧 Setup Database
                        </a>
                    </div>
                <?php else: ?>
                
                <!-- Game Selector -->
                <div class="game-selector">
                    <?php foreach ($games as $game): ?>
                        <a href="admin_packages.php?game=<?php echo $game['id']; ?>" 
                           class="game-btn <?php echo $selected_game_id == $game['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($game['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($selected_game): ?>
                    <!-- Packages Table -->
                    <div class="packages-section">
                        <h2 class="section-title">
                            <i class="fas fa-list"></i> Paket <?php echo htmlspecialchars($selected_game['name']); ?>
                        </h2>
                        
                        <?php if (!empty($packages)): ?>
                            <table class="packages-table">
                                <thead>
                                    <tr>
                                        <th>Nama Paket</th>
                                        <th>Jumlah <?php echo htmlspecialchars($selected_game['currency']); ?></th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packages as $package): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($package['name']); ?></td>
                                            <td><?php echo number_format($package['amount']); ?></td>
                                            <td class="price-display">Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></td>
                                            <td>
                                                <button class="btn-edit" onclick="editPackage(<?php echo htmlspecialchars(json_encode($package)); ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                                                    <input type="hidden" name="action" value="delete_package">
                                                    <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
                                                    <button type="submit" class="btn-delete">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>Belum ada paket untuk game ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Add/Edit Form -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-plus-circle"></i> Tambah Paket Baru
                        </h2>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="add_package">
                            <input type="hidden" name="game_id" value="<?php echo $selected_game_id; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nama Paket</label>
                                    <input type="text" name="package_name" placeholder="Contoh: 400 Robux" required>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah <?php echo htmlspecialchars($selected_game['currency']); ?></label>
                                    <input type="number" name="amount" placeholder="Contoh: 400" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <input type="number" name="price" placeholder="Contoh: 35000" min="0" step="1000" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-plus"></i> Tambah Paket
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Game Modal -->
    <div id="addGameModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Game Baru</h2>
                <button class="modal-close" onclick="closeAddGameModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_game">
                
                <div class="form-group">
                    <label>Slug Game</label>
                    <input type="text" name="game_slug" placeholder="Contoh: mobile-legends" required>
                    <small style="color: #999;">Gunakan huruf kecil dan tanda hubung (-)</small>
                </div>
                
                <div class="form-group">
                    <label>Nama Game</label>
                    <input type="text" name="game_name" placeholder="Contoh: Mobile Legends" required>
                </div>
                
                <div class="form-group">
                    <label>Currency (Mata Uang)</label>
                    <input type="text" name="game_currency" placeholder="Contoh: Diamond" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi Game</label>
                    <input type="text" name="game_description" placeholder="Deskripsi singkat tentang game" style="height: 80px; padding: 12px; resize: vertical;">
                </div>
                
                <div class="form-group">
                    <label>Gambar Game</label>
                    <input type="file" name="game_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                    <small style="color: #999;">Format: JPG, PNG, GIF, WEBP. Max 2MB</small>
                </div>
                
                <button type="submit" class="submit-btn" style="width: 100%;">
                    <i class="fas fa-plus"></i> Tambah Game
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Paket</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="edit_package">
                <input type="hidden" name="package_id" id="edit_package_id">
                
                <div class="form-group">
                    <label>Nama Paket</label>
                    <input type="text" name="package_name" id="edit_package_name" required>
                </div>
                
                <div class="form-group">
                    <label id="edit_currency_label">Jumlah</label>
                    <input type="number" name="amount" id="edit_amount" min="1" required>
                </div>
                
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" min="0" step="1000" required>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAddGameModal() {
            document.getElementById('addGameModal').classList.add('show');
        }
        
        function closeAddGameModal() {
            document.getElementById('addGameModal').classList.remove('show');
        }
        
        function editPackage(package) {
            document.getElementById('edit_package_id').value = package.id;
            document.getElementById('edit_package_name').value = package.name;
            document.getElementById('edit_amount').value = package.amount;
            document.getElementById('edit_price').value = package.price;
            document.getElementById('editModal').classList.add('show');
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }
        
        window.addEventListener('click', function(event) {
            const addModal = document.getElementById('addGameModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target === addModal) {
                closeAddGameModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
