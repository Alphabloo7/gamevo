<?php
/**
 * GAMEVO - Admin Game Packages Management (DEBUG VERSION)
 */
require_once '../../config/database.php';

// DEBUG: Skip auth check
// require_once '../../includes/admin_auth.php';
// requireAdminLogin();

global $conn;
// $admin = getCurrentAdmin();

$message = '';
$message_type = '';

// Get all games
echo "<!-- DEBUG: Fetching games -->";
$games_result = $conn->query("SELECT id, slug, name, currency FROM games WHERE is_active = TRUE ORDER BY name");
echo "<!-- Games Query: " . ($conn->error ?: "OK") . " -->";

$games = [];
if ($games_result) {
    while ($row = $games_result->fetch_assoc()) {
        $games[] = $row;
    }
}
echo "<!-- Games Found: " . count($games) . " -->";

// Get selected game
$selected_game_id = isset($_GET['game']) ? intval($_GET['game']) : (count($games) > 0 ? $games[0]['id'] : null);
echo "<!-- Selected Game ID: " . $selected_game_id . " -->";

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
    <title>Kelola Paket Game - Admin GAMEVO (DEBUG)</title>
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
            padding: 30px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        h1 {
            margin-bottom: 30px;
            text-align: center;
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
        }
        
        .packages-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .packages-table thead {
            background: rgba(102, 126, 234, 0.2);
        }
        
        .packages-table th, .packages-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .price-display {
            color: #4CAF50;
            font-weight: 600;
        }
        
        .form-section {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
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
        
        .debug-info {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-family: monospace;
            font-size: 12px;
            color: #aaa;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Kelola Paket Game (DEBUG)</h1>
        
        <div class="debug-info">
            Games: <?php echo count($games); ?> | 
            Selected Game: <?php echo $selected_game_id; ?> |
            Packages: <?php echo count($packages); ?>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($games)): ?>
            <div class="empty-state">
                <h2>📭 Belum ada game (Games: 0)</h2>
                <p>Database tidak ada games</p>
            </div>
        <?php else: ?>
            
            <div class="game-selector">
                <?php foreach ($games as $game): ?>
                    <a href="?game=<?php echo $game['id']; ?>" 
                       class="game-btn <?php echo $selected_game_id == $game['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($game['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <?php if ($selected_game && !empty($packages)): ?>
                <div class="packages-section">
                    <h2 class="section-title">Paket <?php echo htmlspecialchars($selected_game['name']); ?></h2>
                    <table class="packages-table">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th>Jumlah <?php echo htmlspecialchars($selected_game['currency']); ?></th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($package['name']); ?></td>
                                    <td><?php echo number_format($package['amount']); ?></td>
                                    <td class="price-display">Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <?php if ($selected_game): ?>
                <div class="form-section">
                    <h2 class="section-title">Tambah Paket Baru</h2>
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
                                <input type="number" name="amount" placeholder="400" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Harga (Rp)</label>
                                <input type="number" name="price" placeholder="35000" min="0" step="1000" required>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">Tambah Paket</button>
                    </form>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</body>
</html>
