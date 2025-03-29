<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/middleware.php';

$adminId = authenticateRequest(true); // Admin access required
$db = getDBConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Handle both list and single user requests
        if (isset($_GET['id'])) {
            // Get single user
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        } else {
            // List all users with pagination
            $page = $_GET['page'] ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            $stmt = $db->prepare("SELECT * FROM users LIMIT ? OFFSET ?");
            $stmt->execute([$limit, $offset]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $total = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'users' => $users,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $limit,
                    'current_page' => $page
                ]
            ]);
        }
        break;
        
    case 'PUT':
        // Update user
        parse_str(file_get_contents("php://input"), $data);
        $userId = $data['id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit;
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['name'])) {
            $updates[] = 'name = ?';
            $params[] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $updates[] = 'email = ?';
            $params[] = $data['email'];
        }
        
        if (isset($data['status'])) {
            $updates[] = 'status = ?';
            $params[] = $data['status'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            exit;
        }
        
        $params[] = $userId;
        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute($params)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        break;
        
    case 'DELETE':
        // Delete user
        parse_str(file_get_contents("php://input"), $data);
        $userId = $data['id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit;
        }
        
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        
        if ($stmt->execute([$userId])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}