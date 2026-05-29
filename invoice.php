<?php
// invoice.php
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
    <title>Tax Invoice #<?php echo $order->id; ?> - Moonlight Grocery</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2d1457;
            --secondary-color: #ffc107;
            --text-color: #0f172a;
            --slate-500: #64748b;
            --slate-100: #f1f5f9;
            --border-color: #e2e8f0;
        }
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; 
            color: var(--text-color);
            padding: 40px 0; 
        }
        .invoice-card { 
            background: #ffffff; 
            padding: 50px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); 
            max-width: 850px; 
            margin: 0 auto; 
            border: 1px solid var(--border-color);
        }
        .invoice-header { 
            border-bottom: 2px solid var(--slate-100); 
            padding-bottom: 30px; 
            margin-bottom: 35px; 
        }
        .table thead th {
            background-color: var(--slate-100);
            border-bottom: none;
            color: var(--slate-500);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }
        .table td, .table th {
            padding: 16px;
            vertical-align: middle;
            border-color: var(--border-color);
        }
        .invoice-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
        }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-confirmed { background: #dbeafe; color: #2563eb; }
        .badge-delivered { background: #dcfce7; color: #16a34a; }
        .badge-cancelled { background: #fee2e2; color: #dc2626; }
        
        @media print {
            body { 
                background: #ffffff; 
                padding: 0;
                color: #000;
            }
            .invoice-card { 
                box-shadow: none; 
                max-width: 100%; 
                padding: 0; 
                border: none;
            }
            .no-print { 
                display: none !important; 
            }
            .table thead th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print / Action Controls -->
        <div class="mb-4 text-center no-print">
            <button onclick="window.print()" class="btn btn-primary font-weight-bold px-4 py-2 mr-2" style="background-color: var(--primary-color); border: none; border-radius: 8px;">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
            <a href="index.php" class="btn btn-outline-secondary font-weight-bold px-4 py-2" style="border-radius: 8px;">
                <i class="fas fa-store mr-2"></i> Return to Shop
            </a>
        </div>
        
        <!-- Official Tax Invoice Document -->
        <div class="invoice-card">
            <!-- Header Brand Details -->
            <div class="invoice-header d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="font-weight-bold mb-2" style="color: var(--primary-color); letter-spacing: -0.5px;">
                        <i class="fas fa-shopping-basket mr-2" style="color: var(--secondary-color);"></i>Moonlight Grocery
                    </h2>
                    <p class="text-muted mb-1" style="font-size: 0.9rem;">123 Fresh Lane, Sydney NSW 2000</p>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">contact@moonlight.com | (02) 1234 5678</p>
                </div>
                <div class="text-right">
                    <h2 class="text-uppercase font-weight-bold text-muted mb-3" style="font-size: 1.6rem; letter-spacing: 1px;">Tax Invoice</h2>
                    <p class="mb-1" style="font-size: 0.9rem;"><strong>Invoice ID:</strong> #<?php echo $order->id; ?></p>
                    <p class="mb-1" style="font-size: 0.9rem;"><strong>Date:</strong> <?php echo date('M j, Y', strtotime($order->created_at)); ?></p>
                    <p class="mb-0" style="font-size: 0.9rem;"><strong>Status:</strong> 
                        <?php 
                            $status = $order->status;
                            $badge_class = 'badge-' . $status;
                        ?>
                        <span class="invoice-badge <?php echo $badge_class; ?>"><?php echo strtoupper($status); ?></span>
                    </p>
                </div>
            </div>
            
            <!-- Billing / Shipping Context -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Customer Information</h6>
                    <h5 class="font-weight-bold mb-2" style="color: var(--primary-color);"><?php echo htmlspecialchars($order->customer_name); ?></h5>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;"><i class="fas fa-envelope mr-2" style="width: 16px;"></i><?php echo htmlspecialchars($order->email); ?></p>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;"><i class="fas fa-phone mr-2" style="width: 16px;"></i><?php echo htmlspecialchars($order->phone ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Shipping Address</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.95rem; line-height: 1.6;">
                        <i class="fas fa-map-marker-alt mr-2 text-danger"></i><?php echo nl2br(htmlspecialchars($order->delivery_address)); ?>
                    </p>
                </div>
            </div>
            
            <!-- Items Table -->
            <table class="table mb-5">
                <thead>
                    <tr>
                        <th class="border-0">Product Description</th>
                        <th class="border-0 text-center" style="width: 100px;">Qty</th>
                        <th class="border-0 text-right" style="width: 150px;">Unit Price</th>
                        <th class="border-0 text-right" style="width: 150px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                        <tr>
                            <td class="font-weight-bold" style="color: var(--primary-color);"><?php echo htmlspecialchars($item->name); ?></td>
                            <td class="text-center font-weight-bold"><?php echo $item->quantity; ?></td>
                            <td class="text-right text-muted">$<?php echo number_format($item->price, 2); ?></td>
                            <td class="text-right font-weight-bold">$<?php echo number_format($item->quantity * $item->price, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?php 
                        $tax = (float)$order->tax_amount;
                        $total = (float)$order->total_amount;
                        $subtotal = $total - $tax;
                    ?>
                    <tr>
                        <td colspan="2" class="border-0"></td>
                        <td class="text-right text-muted font-weight-bold border-0">Subtotal:</td>
                        <td class="text-right font-weight-bold border-0">$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border-0"></td>
                        <td class="text-right text-muted font-weight-bold border-0">Tax (10% GST/VAT Included):</td>
                        <td class="text-right font-weight-bold border-0">$<?php echo number_format($tax, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border-0"></td>
                        <td class="text-right text-muted font-weight-bold border-0">Delivery Fee:</td>
                        <td class="text-right font-weight-bold border-0">$<?php echo number_format((float)$order->delivery_fee, 2); ?></td>
                    </tr>
                    <tr style="border-top: 2px solid var(--border-color);">
                        <td colspan="2" class="border-0"></td>
                        <td class="text-right font-weight-bold h5 mb-0 border-0" style="color: var(--primary-color);">Total Paid:</td>
                        <td class="text-right font-weight-bold h5 mb-0 border-0" style="color: var(--primary-color); font-size: 1.4rem;">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Corporate Footer Notes -->
            <div class="text-center text-muted mt-5 pt-4 border-top" style="font-size: 0.85rem;">
                <p class="mb-1 font-weight-bold">Payment Method Sourced: <?php echo strtoupper(str_replace('_', ' ', $order->payment_method)); ?></p>
                <?php if(!empty($order->transaction_id)): ?>
                    <p class="mb-3 text-uppercase" style="font-family: monospace; font-size: 0.8rem; letter-spacing: 0.5px;">Txn ID: <?php echo $order->transaction_id; ?></p>
                <?php endif; ?>
                <p class="mb-0 text-muted">Thank you for supporting sustainable local agriculture. Your business is highly valued!</p>
            </div>
        </div>
    </div>
</body>
</html>
