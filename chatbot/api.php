<?php
header('Content-Type: application/json');

// Include global database configuration to query active categories
require_once __DIR__ . '/../config.php';

// Read incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($data['message'] ?? ''));

if (!$message) {
    echo json_encode(['reply' => 'Hello! How can I assist you today?']);
    exit;
}

$reply = "";

// 1. Dynamic database query for grocery categories
$categories_list = [];
try {
    $stmt = $pdo->query("SELECT name FROM categories ORDER BY name ASC");
    $categories_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Fallback if DB is disconnected
    $categories_list = ['Fruits & Vegetables', 'Dairy', 'Bakery', 'Beverages', 'Nepali Achar', 'Newari Food Items'];
}

// Formatted bulleted list of active categories
$categories_str = "";
foreach ($categories_list as $cat) {
    $categories_str .= "• " . htmlspecialchars($cat) . "\n";
}

// 2. Keyword matching with professional support logic
if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'hey') !== false) {
    $reply = "Hello! Welcome to **Moonlight Grocery Support**. How can I help you with your shopping experience today?";
    
} elseif (strpos($message, 'track') !== false || strpos($message, 'status') !== false || strpos($message, 'where') !== false) {
    $reply = "To **track your orders** in real time, please follow these steps:\n\n" .
             "1. Ensure you are **logged in** to your account.\n" .
             "2. Click the **'My Orders'** option in the top navigation bar (or visit [My Orders](my_orders.php)).\n" .
             "3. You will see a list of all your active and past orders, including real-time shipping states (Pending, Confirmed, Dispatched, Delivered).";
             
} elseif (strpos($message, 'make') !== false || strpos($message, 'order') !== false || strpos($message, 'buy') !== false || strpos($message, 'shop') !== false || strpos($message, 'purchase') !== false) {
    $reply = "Ordering your fresh essentials is quick and simple:\n\n" .
             "1. Head to our **[Shop Catalog](products.php)** and browse our fresh stock.\n" .
             "2. Click **'Add to Cart'** on the items you would like to purchase.\n" .
             "3. Open your **[Shopping Cart](cart.php)** in the top menu to verify quantities.\n" .
             "4. Click **'Proceed to Checkout'**, enter your shipping details, and complete your order. We'll dispatch your package immediately!";
             
} elseif (strpos($message, 'password') !== false || strpos($message, 'reset') !== false || strpos($message, 'forgot') !== false || strpos($message, 'change') !== false) {
    $reply = "To **change or reset your password**:\n\n" .
             "1. If you are locked out or forgot your password, go to the **Login** screen and click **'Forgot password?'** (or go directly to the **[Reset Password](reset_password.php)** page).\n" .
             "2. Input your registered email address, and click the reset button.\n" .
             "3. Check your inbox for the secure reset link to instantly update your login credentials.";
             
} elseif (strpos($message, 'category') !== false || strpos($message, 'categories') !== false || strpos($message, 'sell') !== false || strpos($message, 'item') !== false || strpos($message, 'product') !== false) {
    $reply = "We stock a wide variety of fresh daily necessities! Here are the active **grocery categories** we currently offer:\n\n" . 
             $categories_str . "\n" .
             "You can browse all these fresh products directly in our **[Product Catalog](products.php)**.";
             
} elseif (strpos($message, 'delivery') !== false || strpos($message, 'shipping') !== false) {
    $reply = "We offer fast and reliable home delivery! Purchases typically dispatch and arrive within **1-2 hours** inside the city limits. Shipping parameters can be configured during checkout.";
    
} elseif (strpos($message, 'refund') !== false || strpos($message, 'return') !== false) {
    $reply = "Your satisfaction is our top priority! If you are not fully satisfied with a product, you can request a refund or exchange within **24 hours of delivery**. Please contact our hotline or mail support with your Order ID.";
    
} else {
    // Professional fallbacks
    $reply = "I can certainly assist you with that! Could you please provide a few more details? \n\n" .
             "You can ask me questions like:\n" .
             "• *How do I track my order?*\n" .
             "• *How do I make an order?*\n" .
             "• *What categories do you have?*\n" .
             "• *How do I reset my password?*";
}

// Subtle realistic AI typing simulation
usleep(500000); // 0.5 seconds

echo json_encode(['reply' => $reply]);
?>
