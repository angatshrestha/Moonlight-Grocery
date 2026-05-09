<?php
require_once __DIR__ . '/includes/header.php';

$cartItems = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

if (!empty($cartItems)) {
    $ids = implode(',', array_keys($cartItems));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
}
?>

<div class="mb-4">
    <h1 class="h3 font-weight-bold">Your Cart</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <p class="text-muted text-center py-5 mb-0">Your cart is currently empty.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="border-0">Product</th>
                                    <th scope="col" class="border-0 text-center">Quantity</th>
                                    <th scope="col" class="border-0 text-right">Price</th>
                                    <th scope="col" class="border-0 text-right">Total</th>
                                    <th scope="col" class="border-0 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $product): ?>
                                    <?php 
                                        $qty = $cartItems[$product->id];
                                        $subtotal = $product->price * $qty;
                                        $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($product->image_url); ?>" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;">
                                                <h6 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($product->name); ?></h6>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="cart_action.php" method="POST" class="d-inline-block">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                                                <input type="number" name="quantity" value="<?php echo $qty; ?>" min="1" class="form-control form-control-sm text-center" style="width: 70px;" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-right">$<?php echo number_format($product->price, 2); ?></td>
                                        <td class="text-right font-weight-bold">$<?php echo number_format($subtotal, 2); ?></td>
                                        <td class="text-center">
                                            <form action="cart_action.php" method="POST">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <a href="products.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left mr-2"></i> Continue Shopping</a>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title font-weight-bold border-bottom pb-3 mb-3">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="font-weight-bold">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                    <span class="text-muted">Delivery</span>
                    <span class="text-success">Free</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="h5 font-weight-bold">Total</span>
                    <span class="h5 font-weight-bold text-primary">$<?php echo number_format($total, 2); ?></span>
                </div>
                <?php if (!empty($products)): ?>
                    <a href="checkout.php" class="btn btn-primary btn-block btn-lg">Proceed to Checkout</a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-block btn-lg" disabled>Proceed to Checkout</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
