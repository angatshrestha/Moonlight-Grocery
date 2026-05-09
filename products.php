<?php
require_once __DIR__ . '/includes/header.php';

// Fetch all products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$products = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1 class="h3 font-weight-bold mb-0">Our Products</h1>
    <span class="text-muted"><?php echo count($products); ?> items</span>
</div>

<div class="row">
    <?php foreach($products as $product): ?>
    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card h-100">
            <img src="<?php echo htmlspecialchars($product->image_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name); ?>">
            <div class="card-body d-flex flex-column p-3">
                <small class="text-primary font-weight-bold mb-1"><?php echo htmlspecialchars($product->category_name); ?></small>
                <h6 class="card-title font-weight-bold mb-2"><?php echo htmlspecialchars($product->name); ?></h6>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">$<?php echo number_format($product->price, 2); ?></span>
                    <form action="cart_action.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
