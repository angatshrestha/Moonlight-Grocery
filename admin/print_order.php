<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    die("Access denied.");
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("Invalid Order ID");
}

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
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
    <title>Print Order #<?php echo $order->id; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #525659; padding: 40px 0; font-family: monospace; }
        .page { background: #fff; padding: 40px; width: 21cm; min-height: 29.7cm; margin: 0 auto 20px auto; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        .section-title { border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 20px; font-weight: bold; text-transform: uppercase; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { margin: 0; box-shadow: none; width: 100%; min-height: auto; page-break-after: always; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow"><i class="fas fa-print"></i> Print Documents</button>
        <a href="orders.php" class="btn btn-light btn-lg ml-2 shadow">Back to Orders</a>
    </div>

    <!-- COPY 1: STORE TEAM (PICK & PACK) -->
    <div class="page">
        <div class="text-center mb-4">
            <h2 class="font-weight-bold">STORE TEAM COPY</h2>
            <h4 class="text-muted">PICK & PACK LIST</h4>
        </div>
        
        <div class="row mb-4">
            <div class="col-6">
                <p class="mb-1"><strong>Order ID:</strong> #<?php echo $order->id; ?></p>
                <p class="mb-1"><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order->created_at)); ?></p>
            </div>
            <div class="col-6 text-right">
                <p class="mb-1"><strong>Customer:</strong> <?php echo htmlspecialchars($order->customer_name); ?></p>
            </div>
        </div>

        <h5 class="section-title">Items to Pack</h5>
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width: 10%" class="text-center">Done</th>
                    <th style="width: 15%" class="text-center">Qty</th>
                    <th>Product Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td class="text-center"><div style="width: 20px; height: 20px; border: 1px solid #000; margin: 0 auto;"></div></td>
                    <td class="text-center font-weight-bold" style="font-size: 1.2rem;"><?php echo $item->quantity; ?></td>
                    <td style="font-size: 1.2rem;"><?php echo htmlspecialchars($item->name); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="mt-5 border p-3">
            <p class="mb-0"><strong>Packed By (Signature):</strong> ___________________________</p>
        </div>
    </div>

    <!-- COPY 2: DELIVERY DRIVER -->
    <div class="page">
        <div class="text-center mb-4">
            <h2 class="font-weight-bold">DELIVERY DRIVER COPY</h2>
            <h4 class="text-muted">DELIVERY MANIFEST</h4>
        </div>
        
        <div class="row mb-5 border p-3 bg-light">
            <div class="col-6">
                <h3 class="font-weight-bold">ORDER #<?php echo $order->id; ?></h3>
                <h5 class="text-danger font-weight-bold">TOTAL TO COLLECT: PAID</h5>
            </div>
            <div class="col-6 text-right">
                <h5><?php echo date('M j, Y', strtotime($order->created_at)); ?></h5>
            </div>
        </div>

        <h5 class="section-title">Customer Details</h5>
        <div class="mb-5" style="font-size: 1.2rem;">
            <p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($order->customer_name); ?></p>
            <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($order->phone ?? 'Not Provided'); ?></p>
            <p class="mb-2"><strong>Address:</strong><br>
                <div class="p-3 border mt-2 bg-light">
                    <?php echo nl2br(htmlspecialchars($order->delivery_address)); ?>
                </div>
            </p>
        </div>

        <h5 class="section-title">Order Summary (For Verification)</h5>
        <table class="table table-sm">
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td style="width: 10%"><strong><?php echo $item->quantity; ?>x</strong></td>
                    <td><?php echo htmlspecialchars($item->name); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="mt-5 border p-3">
            <p class="mb-3"><strong>Received By Customer (Signature):</strong> ___________________________</p>
            <p class="mb-0"><strong>Time Delivered:</strong> _______:_______</p>
        </div>
    </div>

</body>
</html>
