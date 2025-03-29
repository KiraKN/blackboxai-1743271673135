<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/middleware.php';

$userId = authenticateRequest();
$db = getDBConnection();

// Get token from header
$headers = getallheaders();
$token = str_replace('Bearer ', '', $headers['Authorization']);

// Delete the token
$stmt = $db->prepare("DELETE FROM auth_tokens WHERE token = ?");
if ($stmt->execute([$token])) {
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Logout failed']);
}