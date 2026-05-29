</div> <!-- End of main container -->

<!-- Footer -->
<footer class="text-white py-5 mt-auto" style="background-color: #1e0b3b;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);"><i class="fas fa-shopping-basket mr-2"></i>Moonlight Grocery</h5>
                <p class="text-light small"><?php echo __('Delivering the freshest organic produce and daily essentials right to your doorstep since 2026. Quality you can trust.'); ?></p>
                <div class="social-icons mt-4">
                    <a href="#" class="text-light mr-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-light mr-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light mr-3"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);"><?php echo __('Quick Links'); ?></h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>products.php" class="text-light text-decoration-none hover-yellow"><?php echo __('Shop All'); ?></a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>products.php?offer=1" class="text-light text-decoration-none hover-yellow"><?php echo __('Specials & Offers'); ?></a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>login.php" class="text-light text-decoration-none hover-yellow"><?php echo __('Your Account'); ?></a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>cart.php" class="text-light text-decoration-none hover-yellow"><?php echo __('Shopping Cart'); ?></a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="font-weight-bold mb-4" style="color: var(--secondary-color);"><?php echo __('Contact Us'); ?></h5>
                <ul class="list-unstyled text-light small contact-links">
                    <li class="mb-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=2+pitt+street,+NSW+2000" target="_blank" class="text-light text-decoration-none hover-yellow">
                            <i class="fas fa-map-marker-alt mr-2"></i> 2 pitt street, NSW 2000
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="tel:+61212345678" class="text-light text-decoration-none hover-yellow">
                            <i class="fas fa-phone mr-2"></i> +61 2 1234 5678
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="mailto:support@moonlightgrocery.com" class="text-light text-decoration-none hover-yellow">
                            <i class="fas fa-envelope mr-2"></i> support@moonlightgrocery.com
                        </a>
                    </li>
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

<script>
$(document).ready(function() {
    // 1. Initialize Sun/Moon Toggle Icon depending on active theme
    const activeTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = $('#theme-toggle-icon');
    if (themeIcon.length) {
        themeIcon.attr('class', activeTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon');
    }

    // 2. Dark & Night Mode Toggler Click Event Handler
    $('#theme-toggle').on('click', function(e) {
        e.preventDefault();
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Update DOM & LocalStorage
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Update Icon Class with rotation micro-animation
        $(this).css('transform', 'rotate(360deg)');
        setTimeout(() => {
            $(this).css('transform', 'rotate(0deg)');
        }, 300);
        
        themeIcon.attr('class', newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon');
    });

    // 3. AJAX cart additions form submit handler
    $('form[action*="cart_action.php"]').on('submit', function(e) {
        var actionInput = $(this).find('input[name="action"]').val();
        if (actionInput === 'add') {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize() + '&ajax=1';
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.post('<?php echo BASE_URL; ?>cart_action.php', formData, function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cart_count);
                    submitBtn.html('<i class="fas fa-check"></i>').addClass('text-success');
                    setTimeout(function() {
                        submitBtn.html(originalText).prop('disabled', false).removeClass('text-success');
                    }, 1500);
                }
            }, 'json').fail(function() {
                submitBtn.html(originalText).prop('disabled', false);
            });
        }
    });
});
</script>
</body>
</html>
