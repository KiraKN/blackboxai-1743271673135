<?php
// Test credentials (should match registration test)
$credentials = [
    'email' => 'test@example.com',
    'password' => 'securepassword123'
];

$ch = curl_init('http://localhost/api/auth/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

$data = json_decode($response, true);
if ($httpCode === 200 && isset($data['success']) && $data['success'] && isset($data['token'])) {
    echo "TEST PASSED: Login successful\n";
    file_put_contents('tests/auth_token.txt', $data['token']);
    exit(0);
} else {
    echo "TEST FAILED: Login failed\n";
    if (isset($data['message'])) {
        echo "Error: " . $data['message'] . "\n";
    }
    exit(1);
}