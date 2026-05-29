<?php
// admin/index.php
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Handle Instant Inventory Restock Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restock') {
    $restock_id = (int)$_POST['product_id'];
    $qty = (int)($_POST['restock_qty'] ?? 50);
    
    $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    $stmt->execute([$qty, $restock_id]);
    $restock_success = "Stock replenished successfully! Restocked {$qty} units.";
}

// Fetch general operational metrics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;

// Fetch low stock products (stock <= 5)
$lowStockStmt = $pdo->query("SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stock <= 5 ORDER BY p.stock ASC");
$lowStockProducts = $lowStockStmt->fetchAll();

// Fetch recent 5 orders
$recentOrders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

// Generate dynamic 7-day sales analytics data
$salesTrend = [];
$labels = [];
$maxVal = 100; // Base value to avoid division by zero
for ($i = 6; $i >= 0; $i--) {
    $dateStr = date('Y-m-d', strtotime("-$i days"));
    $dayLabel = date('D', strtotime("-$i days"));
    
    $trendStmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = ?");
    $trendStmt->execute([$dateStr]);
    $sum = (float)($trendStmt->fetchColumn() ?: 0);
    
    $salesTrend[] = $sum;
    $labels[] = $dayLabel;
    if ($sum > $maxVal) {
        $maxVal = $sum;
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="font-weight-bold">Executive Analytics Dashboard</h2>
        <p class="text-muted">Welcome back to operational oversight, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
    </div>
</div>

<!-- Alert Banner for Replenishment Success -->
<?php if(isset($restock_success)): ?>
    <div class="alert alert-success alert-dismissible fade show border border-success mb-4 shadow-sm" role="alert" style="border-radius:12px;">
        <i class="fas fa-check-circle mr-2"></i> <?php echo $restock_success; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<!-- Core KPI Metric Grids -->
<div class="row mb-4">
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm h-100 border-0" style="background: linear-gradient(135deg, #2d1457, #1e0b3b); color: white; border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 font-weight-bold" style="opacity: 0.75; font-size: 0.75rem; letter-spacing: 1px;">Gross Revenue</h6>
                    <i class="fas fa-dollar-sign fa-lg" style="opacity: 0.6;"></i>
                </div>
                <div>
                    <h2 class="mb-1 font-weight-bold" style="font-size: 2rem;">$<?php echo number_format($totalRevenue, 2); ?></h2>
                    <small style="opacity: 0.8; font-size: 0.8rem;"><i class="fas fa-chevron-up mr-1 text-success"></i> 100% Tax Compliant</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm h-100 border-0" style="background: linear-gradient(135deg, #10b981, #047857); color: white; border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 font-weight-bold" style="opacity: 0.75; font-size: 0.75rem; letter-spacing: 1px;">Processed Orders</h6>
                    <i class="fas fa-shopping-cart fa-lg" style="opacity: 0.6;"></i>
                </div>
                <div>
                    <h2 class="mb-1 font-weight-bold" style="font-size: 2rem;"><?php echo $totalOrders; ?></h2>
                    <small style="opacity: 0.8; font-size: 0.8rem;">Average Ticket: $<?php echo $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00'; ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm h-100 border-0" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 font-weight-bold" style="opacity: 0.75; font-size: 0.75rem; letter-spacing: 1px;">Retail Catalog</h6>
                    <i class="fas fa-box-open fa-lg" style="opacity: 0.6;"></i>
                </div>
                <div>
                    <h2 class="mb-1 font-weight-bold" style="font-size: 2rem;"><?php echo $totalProducts; ?></h2>
                    <small style="opacity: 0.8; font-size: 0.8rem;">Active Grocery SKU List</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm h-100 border-0" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 font-weight-bold" style="opacity: 0.75; font-size: 0.75rem; letter-spacing: 1px;">Retail Customers</h6>
                    <i class="fas fa-users fa-lg" style="opacity: 0.6;"></i>
                </div>
                <div>
                    <h2 class="mb-1 font-weight-bold" style="font-size: 2rem;"><?php echo $totalUsers; ?></h2>
                    <small style="opacity: 0.8; font-size: 0.8rem;">Verified Registered Accounts</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 7-Day Interactive Sales Chart Block -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:16px; background: var(--card-bg);">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px; border-bottom: 1px solid var(--border-color);">
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-chart-line mr-2 text-primary"></i>7-Day Revenue Trend</h5>
                <span class="badge badge-light border text-muted">Real-Time Data Feed</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <?php
                // Generate relative coordinate values for Vector SVG Chart drawing
                $points = "";
                $coordArray = [];
                $width = 650;
                $height = 200;
                $padX = 60;
                $padY = 30;
                
                $chartWidth = $width - ($padX * 2);
                $chartHeight = $height - ($padY * 2);
                
                for ($j = 0; $j < 7; $j++) {
                    $x = $padX + ($j * ($chartWidth / 6));
                    $y = ($height - $padY) - (($salesTrend[$j] / $maxVal) * $chartHeight);
                    $coordArray[] = ['x' => $x, 'y' => $y, 'val' => $salesTrend[$j], 'lbl' => $labels[$j]];
                    $points .= "$x,$y ";
                }
                
                // Formulate gradient polygon coordinates (closes the SVG path to bottom line)
                $polygonPoints = "{$padX}," . ($height - $padY) . " " . trim($points) . " " . ($padX + $chartWidth) . "," . ($height - $padY);
                ?>
                <div class="w-100 overflow-auto text-center">
                    <svg viewBox="0 0 <?php echo $width; ?> <?php echo $height; ?>" width="100%" height="<?php echo $height; ?>" class="mb-3">
                        <!-- Chart definitions (Linear Gradient Colors) -->
                        <defs>
                            <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--primary-color)" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="var(--primary-color)" stop-opacity="0.0"/>
                            </linearGradient>
                            <linearGradient id="lineColor" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#4f46e5"/>
                                <stop offset="100%" stop-color="#ffc107"/>
                            </linearGradient>
                        </defs>
                        
                        <!-- Grid Lines -->
                        <line x1="<?php echo $padX; ?>" y1="<?php echo $padY; ?>" x2="<?php echo $padX + $chartWidth; ?>" y2="<?php echo $padY; ?>" stroke="rgba(0,0,0,0.05)" stroke-dasharray="4,4"/>
                        <line x1="<?php echo $padX; ?>" y1="<?php echo $padY + ($chartHeight/2); ?>" x2="<?php echo $padX + $chartWidth; ?>" y2="<?php echo $padY + ($chartHeight/2); ?>" stroke="rgba(0,0,0,0.05)" stroke-dasharray="4,4"/>
                        <line x1="<?php echo $padX; ?>" y1="<?php echo $height - $padY; ?>" x2="<?php echo $padX + $chartWidth; ?>" y2="<?php echo $height - $padY; ?>" stroke="rgba(0,0,0,0.1)"/>
                        
                        <!-- Gradient Fill under Sales Trend Curve -->
                        <polygon points="<?php echo $polygonPoints; ?>" fill="url(#chartGradient)"/>
                        
                        <!-- Vector Line Curve -->
                        <polyline points="<?php echo trim($points); ?>" fill="none" stroke="url(#lineColor)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        
                        <!-- Value Circle Pins & Text Nodes -->
                        <?php foreach($coordArray as $coord): ?>
                            <!-- Circle Pins -->
                            <circle cx="<?php echo $coord['x']; ?>" cy="<?php echo $coord['y']; ?>" r="5" fill="#ffffff" stroke="var(--primary-color)" stroke-width="3"/>
                            
                            <!-- Value Label Popup -->
                            <text x="<?php echo $coord['x']; ?>" y="<?php echo $coord['y'] - 12; ?>" text-anchor="middle" font-size="10" font-weight="bold" fill="var(--primary-color)">
                                $<?php echo round($coord['val']); ?>
                            </text>
                            
                            <!-- Day Labels Axis -->
                            <text x="<?php echo $coord['x']; ?>" y="<?php echo $height - 8; ?>" text-anchor="middle" font-size="10" font-weight="500" fill="var(--slate-500)">
                                <?php echo $coord['lbl']; ?>
                            </text>
                        <?php endforeach; ?>
                    </svg>
                </div>
                <div class="row w-100 text-center border-top pt-3 mt-2">
                    <div class="col-4">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Peak Revenue</small>
                        <span class="font-weight-bold text-dark" style="font-size: 1.1rem;">$<?php echo number_format($maxVal, 2); ?></span>
                    </div>
                    <div class="col-4 border-left border-right">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Awaiting Review</small>
                        <span class="font-weight-bold text-dark" style="font-size: 1.1rem;">
                            <?php echo $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(); ?> Orders
                        </span>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Low Stock Warnings</small>
                        <span class="font-weight-bold text-danger animate-pulse" style="font-size: 1.1rem;">
                            <?php echo count($lowStockProducts); ?> SKUs
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Inventory replenish block -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:16px; background: var(--card-bg);">
            <div class="card-header bg-white py-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px; border-bottom: 1px solid var(--border-color);">
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Low Stock Console</h5>
            </div>
            <div class="card-body p-0 d-flex flex-column justify-content-between" style="max-height: 380px; overflow-y: auto;">
                <?php if (empty($lowStockProducts)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-warehouse fa-3x mb-3 text-muted" style="opacity: 0.4;"></i>
                        <p class="mb-0 font-weight-bold">Inventory levels fully verified.</p>
                        <small>All SKUs have > 5 items in stock.</small>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush mb-0">
                        <?php foreach($lowStockProducts as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3" style="background: var(--card-bg); border-color: var(--border-color);">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($p->image_url); ?>" alt="" class="rounded mr-3" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($p->name); ?></h6>
                                        <small class="text-danger font-weight-bold">Only <?php echo $p->stock; ?> remaining</small>
                                    </div>
                                </div>
                                <div>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="restock">
                                        <input type="hidden" name="product_id" value="<?php echo $p->id; ?>">
                                        <input type="hidden" name="restock_qty" value="50">
                                        <button type="submit" class="btn btn-xs btn-outline-success font-weight-bold px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                            +50 Units
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light p-3 text-center" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <a href="products.php" class="btn btn-outline-primary btn-sm font-weight-bold block"><i class="fas fa-edit mr-1"></i> Manage Full Catalog</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius:16px; background: var(--card-bg);">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px; border-bottom: 1px solid var(--border-color);">
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-clipboard-list mr-2 text-success"></i>Recent Sales</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 6px;">Manage Orders</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 pl-4">Order ID</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Payment</th>
                                <th class="border-0">Invoice Date</th>
                                <th class="border-0 text-right">Revenue</th>
                                <th class="border-0 text-center" style="width: 140px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td class="font-weight-bold pl-4">#<?php echo $order->id; ?></td>
                                <td><?php echo htmlspecialchars($order->customer_name); ?></td>
                                <td>
                                    <span class="text-uppercase" style="font-family:monospace; font-size:0.8rem;"><?php echo str_replace('_', ' ', $order->payment_method); ?></span>
                                    <?php if($order->payment_status === 'paid'): ?>
                                        <i class="fas fa-check-circle text-success ml-1" title="Paid"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($order->created_at)); ?></td>
                                <td class="text-right font-weight-bold text-primary">$<?php echo number_format($order->total_amount, 2); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $badge = 'secondary';
                                        if($order->status == 'confirmed') $badge = 'primary';
                                        if($order->status == 'delivered') $badge = 'success';
                                        if($order->status == 'cancelled') $badge = 'danger';
                                    ?>
                                    <span class="badge badge-<?php echo $badge; ?> py-1 px-3" style="border-radius:999px; font-weight: 600; font-size:0.75rem;">
                                        <?php echo ucfirst($order->status); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
