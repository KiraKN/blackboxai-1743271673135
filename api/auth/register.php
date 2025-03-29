<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/auth.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Register user
$result = registerUser(
    $data['email'],
    $data['password'],
    $data['name'],
    $data['phone'] ?? null
);

if ($result['success']) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'user_id' => $result['user_id']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}