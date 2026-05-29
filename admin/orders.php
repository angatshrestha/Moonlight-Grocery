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
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, driver_id = ? WHERE id = ?");
    $stmt->execute([$status, $driver_id, $order_id]);
    $success = "Order #$order_id updated successfully.";
}

// Fetch drivers
$drivers = $pdo->query("SELECT id, name FROM users WHERE role = 'driver'")->fetchAll();

$orders = $pdo->query("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

// Fetch order items for the displayed orders
$orderItems = [];
if (!empty($orders)) {
    $orderIds = array_column($orders, 'id');
    $idsStr = implode(',', $orderIds);
    $itemsQuery = $pdo->query("SELECT oi.*, p.name, p.image_url FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN ($idsStr)");
    foreach($itemsQuery->fetchAll() as $item) {
        $orderItems[$item->order_id][] = $item;
    }
}
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

<div class="card shadow-sm border-0 no-hover">
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
                                <select name="driver_id" class="form-control form-control-sm mr-2" style="width: 120px;" title="Assign Driver">
                                    <option value="">Unassigned</option>
                                    <?php foreach($drivers as $d): ?>
                                        <option value="<?php echo $d->id; ?>" <?php echo $o->driver_id == $d->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($d->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="status" class="form-control form-control-sm mr-2" style="width: 140px;" title="Order Status">
                                    <option value="pending" <?php echo $o->status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $o->status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="out_for_delivery" <?php echo $o->status == 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                    <option value="delivered" <?php echo $o->status == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary mr-2" title="Save Updates"><i class="fas fa-save"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#orderModal<?php echo $o->id; ?>" title="View Items">
                                    <i class="fas fa-box-open"></i>
                                </button>
                                <a href="print_order.php?id=<?php echo $o->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary ml-2" title="Print Order">
                                    <i class="fas fa-print"></i>
                                </a>
                            </form>
                            
                            <!-- Order Items Modal -->
                            <div class="modal fade" id="orderModal<?php echo $o->id; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title font-weight-bold">Order #<?php echo $o->id; ?> Details</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="p-3 border-bottom bg-light">
                                                <strong>Delivery Address:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($o->delivery_address)); ?>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <?php if(isset($orderItems[$o->id])): ?>
                                                    <?php foreach($orderItems[$o->id] as $item): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?php echo htmlspecialchars($item->image_url ?? 'https://via.placeholder.com/50'); ?>" alt="" style="width: 40px; height: 40px; object-fit: cover;" class="rounded mr-3">
                                                                <div>
                                                                    <h6 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($item->name ?? 'Unknown Product'); ?></h6>
                                                                    <small class="text-muted">Qty: <?php echo $item->quantity; ?> &times; $<?php echo number_format($item->price, 2); ?></small>
                                                                </div>
                                                            </div>
                                                            <span class="font-weight-bold">$<?php echo number_format($item->quantity * $item->price, 2); ?></span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item text-muted">No items found for this order.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                        <div class="modal-footer bg-light d-flex justify-content-between">
                                            <h5 class="font-weight-bold mb-0">Total: $<?php echo number_format($o->total_amount, 2); ?></h5>
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                        </div>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
