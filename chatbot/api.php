<?php
// chatbot/api.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';

// Read incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($data['message'] ?? ''));

if (!$message) {
    echo json_encode(['reply' => 'Please say something.']);
    exit;
}

$reply = "";

// 1. DYNAMIC ORDER TRACKING INQUIRY
if (preg_match('/(?:order\s*#?|track\s*order\s*#?|status\s*of\s*#?)\s*(\d+)/i', $message, $matches)) {
    $order_id = (int)$matches[1];
    
    // Query database for order details
    $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        // Authenticate check: must be admin or the owner of the order
        $isUserLoggedIn = isLoggedIn();
        $isOwner = $isUserLoggedIn && ($_SESSION['user_id'] == $order->user_id);
        $isStoreAdmin = isAdmin();
        
        if ($isOwner || $isStoreAdmin) {
            $status = strtoupper($order->status);
            $date = date('M j, Y g:i A', strtotime($order->created_at));
            $total = number_format($order->total_amount, 2);
            $address = htmlspecialchars($order->delivery_address);
            
            $reply = "📦 <strong>Order #{$order_id} Details:</strong><br>";
            $reply .= "• <strong>Status:</strong> <span class='badge badge-info'>{$status}</span><br>";
            $reply .= "• <strong>Placed On:</strong> {$date}<br>";
            $reply .= "• <strong>Total Paid:</strong> \${$total}<br>";
            $reply .= "• <strong>Delivery Address:</strong> {$address}<br>";
            
            if ($order->status === 'pending') {
                $reply .= "<br><em>Your order is being reviewed by our warehouse team and will be confirmed shortly!</em>";
            } elseif ($order->status === 'confirmed') {
                $reply .= "<br><em>Your order is packed and ready for dispatch! 🚚</em>";
            } elseif ($order->status === 'delivered') {
                $reply .= "<br><em>This order was successfully delivered. Thank you for shopping with Moonlight! 🎉</em>";
            }
        } else {
            $reply = "🔒 <strong>Access Denied:</strong> Order #{$order_id} is associated with another account. Please make sure you are logged in with the correct account to track this order.";
        }
    } else {
        $reply = "🔍 <strong>Order Not Found:</strong> We couldn't find an order matching ID <strong>#{$order_id}</strong>. Please double-check your Order ID and try again.";
    }

// 2. DYNAMIC LIVE INVENTORY & STOCK CHECK
} else {
    // Fetch all products to do a smart keyword match
    $stmt = $pdo->query("SELECT p.name, p.stock, p.price, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id");
    $products = $stmt->fetchAll();
    
    $matchedProduct = null;
    foreach ($products as $p) {
        $nameLower = strtolower($p->name);
        // Match if the product name is in the message, or if a significant part of the name matches
        if (strpos($message, $nameLower) !== false || (strlen($nameLower) > 4 && strpos($nameLower, $message) !== false)) {
            $matchedProduct = $p;
            break;
        }
    }
    
    if ($matchedProduct) {
        $name = htmlspecialchars($matchedProduct->name);
        $stock = (int)$matchedProduct->stock;
        $price = number_format($matchedProduct->price, 2);
        
        if ($stock > 0) {
            $stockText = $stock <= 5 ? "<span class='text-danger font-weight-bold'>Low Stock (only {$stock} left!)</span>" : "<span class='text-success'>In Stock ({$stock} available)</span>";
            $reply = "🍏 <strong>Product Stock Check:</strong><br>";
            $reply .= "Yes, we have <strong>{$name}</strong> in stock!<br>";
            $reply .= "• <strong>Availability:</strong> {$stockText}<br>";
            $reply .= "• <strong>Price:</strong> \${$price} each.<br><br>";
            $reply .= "<a href='products.php' class='btn btn-sm btn-success text-white py-1 px-3 font-weight-bold' style='border-radius:6px;'>Buy Now</a>";
        } else {
            $reply = "❌ <strong>Out of Stock:</strong> Sorry, <strong>{$name}</strong> is currently out of stock. We are restockting this item soon! Please check back tomorrow.";
        }
    }
}

// 3. GENERAL ASSISTANT KNOWLEDGE BASE FALLBACK (if not order tracking or product match)
if (empty($reply)) {
    if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'hey') !== false) {
        $reply = "👋 Hello! Welcome to Moonlight Grocery's AI Assistant. How can I help you today?<br><br><em>Try asking me:</em><br>• \"Is almond milk in stock?\"<br>• \"Track order #1\"";
    } elseif (strpos($message, 'delivery') !== false || strpos($message, 'shipping') !== false) {
        $reply = "🚚 <strong>Delivery Details:</strong> We deliver fresh organic groceries right to your doorstep within <strong>1-2 hours</strong>. Delivery is <strong>100% Free</strong> on all orders!";
    } elseif (strpos($message, 'refund') !== false || strpos($message, 'return') !== false || strpos($message, 'cancel') !== false) {
        $reply = "🔄 <strong>Returns & Refunds:</strong> Customer satisfaction is our absolute priority. If you receive any damaged produce, contact our support team within 24 hours of delivery for an instant refund or replacement.";
    } elseif (strpos($message, 'contact') !== false || strpos($message, 'phone') !== false || strpos($message, 'address') !== false) {
        $reply = "📞 <strong>Contact Details:</strong><br>• <strong>Location:</strong> 123 Fresh Lane, Sydney NSW 2000<br>• <strong>Support Email:</strong> contact@moonlight.com<br>• <strong>Phone Hotline:</strong> (02) 1234 5678";
    } else {
        $reply = "🤖 I'm Moonlight's smart support bot! I have real-time access to our store inventory and order databases.<br><br><strong>Try asking me:</strong><br>• \"Is sourdough bread in stock?\"<br>• \"Track order #2\" (Make sure you are logged in!)";
    }
}

// Simulate brief AI calculation pause
usleep(500000); 

echo json_encode(['reply' => $reply]);
exit;
?>
