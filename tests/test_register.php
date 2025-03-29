<?php
// Test data
$testUser = [
    'email' => 'test@example.com',
    'password' => 'securepassword123',
    'name' => 'Test User',
    'phone' => '+1234567890'
];

// Initialize cURL
$ch = curl_init('http://localhost/api/auth/register.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testUser));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Execute and get response
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output results
echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

// Verify response
$data = json_decode($response, true);
if ($httpCode === 201 && isset($data['success']) && $data['success']) {
    echo "TEST PASSED: User registration successful\n";
    exit(0);
} else {
    echo "TEST FAILED: User registration failed\n";
    if (isset($data['message'])) {
        echo "Error: " . $data['message'] . "\n";
    }
    exit(1);
}