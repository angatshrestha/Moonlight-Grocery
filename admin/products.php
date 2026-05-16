<?php
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : null;
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    
    // If old_price is set, we can consider it an offer. Let's set is_offer = 1 if old_price is provided.
    $is_offer = $old_price ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, old_price, is_offer, stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $name, $description, $price, $old_price, $is_offer, $stock, $image_url]);
    $success = "Product added successfully.";
}

// Handle Make Offer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'make_offer') {
    $id = $_POST['product_id'];
    $old_price = $_POST['old_price'];
    $pdo->prepare("UPDATE products SET is_offer = 1, old_price = ? WHERE id = ?")->execute([$old_price, $id]);
    $success = "Product is now an offer!";
}

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    
    $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$category_id, $name, $description, $price, $stock, $image_url, $id]);
    $success = "Product updated successfully.";
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->query("DELETE FROM products WHERE id = $id");
    $success = "Product deleted successfully.";
}

// Handle Remove Offer
if (isset($_GET['remove_offer'])) {
    $id = $_GET['remove_offer'];
    $pdo->query("UPDATE products SET is_offer = 0, old_price = NULL WHERE id = $id");
    $success = "Offer removed successfully.";
}

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-weight-bold mb-0">Manage Products</h2>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
        <i class="fas fa-plus mr-1"></i> Add Product
    </button>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">ID</th>
                        <th class="border-0">Image</th>
                        <th class="border-0">Name</th>
                        <th class="border-0">Category</th>
                        <th class="border-0 text-right">Price</th>
                        <th class="border-0 text-center">Stock</th>
                        <th class="border-0 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><?php echo $p->id; ?></td>
                        <td><img src="<?php echo htmlspecialchars($p->image_url); ?>" alt="" style="width:40px;height:40px;object-fit:cover;" class="rounded"></td>
                        <td class="font-weight-bold"><?php echo htmlspecialchars($p->name); ?></td>
                        <td><?php echo htmlspecialchars($p->category_name); ?></td>
                        <td class="text-right">
                            $<?php echo number_format($p->price, 2); ?>
                            <?php if ($p->is_offer && $p->old_price): ?>
                                <br><small class="text-danger"><s>$<?php echo number_format($p->old_price, 2); ?></s></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-<?php echo $p->stock > 10 ? 'success' : 'danger'; ?>">
                                <?php echo $p->stock; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" title="Edit Product" data-toggle="modal" data-target="#editProductModal<?php echo $p->id; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($p->is_offer): ?>
                                <a href="?remove_offer=<?php echo $p->id; ?>" class="btn btn-sm btn-outline-warning" title="Remove Offer">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-success" title="Make Offer" data-toggle="modal" data-target="#makeOfferModal<?php echo $p->id; ?>">
                                    <i class="fas fa-tag"></i>
                                </button>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $p->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete Product">
                                <i class="fas fa-trash"></i>
                            </a>
                            
                            <!-- Make Offer Modal -->
                            <div class="modal fade text-left" id="makeOfferModal<?php echo $p->id; ?>" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h6 class="modal-title font-weight-bold">Make Offer</h6>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="make_offer">
                                                <input type="hidden" name="product_id" value="<?php echo $p->id; ?>">
                                                <p class="small text-muted mb-3">Set an old price higher than the current price ($<?php echo number_format($p->price, 2); ?>).</p>
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold">Old Price ($)</label>
                                                    <input type="number" step="0.01" name="old_price" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer p-2">
                                                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success btn-sm">Save Offer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Product Modal -->
                            <div class="modal fade text-left" id="editProductModal<?php echo $p->id; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title font-weight-bold">Edit Product</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="edit">
                                                <input type="hidden" name="product_id" value="<?php echo $p->id; ?>">
                                                
                                                <div class="form-group">
                                                    <label>Product Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($p->name); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control" required>
                                                        <?php foreach($categories as $c): ?>
                                                            <option value="<?php echo $c->id; ?>" <?php echo $c->id == $p->category_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Price ($)</label>
                                                        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $p->price; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Stock Quantity</label>
                                                        <input type="number" name="stock" class="form-control" value="<?php echo $p->stock; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Image URL</label>
                                                    <input type="url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($p->image_url); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($p->description); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach($categories as $c): ?>
                                <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Old Price ($)</label>
                            <input type="number" step="0.01" name="old_price" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Initial Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
