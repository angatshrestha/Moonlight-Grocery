<?php
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Fetch stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;

$recentOrders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="font-weight-bold">Admin Dashboard</h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Total Revenue</h6>
                        <h2 class="mb-0 font-weight-bold">$<?php echo number_format($totalRevenue, 2); ?></h2>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Orders</h6>
                        <h2 class="mb-0 font-weight-bold"><?php echo $totalOrders; ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Products</h6>
                        <h2 class="mb-0 font-weight-bold"><?php echo $totalProducts; ?></h2>
                    </div>
                    <i class="fas fa-box-open fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Customers</h6>
                        <h2 class="mb-0 font-weight-bold"><?php echo $totalUsers; ?></h2>
                    </div>
                    <i class="fas fa-users fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 font-weight-bold">Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Order ID</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order->id; ?></td>
                                <td><?php echo htmlspecialchars($order->customer_name); ?></td>
                                <td>$<?php echo number_format($order->total_amount, 2); ?></td>
                                <td>
                                    <?php 
                                        $badge = 'secondary';
                                        if($order->status == 'confirmed') $badge = 'primary';
                                        if($order->status == 'delivered') $badge = 'success';
                                    ?>
                                    <span class="badge badge-<?php echo $badge; ?>"><?php echo ucfirst($order->status); ?></span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order->created_at)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 font-weight-bold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="products.php" class="btn btn-outline-primary btn-block text-left mb-3">
                    <i class="fas fa-plus-circle mr-2"></i> Manage Products
                </a>
                <a href="orders.php" class="btn btn-outline-success btn-block text-left mb-3">
                    <i class="fas fa-clipboard-list mr-2"></i> Manage Orders
                </a>
                <a href="../index.php" class="btn btn-outline-secondary btn-block text-left">
                    <i class="fas fa-store mr-2"></i> View Storefront
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
