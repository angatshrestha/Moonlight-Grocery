<?php
require_once __DIR__ . '/includes/header.php';

// Fetch categories for sidebar
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle filtering
$whereClause = "1=1";
$params = [];

if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $whereClause .= " AND p.category_id = :category_id";
    $params[':category_id'] = $_GET['category'];
}

if (isset($_GET['offer']) && $_GET['offer'] == 1) {
    $whereClause .= " AND p.is_offer = 1";
}

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $whereClause .= " AND p.name LIKE :search";
    $params[':search'] = '%' . trim($_GET['search']) . '%';
}

// Fetch products based on filter
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereClause ORDER BY p.name ASC");
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <h4 class="font-weight-bold mb-3">Categories</h4>
        <div class="list-group shadow-sm">
            <a href="products.php" class="list-group-item list-group-item-action <?php echo (!isset($_GET['category']) && !isset($_GET['offer'])) ? 'active font-weight-bold' : ''; ?>">All Products</a>
            <a href="products.php?offer=1" class="list-group-item list-group-item-action <?php echo (isset($_GET['offer']) && $_GET['offer'] == 1) ? 'active font-weight-bold text-white' : 'text-danger font-weight-bold'; ?>"><i class="fas fa-tag mr-2"></i>Specials & Offers</a>
            
            <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="list-group-item list-group-item-action <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active font-weight-bold' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Product Grid -->
    <div class="col-lg-9">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom pb-3">
            <h1 class="h3 font-weight-bold mb-3 mb-md-0">
                <?php 
                    if (isset($_GET['search']) && trim($_GET['search']) !== '') echo "Search: " . htmlspecialchars($_GET['search']);
                    elseif (isset($_GET['offer']) && $_GET['offer'] == 1) echo "Specials & Offers";
                    elseif (isset($_GET['category'])) {
                        $key = array_search($_GET['category'], array_column($categories, 'id'));
                        echo $key !== false ? htmlspecialchars($categories[$key]['name']) : "Our Products";
                    } else {
                        echo "All Products";
                    }
                ?>
            </h1>
            <div class="d-flex align-items-center">
                <form action="products.php" method="GET" class="form-inline mr-3">
                    <?php if(isset($_GET['category'])): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                    <?php endif; ?>
                    <?php if(isset($_GET['offer'])): ?>
                        <input type="hidden" name="offer" value="<?php echo htmlspecialchars($_GET['offer']); ?>">
                    <?php endif; ?>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <span class="text-muted d-none d-sm-block"><?php echo count($products); ?> items</span>
            </div>
        </div>

        <?php if(empty($products)): ?>
            <div class="alert alert-info">No products found in this category. We are adding more products soon!</div>
        <?php else: ?>
            <div class="row">
                <?php foreach($products as $product): ?>
                <div class="col-6 col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative">
                            <?php if(isset($product->is_offer) && $product->is_offer == 1): ?>
                                <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;">Offer</span>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($product->image_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name); ?>">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <small class="text-primary font-weight-bold mb-1"><?php echo htmlspecialchars($product->category_name); ?></small>
                            <h6 class="card-title font-weight-bold mb-2"><?php echo htmlspecialchars($product->name); ?></h6>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <?php if(isset($product->old_price) && $product->old_price > $product->price): ?>
                                    <div class="d-flex align-items-center">
                                        <del class="text-danger mr-2 small font-weight-bold">$<?php echo number_format($product->old_price, 2); ?></del>
                                        <span class="font-weight-bold text-success h5 mb-0">$<?php echo number_format($product->price, 2); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="font-weight-bold text-success h5 mb-0">$<?php echo number_format($product->price, 2); ?></span>
                                <?php endif; ?>
                                <form action="cart_action.php" method="POST" class="ajax-add-to-cart">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                                    <button type="submit" class="btn btn-sm text-dark font-weight-bold" style="background-color: var(--secondary-color);"><i class="fas fa-plus"></i> Add</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
