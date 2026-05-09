<?php
header('Content-Type: application/json');

// Read incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower($data['message'] ?? '');

if (!$message) {
    echo json_encode(['reply' => 'Please say something.']);
    exit;
}

// Simulated AI Logic for Prototype
// In a real application, you would make an API call to Gemini, OpenAI, etc. here.

$reply = "";

// Keyword matching for grocery context
if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
    $reply = "Hello there! How can I assist you with your grocery shopping today?";
} elseif (strpos($message, 'order') !== false || strpos($message, 'status') !== false) {
    $reply = "To check your order status, please navigate to the My Orders page, or let me know your Order ID and I can look it up for you.";
} elseif (strpos($message, 'delivery') !== false) {
    $reply = "We offer free delivery on all orders! Deliveries typically take 1-2 hours within city limits.";
} elseif (strpos($message, 'refund') !== false || strpos($message, 'return') !== false) {
    $reply = "If you're not satisfied with a product, you can return it within 24 hours of delivery. Please contact our support hotline.";
} elseif (strpos($message, 'milk') !== false || strpos($message, 'bread') !== false || strpos($message, 'produce') !== false) {
    $reply = "We have fresh dairy and bakery items restocked daily. You can find them in our Product Catalog.";
} else {
    // Default fallback
    $responses = [
        "I'm sorry, I didn't quite understand that. Could you please rephrase?",
        "As an AI assistant for Moonlight Grocery, I'm still learning. Can you provide more details?",
        "I can help you with orders, delivery information, and product inquiries. What do you need help with?"
    ];
    $reply = $responses[array_rand($responses)];
}

// Simulate network delay for realism
usleep(800000); // 0.8 seconds

echo json_encode(['reply' => $reply]);
?>
