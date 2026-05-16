<?php
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-4">
                <h2 class="font-weight-bold mb-4">My Orders</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3 opacity-50"></i>
                        <h5>You haven't placed any orders yet.</h5>
                        <a href="products.php" class="btn btn-primary mt-3">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="font-weight-bold">#<?php echo $order->id; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order->created_at)); ?></td>
                                        <td class="font-weight-bold text-success">$<?php echo number_format($order->total_amount, 2); ?></td>
                                        <td>
                                            <?php 
                                                $badge = 'secondary';
                                                if($order->status == 'confirmed') $badge = 'primary';
                                                if($order->status == 'out_for_delivery') $badge = 'info';
                                                if($order->status == 'delivered') $badge = 'success';
                                            ?>
                                            <span class="badge badge-<?php echo $badge; ?> p-2 text-uppercase"><?php echo str_replace('_', ' ', $order->status); ?></span>
                                        </td>
                                        <td class="text-right">
                                            <a href="order_tracking.php?id=<?php echo $order->id; ?>" class="btn btn-sm btn-info text-white font-weight-bold mr-2"><i class="fas fa-map-marker-alt"></i> Track</a>
                                            <a href="invoice.php?id=<?php echo $order->id; ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-file-invoice"></i> Invoice</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
