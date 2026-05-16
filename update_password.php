<?php
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$token = $_GET['token'] ?? '';
$error = null;
$success = null;

if (empty($token)) {
    $error = "Invalid or missing password reset token.";
} else {
    // Check if token is valid and not expired
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = "This password reset link is invalid or has expired.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && !$success) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear token
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hashed, $user->id]);
        
        $success = "Your password has been successfully reset! You can now log in with your new password.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                    <h2 class="font-weight-bold">Create New Password</h2>
                    <?php if(!$success && !$error): ?>
                        <p class="text-muted">Enter a new strong password below</p>
                    <?php endif; ?>
                </div>
                
                <?php if($error): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle mb-2 fa-2x"></i><br>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <div class="text-center mt-4">
                        <a href="reset_password.php" class="btn btn-outline-primary">Request New Link</a>
                    </div>
                <?php elseif($success): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle mb-2 fa-2x"></i><br>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <div class="text-center mt-4">
                        <a href="login.php" class="btn btn-success btn-lg font-weight-bold shadow-sm px-4">Log In Now</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">New Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required minlength="6" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg" required minlength="6" placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-warning btn-block btn-lg mt-4 font-weight-bold shadow-sm">Save New Password</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
