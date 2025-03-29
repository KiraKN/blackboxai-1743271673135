<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/middleware.php';

$adminId = authenticateRequest(true); // Requires admin

echo json_encode([
    'success' => true,
    'message' => 'Admin access granted', 
    'admin_id' => $adminId
]);