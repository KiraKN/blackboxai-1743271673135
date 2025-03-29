<?php
require_once __DIR__ . '/../config/database.php';

function registerUser($email, $password, $name, $phone) {
    $db = getDBConnection();
    
    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (email, password_hash, name, phone) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$email, $passwordHash, $name, $phone])) {
        return ['success' => true, 'user_id' => $db->lastInsertId()];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function loginUser($email, $password) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT id, password_hash, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    if (password_verify($password, $user['password_hash'])) {
        return [
            'success' => true,
            'user_id' => $user['id'],
            'name' => $user['name']
        ];
    } else {
        return ['success' => false, 'message' => 'Invalid password'];
    }
}

function createAuthToken($userId) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 day'));
    
    $db = getDBConnection();
    $stmt = $db->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$userId, $token, $expires])) {
        return $token;
    }
    return false;
}

function validateToken($token) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT user_id FROM auth_tokens WHERE token = ? AND expires_at > datetime('now')");
    $stmt->execute([$token]);
    $data = $stmt->fetch();
    
    if ($data) {
        return $data['user_id'];
    }
    return false;
}