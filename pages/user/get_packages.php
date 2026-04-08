<?php
/**
 * GAMEVO - API Get Game Packages
 * Fetch game packages dynamically
 */
require_once '../../config/database.php';

header('Content-Type: application/json');

$game_slug = isset($_GET['game']) ? trim($_GET['game']) : null;

if (!$game_slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Game parameter required']);
    exit;
}

global $conn;

// Get game info
$stmt = $conn->prepare("SELECT id, slug, name, title, description, currency, image_url FROM games WHERE slug = ? AND is_active = TRUE");
$stmt->bind_param("s", $game_slug);
$stmt->execute();
$game_result = $stmt->get_result();

if ($game_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Game not found']);
    exit;
}

$game = $game_result->fetch_assoc();
$game_id = $game['id'];
$stmt->close();

// Get packages for game
$stmt = $conn->prepare("SELECT id, name, amount, price FROM game_packages WHERE game_id = ? AND is_active = TRUE ORDER BY amount ASC");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$packages_result = $stmt->get_result();

$packages = [];
while ($row = $packages_result->fetch_assoc()) {
    $packages[] = [
        'id' => intval($row['id']),
        'name' => $row['name'],
        'amount' => intval($row['amount']),
        'price' => floatval($row['price']),
        'price_formatted' => 'Rp ' . number_format($row['price'], 0, ',', '.')
    ];
}
$stmt->close();

// Return response
echo json_encode([
    'success' => true,
    'game' => [
        'slug' => $game['slug'],
        'name' => $game['name'],
        'title' => $game['title'],
        'description' => $game['description'],
        'currency' => $game['currency'],
        'image' => $game['image_url']
    ],
    'packages' => $packages
]);
