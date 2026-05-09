<?php
require_once __DIR__ . '/includes/header.php';

// Fetch featured products (limit to 3)
$stmt = $pdo->query("SELECT * FROM products LIMIT 3");
$featuredProducts = $stmt->fetchAll();
?>

<div class="hero-section">
    <div class="container">
        <h1>Welcome to Moonlight Grocery</h1>
        <p class="lead text-muted mb-4">Fresh produce, dairy, and bakery items delivered right to your door.</p>
        <a href="products.php" class="btn btn-primary btn-lg shadow-sm">Shop Now <i class="fas fa-arrow-right ml-2"></i></a>
    </div>
</div>

<div class="mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h2 class="font-weight-bold">Featured Products</h2>
        <a href="products.php" class="text-primary text-decoration-none font-weight-bold">View All <i class="fas fa-chevron-right ml-1"></i></a>
    </div>
    
    <div class="row">
        <?php foreach($featuredProducts as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($product->image_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name); ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($product->name); ?></h5>
                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product->description); ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="h5 mb-0 font-weight-bold text-primary">$<?php echo number_format($product->price, 2); ?></span>
                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 35px; height: 35px; padding: 0;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
