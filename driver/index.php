<?php
require_once __DIR__ . '/../config.php';

// Only drivers can access this
if (!isLoggedIn() || $_SESSION['user_role'] !== 'driver') {
    header("Location: ../index.php");
    exit;
}

$driver_id = $_SESSION['user_id'];

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $driver_location = !empty($_POST['driver_location']) ? $_POST['driver_location'] : null;
    
    // Ensure the order actually belongs to this driver
    if ($status === 'out_for_delivery' && $driver_location) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, driver_location = ? WHERE id = ? AND driver_id = ?");
        $stmt->execute([$status, $driver_location, $order_id, $driver_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND driver_id = ?");
        $stmt->execute([$status, $order_id, $driver_id]);
    }
    $success = "Order #$order_id marked as " . str_replace('_', ' ', $status) . "!";
}

// Fetch active assigned orders
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.driver_id = ? AND o.status != 'delivered' ORDER BY o.created_at ASC");
$stmt->execute([$driver_id]);
$activeOrders = $stmt->fetchAll();

// Fetch completed deliveries count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE driver_id = ? AND status = 'delivered'");
$stmt->execute([$driver_id]);
$completedCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Moonlight Grocery</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .navbar-driver { background-color: #2d1457; }
        .order-card { border-radius: 12px; border-left: 5px solid #ffc107; }
        .order-card.status-out_for_delivery { border-left-color: #17a2b8; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-driver shadow-sm sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand font-weight-bold mb-0 h1"><i class="fas fa-truck mr-2" style="color: #ffc107;"></i> Driver Portal</span>
        <a href="../logout.php" class="text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="font-weight-bold">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h4>
            <p class="text-muted">You have <?php echo count($activeOrders); ?> active deliveries. (<?php echo $completedCount; ?> completed)</p>
        </div>
    </div>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if(empty($activeOrders)): ?>
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="fas fa-check-double fa-4x text-success mb-3 opacity-50"></i>
                <h5 class="font-weight-bold">All caught up!</h5>
                <p class="text-muted">You have no pending deliveries.</p>
                <button onclick="window.location.reload()" class="btn btn-outline-primary mt-3"><i class="fas fa-sync-alt mr-1"></i> Refresh</button>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($activeOrders as $o): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 order-card status-<?php echo $o->status; ?> h-100">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold mb-0">Order #<?php echo $o->id; ?></h5>
                        <?php if($o->status == 'out_for_delivery'): ?>
                            <span class="badge badge-info py-2 px-3">Out for Delivery</span>
                        <?php else: ?>
                            <span class="badge badge-warning py-2 px-3 text-dark">Assigned</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Customer</h6>
                            <p class="mb-0 font-weight-bold" style="font-size: 1.1rem;"><?php echo htmlspecialchars($o->customer_name); ?></p>
                            <?php if(!empty($o->phone)): ?>
                                <a href="tel:<?php echo htmlspecialchars($o->phone); ?>" class="text-primary"><i class="fas fa-phone-alt mr-1"></i> <?php echo htmlspecialchars($o->phone); ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Delivery Address</h6>
                            <div class="bg-light p-3 rounded border">
                                <?php echo nl2br(htmlspecialchars($o->delivery_address)); ?>
                            </div>
                        </div>
                        
                        <form method="POST" class="d-flex flex-column gap-2">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?php echo $o->id; ?>">
                            <input type="hidden" name="driver_location" class="driver-location-input" value="">
                            
                            <?php if($o->status != 'out_for_delivery'): ?>
                                <button type="submit" name="status" value="out_for_delivery" class="btn btn-info btn-block btn-lg mb-2 shadow-sm font-weight-bold">
                                    <i class="fas fa-truck mr-2"></i> Start Delivery
                                </button>
                            <?php endif; ?>
                            
                            <button type="submit" name="status" value="delivered" class="btn btn-success btn-block btn-lg shadow-sm font-weight-bold" onclick="return confirm('Confirm that you have handed the items to the customer?');">
                                <i class="fas fa-check-circle mr-2"></i> Mark Delivered
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    // Request driver's live GPS location when they open the dashboard
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            let latlng = position.coords.latitude + "," + position.coords.longitude;
            // Inject it into all forms so when they click "Start Delivery", it saves their exact location
            document.querySelectorAll('.driver-location-input').forEach(function(input) {
                input.value = latlng;
            });
            console.log("GPS Location captured: " + latlng);
        }, function(error) {
            console.warn("Geolocation access denied or failed. Will fall back to store location.");
        });
    }
</script>
</body>
</html>
