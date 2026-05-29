<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("Invalid Order ID");
}

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND (o.user_id = ? OR ?)");
$isAdmin = isAdmin() ? 1 : 0;
$stmt->execute([$order_id, $_SESSION['user_id'], $isAdmin]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found or access denied.");
}

// Fetch items
$itemsStmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemsStmt->execute([$order_id]);
$items = $itemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order->id; ?> - Moonlight Grocery</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding: 40px 0; }
        .invoice-card { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .invoice-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-card { box-shadow: none; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-3 text-center no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
            <a href="index.php" class="btn btn-outline-secondary">Return to Shop</a>
        </div>
        
        <div class="invoice-card">
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold text-primary mb-0">Moonlight Grocery</h2>
                    <p class="text-muted mb-0">123 Fresh Lane, Sydney NSW 2000</p>
                    <p class="text-muted mb-0">contact@moonlight.com | (02) 1234 5678</p>
                </div>
                <div class="text-right">
                    <h1 class="text-uppercase font-weight-bold" style="color: #ccc;">Invoice</h1>
                    <p class="mb-0"><strong>Order #:</strong> <?php echo $order->id; ?></p>
                    <p class="mb-0"><strong>Date:</strong> <?php echo date('M j, Y', strtotime($order->created_at)); ?></p>
                    <p class="mb-0"><strong>Status:</strong> <span class="text-success text-uppercase"><?php echo $order->status; ?></span></p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-muted border-bottom pb-2 mb-3">Bill To:</h5>
                    <h5 class="font-weight-bold mb-1"><?php echo htmlspecialchars($order->customer_name); ?></h5>
                    <p class="mb-0"><?php echo htmlspecialchars($order->email); ?></p>
                    <p class="mb-0"><?php echo htmlspecialchars($order->phone ?? 'No phone provided'); ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-muted border-bottom pb-2 mb-3">Ship To:</h5>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($order->delivery_address)); ?></p>
                </div>
            </div>
            
            <table class="table table-bordered mb-5">
                <thead class="bg-light">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->name); ?></td>
                            <td class="text-center"><?php echo $item->quantity; ?></td>
                            <td class="text-right">$<?php echo number_format($item->price, 2); ?></td>
                            <td class="text-right font-weight-bold">$<?php echo number_format($item->quantity * $item->price, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold">Subtotal:</td>
                        <td class="text-right font-weight-bold">$<?php echo number_format($order->total_amount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold">Delivery Fee:</td>
                        <td class="text-right font-weight-bold">$0.00</td>
                    </tr>
                    <tr class="bg-light">
                        <td colspan="3" class="text-right font-weight-bold h5 mb-0">Total:</td>
                        <td class="text-right font-weight-bold h5 mb-0 text-primary">$<?php echo number_format($order->total_amount, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="text-center text-muted mt-5 pt-3 border-top">
                <p>Thank you for shopping with Moonlight Grocery!</p>
            </div>
        </div>
    </div>
</body>
</html>
