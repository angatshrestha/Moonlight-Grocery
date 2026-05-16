</div> <!-- End of main container -->

<!-- Footer -->
<footer class="text-white py-5 mt-auto" style="background-color: #1e0b3b;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);"><i class="fas fa-shopping-basket mr-2"></i>Moonlight Grocery</h5>
                <p class="text-light small">Delivering the freshest organic produce and daily essentials right to your doorstep since 2026. Quality you can trust.</p>
                <div class="social-icons mt-4">
                    <a href="#" class="text-light mr-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-light mr-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light mr-3"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="products.php" class="text-light text-decoration-none hover-yellow">Shop All</a></li>
                    <li class="mb-2"><a href="products.php?offer=1" class="text-light text-decoration-none hover-yellow">Specials & Offers</a></li>
                    <li class="mb-2"><a href="login.php" class="text-light text-decoration-none hover-yellow">Your Account</a></li>
                    <li class="mb-2"><a href="cart.php" class="text-light text-decoration-none hover-yellow">Shopping Cart</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);">Contact Us</h5>
                <ul class="list-unstyled text-light small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt mr-2"></i> 2 pitt street, NSW 2000</li>
                    <li class="mb-2"><i class="fas fa-phone mr-2"></i> +61 2 1234 5678</li>
                    <li class="mb-2"><i class="fas fa-envelope mr-2"></i> support@moonlightgrocery.com</li>
                </ul>
            </div>
        </div>
        <hr class="mt-4 mb-4" style="border-color: rgba(255,255,255,0.1);">
        <div class="text-center text-light small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Moonlight Grocery. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Include Chatbot Widget -->
<?php include_once __DIR__ . '/../chatbot/chatbot_widget.php'; ?>

<!-- Bootstrap 4.0 Scripts (jQuery, Popper.js, Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
