<?php
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    
    if (empty($address)) {
        $error = "Delivery address is required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Calculate total
            $ids = implode(',', array_keys($cartItems));
            $stmt = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids)");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $total = 0;
            $productPrices = [];
            foreach ($products as $p) {
                $total += $p['price'] * $cartItems[$p['id']];
                $productPrices[$p['id']] = $p['price'];
            }
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total, $address]);
            $orderId = $pdo->lastInsertId();
            
            // Create order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $pid => $qty) {
                $stmt->execute([$orderId, $pid, $qty, $productPrices[$pid]]);
                
                // Update stock
                $pdo->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
            }
            
            $pdo->commit();
            unset($_SESSION['cart']);
            $success = "Order placed successfully! Your order number is #$orderId.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to place order. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-5">
                <h2 class="font-weight-bold mb-4 text-center">Checkout</h2>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                        <h4 class="alert-heading">Success!</h4>
                        <p><?php echo $success; ?></p>
                        <a href="index.php" class="btn btn-success mt-3">Return to Home</a>
                    </div>
                <?php else: ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="address" class="font-weight-bold">Delivery Address</label>
                            <textarea name="address" id="address" rows="3" class="form-control" required placeholder="Enter your full delivery address..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i> Payment simulation: In this prototype, payment is considered completed upon placing the order.
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="cart.php" class="text-muted"><i class="fas fa-arrow-left mr-1"></i> Back to Cart</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">Place Order</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
