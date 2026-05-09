<?php
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold">Create an Account</h2>
                    <p class="text-muted">Join Moonlight Grocery today</p>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="font-weight-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email address</label>
                        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Confirm Password</label>
                        <input type="password" name="confirm" class="form-control" required minlength="6" placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">Sign Up</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Already have an account? <a href="login.php" class="font-weight-bold">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
