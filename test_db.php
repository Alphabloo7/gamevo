<?php
/**
 * Test Database Connection
 */
require_once '../config/database.php';

global $conn;

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head><title>Test DB Connection</title></head>";
echo "<body style='font-family: Arial; padding: 20px;'>";

// Test connection
if ($conn->connect_error) {
    echo "<h2 style='color: red;'>❌ Connection Error</h2>";
    echo "<p>Error: " . htmlspecialchars($conn->connect_error) . "</p>";
} else {
    echo "<h2 style='color: green;'>✅ Connection OK</h2>";
    
    // Test games query
    echo "<h3>📋 Games in Database:</h3>";
    $result = $conn->query("SELECT id, slug, name, currency FROM games WHERE is_active = TRUE ORDER BY name");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
        echo "<tr><th>ID</th><th>Slug</th><th>Name</th><th>Currency</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['slug']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['currency']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No games found</p>";
        echo "<p>Error: " . $conn->error . "</p>";
    }
    
    // Test packages
    echo "<h3>📦 Sample Packages:</h3>";
    $pkg_result = $conn->query("SELECT gp.id, g.name as game_name, gp.name, gp.amount, gp.price FROM game_packages gp JOIN games g ON gp.game_id = g.id LIMIT 5");
    
    if ($pkg_result && $pkg_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
        echo "<tr><th>ID</th><th>Game</th><th>Package</th><th>Amount</th><th>Price</th></tr>";
        while ($row = $pkg_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['game_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . $row['amount'] . "</td>";
            echo "<td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No packages found</p>";
        echo "<p>Error: " . $conn->error . "</p>";
    }
}

echo "</body></html>";
?>
