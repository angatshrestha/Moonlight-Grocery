<?php
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Generate a secure token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user in database
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);
    
    // Check if the email actually existed (optional, but good for local testing)
    if ($stmt->rowCount() > 0) {
        $resetLink = "update_password.php?token=" . $token;
        $success = "Normally, an email would be sent to $email. For this local test environment, click the secure link below to reset your password:";
    } else {
        $success = "If an account exists with that email, a reset link has been sent.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-key fa-3x text-primary mb-3"></i>
                    <h2 class="font-weight-bold">Reset Password</h2>
                    <p class="text-muted">Enter your email to receive a reset link</p>
                </div>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-envelope-open-text mb-2 fa-2x"></i><br>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    
                    <?php if(isset($resetLink)): ?>
                        <div class="text-center mt-3 mb-4">
                            <a href="<?php echo $resetLink; ?>" class="btn btn-warning btn-lg font-weight-bold shadow"><i class="fas fa-unlock-alt mr-2"></i>Reset Password Now</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4 border-top pt-3">
                        <a href="login.php" class="text-muted"><i class="fas fa-arrow-left mr-1"></i> Return to Login</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">Email address</label>
                            <input type="email" name="email" class="form-control form-control-lg" required placeholder="name@example.com">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4 font-weight-bold">Send Reset Link</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Remembered your password? <a href="login.php" class="font-weight-bold">Log in here</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
