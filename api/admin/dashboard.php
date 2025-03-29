<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/middleware.php';

$adminId = authenticateRequest(true); // Admin access required

$db = getDBConnection();

// Get system stats
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'active_sessions' => $db->query("SELECT COUNT(*) FROM auth_tokens WHERE expires_at > datetime('now')")->fetchColumn(),
    'total_orders' => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'admin_users' => $db->query("SELECT id, email, permissions FROM admin_users")->fetchAll(PDO::FETCH_ASSOC)
];

// Get recent activity
$recentActivity = $db->query(
    "SELECT * FROM (
        SELECT 'user' as type, id, email, created_at FROM users ORDER BY created_at DESC LIMIT 5
        UNION ALL
        SELECT 'order' as type, id, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5
    ) ORDER BY created_at DESC LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'recent_activity' => $recentActivity,
    'system_status' => [
        'database' => 'online',
        'authentication' => 'active',
        'last_updated' => date('Y-m-d H:i:s')
    ]
]);