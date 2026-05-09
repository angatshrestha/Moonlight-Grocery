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
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $name, $description, $price, $stock, $image_url]);
    $success = "Product added successfully.";
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->query("DELETE FROM products WHERE id = $id");
    $success = "Product deleted successfully.";
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
                        <td class="text-right">$<?php echo number_format($p->price, 2); ?></td>
                        <td class="text-center">
                            <span class="badge badge-<?php echo $p->stock > 10 ? 'success' : 'danger'; ?>">
                                <?php echo $p->stock; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="?delete=<?php echo $p->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
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
                        <div class="col-md-6 form-group">
                            <label>Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
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
