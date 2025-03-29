<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

$result = loginUser($data['email'], $data['password']);

if ($result['success']) {
    $token = createAuthToken($result['user_id']);
    if ($token) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $result['user_id'],
                'name' => $result['name']
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create session']);
    }
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $result['message']]);
}