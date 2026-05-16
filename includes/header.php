<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moonlight Grocery E-commerce</title>
    <!-- Bootstrap 4.0 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--primary-color);">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="index.php" style="color: var(--secondary-color);">
            <i class="fas fa-shopping-basket mr-2"></i>Moonlight Grocery
        </a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-white font-weight-bold px-3" href="products.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white font-weight-bold px-3" href="cart.php">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="badge badge-pill text-dark ml-1" id="cart-count" style="background-color: var(--secondary-color);">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </li>
                <?php if(isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="my_orders.php">My Orders</a>
                    </li>
                    <?php if(isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold px-3" href="admin/index.php" style="color: var(--secondary-color);">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white font-weight-bold px-3" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn font-weight-bold ml-lg-2 px-4 py-2 text-dark shadow-sm" href="register.php" style="background-color: var(--secondary-color); border-radius: 8px;">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5 pb-5">
