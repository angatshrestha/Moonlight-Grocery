<?php
// verify_otp.php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$userOtp = trim($_POST['otp'] ?? '');

if (empty($userOtp)) {
    echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
    exit;
}

$sessionOtp = $_SESSION['otp_code'] ?? null;
$sessionExpiry = $_SESSION['otp_expiry'] ?? 0;

if (!$sessionOtp) {
    echo json_encode(['success' => false, 'message' => 'No active OTP found. Please send OTP first.']);
    exit;
}

if (time() > $sessionExpiry) {
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
    exit;
}

if (strval($userOtp) === strval($sessionOtp)) {
    $_SESSION['otp_verified'] = true;
    echo json_encode([
        'success' => true,
        'message' => 'Phone number verified successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid verification code. Please try again.'
    ]);
}
exit;
?>
