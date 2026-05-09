<?php
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $pdo->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    $success = "Order #$order_id status updated to $status.";
}

$orders = $pdo->query("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-weight-bold mb-0">Manage Orders</h2>
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
                        <th class="border-0">Order ID</th>
                        <th class="border-0">Customer</th>
                        <th class="border-0">Date</th>
                        <th class="border-0">Amount</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td class="font-weight-bold">#<?php echo $o->id; ?></td>
                        <td>
                            <?php echo htmlspecialchars($o->customer_name); ?><br>
                            <small class="text-muted"><?php echo htmlspecialchars($o->email); ?></small>
                        </td>
                        <td><?php echo date('M j, Y g:i A', strtotime($o->created_at)); ?></td>
                        <td class="font-weight-bold text-primary">$<?php echo number_format($o->total_amount, 2); ?></td>
                        <td>
                            <?php 
                                $badge = 'secondary';
                                if($o->status == 'confirmed') $badge = 'primary';
                                if($o->status == 'delivered') $badge = 'success';
                            ?>
                            <span class="badge badge-<?php echo $badge; ?>"><?php echo ucfirst($o->status); ?></span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="order_id" value="<?php echo $o->id; ?>">
                                <select name="status" class="form-control form-control-sm mr-2" style="width: 120px;">
                                    <option value="pending" <?php echo $o->status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $o->status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="delivered" <?php echo $o->status == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
