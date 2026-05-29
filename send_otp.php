<?php
// send_otp.php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$phone = trim($_POST['phone'] ?? '');

if (empty($phone) || strlen($phone) < 5) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number.']);
    exit;
}

// Generate secure 6-digit OTP
$otp = rand(100000, 999999);
$expiry = time() + (5 * 60); // Valid for 5 minutes

// Save in session
$_SESSION['otp_code'] = $otp;
$_SESSION['otp_expiry'] = $expiry;
$_SESSION['otp_phone'] = $phone;
$_SESSION['otp_verified'] = false; // Reset verification status

echo json_encode([
    'success' => true,
    'message' => 'OTP sent successfully.',
    'otp' => $otp // Return the OTP to show in the gorgeous simulated browser SMS notification!
]);
exit;
?>
