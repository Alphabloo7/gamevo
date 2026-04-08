<?php
/**
 * GAMEVO - Database Import Setup Script
 * Import database.sql to MySQL
 */

require_once '../config/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - GAMEVO</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f44336;
        }
        
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            width: 100%;
            margin-top: 10px;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .button.secondary {
            background: #666;
            margin-top: 10px;
        }
        
        .button.secondary:hover {
            background: #555;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }
        
        .log {
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Setup Database GAMEVO</h1>
        <p class="subtitle">Import tabel dan data default ke database</p>
        
        <?php
        global $conn;
        
        // Check if database connection exists
        if ($conn->connect_error) {
            echo '<div class="status error">❌ Gagal terhubung ke database: ' . htmlspecialchars($conn->connect_error) . '</div>';
            echo '<p class="info-box">Pastikan MySQL sudah berjalan dan konfigurasi database.php sudah benar.</p>';
            exit;
        }
        
        // Check if tables already exist
        $tables_to_check = ['games', 'game_packages'];
        $tables_exist = true;
        $missing_tables = [];
        
        foreach ($tables_to_check as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if (!$result || $result->num_rows === 0) {
                $tables_exist = false;
                $missing_tables[] = $table;
            }
        }
        
        if ($tables_exist) {
            echo '<div class="status success">✅ Database sudah tersedia!</div>';
            echo '<p class="info-box">Semua tabel telah dibuat. Anda bisa mulai menggunakan aplikasi.</p>';
            echo '<a href="../pages/admin/admin_packages.php" class="button">Kelola Paket Game</a>';
            echo '<a href="../index.php" class="button secondary">Kembali ke Beranda</a>';
            exit;
        }
        
        // If import requested
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import'])) {
            echo '<div class="status info">⏳ Sedang import database...</div>';
            
            $log_output = '';
            
            try {
                // Read database.sql
                $sql_file = dirname(__DIR__) . '/database.sql';
                if (!file_exists($sql_file)) {
                    throw new Exception('File database.sql tidak ditemukan di: ' . $sql_file);
                }
                
                $sql_content = file_get_contents($sql_file);
                
                // Split by semicolon and execute each statement
                $statements = array_filter(array_map('trim', explode(';', $sql_content)));
                
                $executed = 0;
                $errors = [];
                
                foreach ($statements as $statement) {
                    if (empty($statement) || substr(trim($statement), 0, 2) === '--') {
                        continue;
                    }
                    
                    if ($conn->query($statement) === TRUE) {
                        $executed++;
                        $log_output .= "✓ Executed successfully\n";
                    } else {
                        $error = $conn->error;
                        // Skip duplicate table errors
                        if (strpos($error, 'already exists') === false) {
                            $errors[] = $error;
                            $log_output .= "✗ Error: " . htmlspecialchars($error) . "\n";
                        } else {
                            $log_output .= "✓ Table already exists (skipped)\n";
                        }
                    }
                }
                
                if (empty($errors)) {
                    echo '<div class="status success">✅ Database berhasil di-import!</div>';
                    echo '<p class="info-box">Total queries dijalankan: ' . $executed . '</p>';
                    echo '<a href="../pages/admin/admin_packages.php" class="button">Kelola Paket Game</a>';
                    echo '<a href="../index.php" class="button secondary">Kembali ke Beranda</a>';
                } else {
                    echo '<div class="status error">⚠️ Import selesai dengan beberapa error</div>';
                    echo '<p class="info-box">Total queries berhasil: ' . $executed . '</p>';
                    echo '<div class="log">' . htmlspecialchars($log_output) . '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="status error">❌ Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            
        } else {
            // Show import button
            echo '<div class="status error">⚠️ Tabel berikut belum ada di database:</div>';
            echo '<div class="info-box">';
            foreach ($missing_tables as $table) {
                echo '• ' . htmlspecialchars($table) . '<br>';
            }
            echo '</div>';
            
            echo '<div class="info-box">';
            echo '<strong>Langkah selanjutnya:</strong><br>';
            echo '1. Klik tombol "Import Database" di bawah<br>';
            echo '2. Sistem akan otomatis membuat tabel dan memasukkan data default<br>';
            echo '3. Setelah itu Anda bisa langsung gunakan sistem';
            echo '</div>';
            
            echo '<form method="POST">';
            echo '<input type="hidden" name="import" value="1">';
            echo '<button type="submit" class="button">📥 Import Database</button>';
            echo '</form>';
            
            echo '<a href="../index.php" class="button secondary">Kembali ke Beranda</a>';
        }
        ?>
    </div>
</body>
</html>
