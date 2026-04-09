<?php
/**
 * GAMEVO - Admin Real-time Data API
 * Fetch live dashboard data via AJAX
 */
require_once '../../includes/admin_auth.php';

// Check admin login
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set JSON header
header('Content-Type: application/json');

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$action = $_GET['action'] ?? 'all';

try {
    $response = [];
    
    if ($action === 'all' || $action === 'stats') {
        // Get real-time statistics
        $response['today'] = getTodaysSalesSummary();
        $response['realtime'] = getRealtimeSalesData(60);
        $response['transactions'] = getLatestTransactions(10);
    }
    
    if ($action === 'today') {
        $response = getTodaysSalesSummary();
    }
    
    if ($action === 'realtime') {
        $response = getRealtimeSalesData(60);
    }
    
    if ($action === 'transactions') {
        $response = getLatestTransactions(15);
    }
    
    // Add timestamp
    $response['timestamp'] = date('Y-m-d H:i:s');
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
