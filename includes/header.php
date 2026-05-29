<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] === 'ne') ? 'ne' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moonlight Grocery E-commerce</title>
    <!-- Bootstrap 4.0 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script>
        // Inline script to prevent "flicker of light mode" when page reloads
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--primary-color);">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="<?php echo BASE_URL; ?>index.php" style="color: var(--secondary-color);">
            <i class="fas fa-shopping-basket mr-2"></i>Moonlight Grocery
        </a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Shop Link -->
                <li class="nav-item">
                    <a class="nav-link text-white font-weight-bold px-3" href="<?php echo BASE_URL; ?>products.php"><?php echo __('Shop'); ?></a>
                </li>
                
                <!-- Cart Link -->
                <li class="nav-item">
                    <a class="nav-link text-white font-weight-bold px-3" href="<?php echo BASE_URL; ?>cart.php">
                        <i class="fas fa-shopping-cart mr-1"></i> <?php echo __('Cart'); ?>
                        <span class="badge badge-pill text-dark ml-1" id="cart-count" style="background-color: var(--secondary-color);">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </li>
                
                <?php if(isLoggedIn()): ?>
                    <!-- Orders Link -->
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="<?php echo BASE_URL; ?>my_orders.php"><?php echo __('My Orders'); ?></a>
                    </li>
                    
                    <?php if(isAdmin()): ?>
                        <!-- Admin Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold px-3" href="<?php echo BASE_URL; ?>admin/index.php" style="color: var(--secondary-color);"><?php echo __('Admin Dashboard'); ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Logout Link -->
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="<?php echo BASE_URL; ?>logout.php"><?php echo __('Logout'); ?></a>
                    </li>
                <?php else: ?>
                    <!-- Login Link -->
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="<?php echo BASE_URL; ?>login.php"><?php echo __('Login'); ?></a>
                    </li>
                    
                    <!-- Sign Up Button -->
                    <li class="nav-item">
                        <a class="nav-link btn font-weight-bold ml-lg-2 px-4 py-2 text-dark shadow-sm" href="<?php echo BASE_URL; ?>register.php" style="background-color: var(--secondary-color); border-radius: 8px;"><?php echo __('Sign Up'); ?></a>
                    </li>
                <?php endif; ?>

                <!-- Elegant Spacer/Divider in Navbar -->
                <li class="nav-item d-none d-lg-block px-1">
                    <span class="text-white-50">|</span>
                </li>

                <!-- Dynamic Language Selector Selector -->
                <li class="nav-item dropdown px-2">
                    <a class="nav-link dropdown-toggle text-white font-weight-bold p-0 d-flex align-items-center" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-globe mr-1"></i> <?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] === 'ne') ? 'नेपाली' : 'EN'; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 mt-2" aria-labelledby="langDropdown" style="border-radius: 12px; background: var(--card-bg);">
                        <a class="dropdown-item font-weight-bold <?php echo ($currentLang === 'en') ? 'text-primary' : ''; ?>" href="?lang=en" style="color: var(--text-color);">🇬🇧 English</a>
                        <a class="dropdown-item font-weight-bold <?php echo ($currentLang === 'ne') ? 'text-primary' : ''; ?>" href="?lang=ne" style="color: var(--text-color);">🇳🇵 नेपाली (Nepali)</a>
                    </div>
                </li>

                <!-- Elegant Dark Mode Toggler Selector -->
                <li class="nav-item px-2">
                    <button id="theme-toggle" class="btn btn-link text-white p-0 shadow-none border-0" style="font-size: 1.2rem; transition: transform 0.3s ease; outline: none;">
                        <i class="fas fa-moon" id="theme-toggle-icon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5 pb-5">
