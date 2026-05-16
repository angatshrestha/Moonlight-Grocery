<?php
require_once __DIR__ . '/includes/header.php';

$order_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track_order'])) {
    $order_id = $_POST['order_id'];
    header("Location: order_tracking.php?id=$order_id");
    exit;
}

$order = null;
$driverName = "Your Driver";
$eta = "Calculating...";

if ($order_id) {
    // Admins can see any order, users can only see theirs, drivers can see theirs
    $query = "SELECT o.*, u.name as driver_name FROM orders o LEFT JOIN users u ON o.driver_id = u.id WHERE o.id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order && !isAdmin() && $_SESSION['user_id'] != $order->user_id && $_SESSION['user_id'] != $order->driver_id) {
        $order = null; // Access denied
    }
    
    if ($order) {
        if (!empty($order->driver_name)) {
            $driverName = htmlspecialchars($order->driver_name);
        }
        
        if ($order->status == 'out_for_delivery') {
            $minutes = 0;
            // Try to calculate Real ETA using OSRM Public API
            if (!empty($order->driver_location) && strpos($order->driver_location, ',') !== false) {
                list($dLat, $dLon) = explode(',', $order->driver_location);
                $dLat = trim($dLat);
                $dLon = trim($dLon);
                
                // 1. Geocode Delivery Address
                $context = stream_context_create([
                    "http" => [
                        "header" => "User-Agent: MoonlightGrocery/1.0\r\n",
                        "timeout" => 3 // fast timeout so page doesn't hang
                    ]
                ]);
                $nomUrl = "https://nominatim.openstreetmap.org/search?q=" . urlencode($order->delivery_address) . "&format=json&limit=1";
                $nomRes = @file_get_contents($nomUrl, false, $context);
                
                if ($nomRes) {
                    $nomData = json_decode($nomRes, true);
                    if (!empty($nomData)) {
                        $cLat = $nomData[0]['lat'];
                        $cLon = $nomData[0]['lon'];
                        
                        // 2. Get Driving Duration from OSRM
                        $osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$dLon},{$dLat};{$cLon},{$cLat}?overview=false";
                        $osrmRes = @file_get_contents($osrmUrl, false, $context);
                        if ($osrmRes) {
                            $osrmData = json_decode($osrmRes, true);
                            if (!empty($osrmData['routes'])) {
                                $seconds = $osrmData['routes'][0]['duration'];
                                $minutes = ceil($seconds / 60);
                            }
                        }
                    }
                }
            }
            
            if ($minutes > 0) {
                // Real ETA based on driving distance
                $eta = date('h:i A', strtotime("+$minutes minutes"));
            } else {
                // Fallback to simulated ETA
                srand($order->id); 
                $minutes = rand(5, 30);
                $eta = date('h:i A', strtotime("+$minutes minutes"));
            }
        } else if ($order->status == 'delivered') {
            $eta = "Delivered";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <h2 class="font-weight-bold mb-4 text-center">Track Your Order</h2>
                
                <?php if (!$order_id): ?>
                    <form method="POST" class="text-center w-75 mx-auto">
                        <p class="text-muted mb-4">Enter your Order ID to track its real-time delivery status.</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="number" name="order_id" class="form-control border-left-0 form-control-lg" placeholder="Order ID (e.g. 1042)" required>
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4" name="track_order" type="submit">Track</button>
                            </div>
                        </div>
                    </form>
                <?php elseif (!$order): ?>
                    <div class="alert alert-danger text-center">Order not found or you don't have permission to view it.</div>
                    <div class="text-center mt-4">
                        <a href="order_tracking.php" class="btn btn-outline-primary">Try Another Order</a>
                    </div>
                <?php else: ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4 class="mb-0 font-weight-bold">Order #<?php echo $order->id; ?></h4>
                        <a href="invoice.php?id=<?php echo $order->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-invoice"></i> View Invoice</a>
                    </div>
                    
                    <!-- Tracking Progress Bar -->
                    <div class="tracking-wrap my-5">
                        <?php 
                            $step = 1;
                            if($order->status == 'confirmed') $step = 2;
                            if($order->status == 'out_for_delivery') $step = 3;
                            if($order->status == 'delivered') $step = 4;
                            
                            $progress = ($step - 1) * 33.33;
                        ?>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress; ?>%;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3 text-center position-relative" style="top: -25px;">
                            <div style="width: 25%;">
                                <div class="bg-<?php echo $step >= 1 ? 'success' : 'light text-muted'; ?> text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border: 3px solid #fff;">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <p class="font-weight-bold mt-2 mb-0" style="font-size: 0.85rem;">Order Placed</p>
                            </div>
                            <div style="width: 25%;">
                                <div class="bg-<?php echo $step >= 2 ? 'success' : 'light text-muted'; ?> text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border: 3px solid #fff;">
                                    <i class="fas fa-box"></i>
                                </div>
                                <p class="font-weight-bold mt-2 mb-0" style="font-size: 0.85rem;">Confirmed</p>
                            </div>
                            <div style="width: 25%;">
                                <div class="bg-<?php echo $step >= 3 ? 'success' : 'light text-muted'; ?> text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border: 3px solid #fff;">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <p class="font-weight-bold mt-2 mb-0" style="font-size: 0.85rem;">Out for Delivery</p>
                            </div>
                            <div style="width: 25%;">
                                <div class="bg-<?php echo $step >= 4 ? 'success' : 'light text-muted'; ?> text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border: 3px solid #fff;">
                                    <i class="fas fa-home"></i>
                                </div>
                                <p class="font-weight-bold mt-2 mb-0" style="font-size: 0.85rem;">Delivered</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Live Status Details -->
                    <div class="row mt-5">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h6 class="text-uppercase text-muted font-weight-bold mb-1" style="font-size: 0.8rem;">Estimated Arrival</h6>
                                    <h3 class="font-weight-bold mb-0 text-dark"><?php echo $eta; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-id-badge fa-2x text-info mb-2"></i>
                                    <h6 class="text-uppercase text-muted font-weight-bold mb-1" style="font-size: 0.8rem;">Assigned Driver</h6>
                                    <h4 class="font-weight-bold mb-0 text-dark"><?php echo $order->driver_id ? $driverName : 'Assigning...'; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($order->status == 'out_for_delivery'): ?>
                    <!-- Live Map Embed -->
                    <div class="mt-4 border rounded overflow-hidden position-relative bg-light" style="height: 350px;">
                        <!-- Overlay Indicator -->
                        <div class="position-absolute p-3 bg-white shadow-sm rounded m-3 d-flex align-items-center" style="z-index: 10; top: 0; right: 0; pointer-events: none;">
                            <div class="spinner-grow text-success spinner-grow-sm mr-2" role="status"></div>
                            <span class="font-weight-bold text-dark mb-0">Driver En Route</span>
                        </div>
                        
                        <!-- Interactive Google Maps Embed with Route from Driver to Customer -->
                        <?php 
                            // If we captured the driver's GPS coordinates, use them! Otherwise fall back to the store.
                            $startLocation = !empty($order->driver_location) ? $order->driver_location : "Sydney+CBD,+NSW,+Australia";
                        ?>
                        <iframe 
                            src="https://maps.google.com/maps?saddr=<?php echo urlencode($startLocation); ?>&daddr=<?php echo urlencode($order->delivery_address); ?>&output=embed" 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0;" 
                            allowfullscreen="" 
                            aria-hidden="false" 
                            tabindex="0">
                        </iframe>
                    </div>
                    <?php endif; ?>

                    <div class="text-center mt-5">
                        <button onclick="window.location.reload()" class="btn btn-primary px-4 shadow-sm font-weight-bold rounded-pill"><i class="fas fa-sync-alt mr-2"></i> Refresh Status</button>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
