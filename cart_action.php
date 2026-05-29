<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($product_id) {
        $stmt = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $prod = $stmt->fetch();
        $stock = $prod ? (int)$prod->stock : 0;
        $prodName = $prod ? $prod->name : 'Product';
    }

    if ($action === 'add' && $product_id) {
        $currentQty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
        $newQty = $currentQty + 1;
        if ($newQty > $stock) {
            $_SESSION['cart'][$product_id] = $stock;
            $_SESSION['cart_warning'] = "Capped " . htmlspecialchars($prodName) . " at maximum available stock ($stock items left).";
        } else {
            $_SESSION['cart'][$product_id] = $newQty;
        }
    } elseif ($action === 'remove' && $product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($action === 'update' && $product_id) {
        $qty = (int)$_POST['quantity'];
        if ($qty > $stock) {
            $_SESSION['cart'][$product_id] = $stock;
            $_SESSION['cart_warning'] = "Capped " . htmlspecialchars($prodName) . " at maximum available stock ($stock items left).";
        } elseif ($qty > 0) {
            $_SESSION['cart'][$product_id] = $qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        exit;
    }
}

// Redirect back to where they came from
$referer = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header("Location: $referer");
exit;
?>
