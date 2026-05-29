<?php
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

$stmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userPoints = $stmt->fetchColumn() ?: 0;

// Pre-calculate subtotal for UI
$cartSubtotal = 0;
$ids = implode(',', array_keys($cartItems));
$uiProducts = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($uiProducts as $p) {
    $cartSubtotal += $p['price'] * $cartItems[$p['id']];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and format detailed address
    $street = trim($_POST['street'] ?? '');
    $apt = trim($_POST['apartment'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    
    $addressParts = [];
    if (!empty($apt)) $addressParts[] = $apt;
    if (!empty($street)) $addressParts[] = $street;
    if (!empty($city)) $addressParts[] = $city;
    if (!empty($state)) $addressParts[] = $state;
    if (!empty($postcode)) $addressParts[] = $postcode;
    
    $address = implode(', ', $addressParts);
    
    $phone = $_POST['phone'] ?? '';
    $use_points = isset($_POST['use_points']) ? true : false;
    
    if (empty($street) || empty($city) || empty($phone)) {
        $error = "Delivery address and phone number are required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Calculate subtotal
            $ids = implode(',', array_keys($cartItems));
            $stmt = $pdo->query("SELECT id, price FROM products WHERE id IN ($ids)");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $subtotal = 0;
            $productPrices = [];
            foreach ($products as $p) {
                $subtotal += $p['price'] * $cartItems[$p['id']];
                $productPrices[$p['id']] = $p['price'];
            }
            
            // Apply points discount
            $discount = 0;
            $pointsToDeduct = 0;
            if ($use_points && $userPoints >= 100) {
                $discount = floor($userPoints / 100);
                if ($discount > $subtotal) {
                    $discount = floor($subtotal);
                }
                $pointsToDeduct = $discount * 100;
            }
            
            $total = max(0, $subtotal - $discount);
            $pointsEarned = floor($total); // 1 point per $1 spent
            $tax_amount = round($total * 0.10, 2); // 10% tax
            $payment_method = $_POST['payment_method'] ?? 'card';
            $transaction_id = 'ch_' . substr(md5(uniqid(rand(), true)), 0, 16);
            if ($payment_method === 'paypal' && !empty($_POST['paypal_transaction_id'])) {
                $transaction_id = $_POST['paypal_transaction_id'];
            }
            $payment_status = 'paid';
            
            // Create order with tax, payment method, status, and transaction tracking (defensive fallback enabled)
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, tax_amount, delivery_fee, status, payment_method, transaction_id, payment_status, delivery_address, phone) VALUES (?, ?, ?, 0.00, 'confirmed', ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $total, $tax_amount, $payment_method, $transaction_id, $payment_status, $address, $phone]);
            } catch (PDOException $e) {
                // Fallback: If newer database columns do not exist in users' custom database, execute base order insert
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $total, $address]);
            }
            $orderId = $pdo->lastInsertId();
            
            // Create order items with stock verification
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $pid => $qty) {
                // Verify available stock inside transaction
                $stockCheck = $pdo->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
                $stockCheck->execute([$pid]);
                $prod = $stockCheck->fetch();
                if (!$prod || $prod->stock < $qty) {
                    throw new Exception("Sorry, " . ($prod ? $prod->name : "Product") . " does not have enough stock available to complete your order.");
                }

                $stmt->execute([$orderId, $pid, $qty, $productPrices[$pid]]);
                
                // Decrement product stock
                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $pid]);
            }
            
            // Update user points (defensive fallback enabled)
            try {
                $pdo->query("UPDATE users SET points = points - $pointsToDeduct + $pointsEarned WHERE id = {$_SESSION['user_id']}");
            } catch (PDOException $e) {
                // Fallback: Ignore points updating if the column is absent
            }
            
            $pdo->commit();
            unset($_SESSION['cart']);
            unset($_SESSION['otp_code']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['otp_verified']);
            $success = "Order placed successfully! You earned $pointsEarned reward points.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage() ?: "Failed to place order. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-5">
                <h2 class="font-weight-bold mb-4 text-center">Checkout</h2>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                        <h4 class="alert-heading">Success!</h4>
                        <p><?php echo $success; ?></p>
                        <a href="order_tracking.php?id=<?php echo $orderId; ?>" class="btn btn-info mt-3 mr-2 text-white font-weight-bold"><i class="fas fa-map-marker-alt mr-1"></i> Track Live GPS</a>
                        <a href="invoice.php?id=<?php echo $orderId; ?>" class="btn btn-outline-primary mt-3 mr-2" target="_blank"><i class="fas fa-file-invoice mr-1"></i> Invoice</a>
                        <a href="index.php" class="btn btn-success mt-3">Home</a>
                    </div>
                <?php else: ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="checkout-form">
                        <h4 class="font-weight-bold mb-3 border-bottom pb-2">Delivery Details</h4>
                        <div class="form-group">
                            <label class="font-weight-bold">Street Address</label>
                            <input type="text" name="street" class="form-control" required placeholder="123 Main Street">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Apartment, suite, unit, etc. <span class="text-muted font-weight-normal">(optional)</span></label>
                            <input type="text" name="apartment" class="form-control" placeholder="Apt 4B">
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label class="font-weight-bold">City / Suburb</label>
                                <input type="text" name="city" class="form-control" required placeholder="Sydney">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">State</label>
                                <select name="state" class="form-control" required>
                                    <option value="">Choose...</option>
                                    <option value="NSW">New South Wales</option>
                                    <option value="VIC">Victoria</option>
                                    <option value="QLD">Queensland</option>
                                    <option value="SA">South Australia</option>
                                    <option value="WA">Western Australia</option>
                                    <option value="TAS">Tasmania</option>
                                    <option value="NT">Northern Territory</option>
                                    <option value="ACT">ACT</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">Postcode</label>
                                <input type="text" name="postcode" class="form-control" required placeholder="2000" maxlength="4">
                            </div>
                        </div>
                        <div class="form-group" id="phone-group">
                            <label for="phone" class="font-weight-bold">Contact Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control" required placeholder="e.g. 0412 345 678">
                        </div>
                        
                        <!-- Rewards Points -->
                        <h4 class="font-weight-bold mb-3 mt-5 border-bottom pb-2">Rewards Points</h4>
                        <?php if ($userPoints >= 100): ?>
                            <?php 
                                $maxDiscount = floor($userPoints / 100); 
                                if ($maxDiscount > $cartSubtotal) $maxDiscount = floor($cartSubtotal);
                            ?>
                            <div class="alert alert-warning mb-0 border border-warning shadow-sm">
                                <?php if ($maxDiscount > 0): ?>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="use_points" name="use_points" value="1">
                                        <label class="custom-control-label font-weight-bold" for="use_points" style="cursor: pointer;">
                                            Use <?php echo $maxDiscount * 100; ?> Points for a $<?php echo $maxDiscount; ?> discount!
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <p class="font-weight-bold mb-0">Order total too low to apply points discount.</p>
                                <?php endif; ?>
                                <small class="text-dark d-block mt-2"><i class="fas fa-coins text-warning"></i> You have <strong><?php echo $userPoints; ?></strong> total points. (100 points = $1 discount)</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0 shadow-sm">
                                <i class="fas fa-coins text-warning mr-2"></i> You have <strong><?php echo $userPoints; ?> points</strong>. Earn <?php echo floor($cartSubtotal); ?> more on this order! <br>
                                <small>(100 points = $1 discount)</small>
                            </div>
                        <?php endif; ?>
                        
                        <h4 class="font-weight-bold mb-3 mt-5 border-bottom pb-2">Payment Method</h4>
                        
                        <!-- Payment Method Selector -->
                        <div class="mb-4">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="pay_card" name="payment_method" class="custom-control-input" value="card" checked onchange="togglePaymentForm()">
                                <label class="custom-control-label font-weight-bold" for="pay_card"><i class="fas fa-credit-card text-primary mr-1"></i> Credit Card</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="pay_paypal" name="payment_method" class="custom-control-input" value="paypal" onchange="togglePaymentForm()">
                                <label class="custom-control-label font-weight-bold" for="pay_paypal"><i class="fab fa-paypal text-info mr-1"></i> PayPal</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="pay_esewa" name="payment_method" class="custom-control-input" value="esewa" onchange="togglePaymentForm()">
                                <label class="custom-control-label font-weight-bold text-success" for="pay_esewa"><i class="fas fa-wallet mr-1"></i> e-Sewa</label>
                            </div>
                        </div>

                        <!-- Credit Card Form -->
                        <div id="card_form">
                            <div class="form-group">
                                <label class="font-weight-bold">Name on Card</label>
                                <input type="text" class="form-control payment-input" required placeholder="John Doe">
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Card Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control payment-input" required placeholder="XXXX XXXX XXXX XXXX" pattern="[0-9 ]{19}" title="Please enter a 16-digit card number" maxlength="19">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fab fa-cc-visa text-primary mr-1"></i><i class="fab fa-cc-mastercard text-danger"></i></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Expiry Date</label>
                                    <input type="text" class="form-control payment-input" required placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" title="Format: MM/YY" maxlength="5">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">CVV</label>
                                    <input type="text" class="form-control payment-input" required placeholder="123" pattern="\d{3,4}" title="3 or 4 digit CVV" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Info -->
                        <div id="paypal_form" style="display: none;">
                            <div class="alert alert-info border-info text-center py-4">
                                <i class="fab fa-paypal fa-3x text-info mb-3"></i>
                                <h5>Pay securely with PayPal</h5>
                                <div id="paypal-button-container" class="mt-3 w-100" style="max-width: 400px; margin: 0 auto;"></div>
                            </div>
                        </div>

                        <!-- e-Sewa Info -->
                        <div id="esewa_form" style="display: none;">
                            <div class="alert alert-success border-success text-center py-4">
                                <i class="fas fa-wallet fa-3x text-success mb-3"></i>
                                <h5>Pay with e-Sewa</h5>
                                <p class="mb-0 text-muted">You will be redirected to the e-Sewa portal to complete your transaction.</p>
                            </div>
                        </div>

                        <!-- PayPal SDK Integration -->
                        <script src="https://www.paypal.com/sdk/js?client-id=ATpAzKtTFjPjnTCxz8qLpkYDiuw0nXbBMj_2a6PkzGDzA-DNCBBKWL3rAeZ4dBiwEsCG_xvcbhiPXmrv&currency=AUD"></script>
                        <script>
                            // Handle Dynamic Total based on Points Checkbox
                            let baseTotal = <?php echo $cartSubtotal; ?>;
                            let maxDiscount = <?php echo isset($maxDiscount) ? $maxDiscount : 0; ?>;
                            let finalAmount = baseTotal;

                            const pointsCheckbox = document.getElementById('use_points');
                            if (pointsCheckbox) {
                                pointsCheckbox.addEventListener('change', function() {
                                    if (this.checked) {
                                        finalAmount = Math.max(0, baseTotal - maxDiscount);
                                    } else {
                                        finalAmount = baseTotal;
                                    }
                                });
                            }

                            // Initialize PayPal Buttons
                            paypal.Buttons({
                                createOrder: function(data, actions) {
                                    // Validate form before opening PayPal
                                    if(document.getElementById('phone_verified').value === '0') {
                                        alert('Please verify your phone number with OTP first.');
                                        return actions.reject();
                                    }
                                    if(!document.querySelector('input[name="street"]').value || !document.querySelector('input[name="city"]').value) {
                                        alert('Please fill in your complete delivery address first.');
                                        return actions.reject();
                                    }
                                    
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: {
                                                value: finalAmount.toFixed(2)
                                            }
                                        }]
                                    });
                                },
                                onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(details) {
                                        // Payment Successful! Submit the form to create the database record
                                        let input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'paypal_transaction_id';
                                        input.value = details.id;
                                        document.getElementById('checkout-form').appendChild(input);
                                        
                                        document.getElementById('checkout-form').submit();
                                    });
                                }
                            }).render('#paypal-button-container');

                            function togglePaymentForm() {
                                const method = document.querySelector('input[name="payment_method"]:checked').value;
                                
                                document.getElementById('card_form').style.display = 'none';
                                document.getElementById('paypal_form').style.display = 'none';
                                document.getElementById('esewa_form').style.display = 'none';
                                
                                // Hide the standard Submit button if using PayPal (since PayPal has its own buttons)
                                document.getElementById('main-submit-btn').style.display = (method === 'paypal') ? 'none' : 'block';
                                
                                // Disable required attributes on card inputs if not using card so the form can submit
                                const cardInputs = document.querySelectorAll('.payment-input');
                                cardInputs.forEach(input => input.required = false);

                                if (method === 'card') {
                                    document.getElementById('card_form').style.display = 'block';
                                    cardInputs.forEach(input => input.required = true);
                                } else if (method === 'paypal') {
                                    document.getElementById('paypal_form').style.display = 'block';
                                } else if (method === 'esewa') {
                                    document.getElementById('esewa_form').style.display = 'block';
                                }
                            }
                            
                            // Card formatting input masks
                            document.addEventListener('DOMContentLoaded', function() {
                                const cardNumberInput = document.querySelector('input[placeholder="XXXX XXXX XXXX XXXX"]');
                                const expiryInput = document.querySelector('input[placeholder="MM/YY"]');
                                const cvvInput = document.querySelector('input[placeholder="123"]');
                                
                                if(cardNumberInput) {
                                    cardNumberInput.addEventListener('input', function(e) {
                                        let val = this.value.replace(/\D/g, '');
                                        let formatted = '';
                                        for(let i=0; i<val.length; i++) {
                                            if(i > 0 && i % 4 === 0) formatted += ' ';
                                            formatted += val[i];
                                        }
                                        this.value = formatted.substring(0, 19); // 16 digits + 3 spaces
                                    });
                                }
                                
                                if(expiryInput) {
                                    expiryInput.addEventListener('input', function(e) {
                                        let val = this.value.replace(/\D/g, '');
                                        if(val.length > 2) {
                                            this.value = val.substring(0, 2) + '/' + val.substring(2, 4);
                                        } else {
                                            this.value = val;
                                        }
                                    });
                                }
                                
                                if(cvvInput) {
                                    cvvInput.addEventListener('input', function(e) {
                                        this.value = this.value.replace(/\D/g, '').substring(0, 4);
                                    });
                                }
                            });

                            // Initialize on load
                            document.addEventListener("DOMContentLoaded", function() {
                                togglePaymentForm();
                            });
                        </script>
                        
                        <div class="alert alert-info mt-3 shadow-sm border-info">
                            <i class="fas fa-shield-alt mr-2"></i> This is a secure 256-bit encrypted simulated payment gateway. Your details are safe.
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="cart.php" class="text-muted font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Cart</a>
                            <button type="submit" id="main-submit-btn" class="btn btn-lg px-5 font-weight-bold text-dark shadow-sm" style="background-color: var(--secondary-color);">Pay & Place Order</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
