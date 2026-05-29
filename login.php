<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        
        if ($user->role === 'driver') {
            header("Location: driver/index.php");
            exit;
        }
        
        $redirect = $_SESSION['redirect_to'] ?? 'index.php';
        unset($_SESSION['redirect_to']);
        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                    <h2 class="font-weight-bold">Welcome Back</h2>
                    <p class="text-muted">Please login to your account</p>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="font-weight-bold">Email address</label>
                        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="font-weight-bold mb-0">Password</label>
                            <a href="reset_password.php" class="text-primary small font-weight-bold">Forgot password?</a>
                        </div>
                        <input type="password" name="password" class="form-control mt-2" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">Login</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Don't have an account? <a href="register.php" class="font-weight-bold">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
