<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/middleware.php';

$userId = authenticateRequest();

echo json_encode([
    'success' => true,
    'message' => 'Access granted to protected resource',
    'user_id' => $userId
]);