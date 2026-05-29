<?php
require_once __DIR__ . '/includes/header.php';

// Fetch featured products (limit to 3)
$stmt = $pdo->query("SELECT * FROM products LIMIT 3");
$featuredProducts = $stmt->fetchAll();

// Fetch daily offers
$offerStmt = $pdo->query("SELECT * FROM products WHERE is_offer = 1 ORDER BY RAND() LIMIT 5");
$dailyOffers = $offerStmt->fetchAll();
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="row">
        <!-- Left Carousel/Banner -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div id="woolworthsCarousel" class="carousel slide shadow-sm rounded overflow-hidden" data-ride="carousel" style="background-color: var(--primary-color);">
                <div class="carousel-inner h-100">
                    <?php if (!empty($dailyOffers)): ?>
                        <?php foreach($dailyOffers as $index => $offer): ?>
                            <div class="carousel-item h-100 <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="woolworths-banner d-flex h-100" style="min-height: 400px;">
                                    <div class="banner-content text-white p-5 d-flex flex-column justify-content-center" style="width: 45%;">
                                        <div class="rounded-circle text-dark d-flex align-items-center justify-content-center mb-4 shadow" style="background-color: var(--secondary-color); width: 90px; height: 90px; font-weight: bold; font-size: 1.1rem; transform: rotate(-5deg);">
                                            Daily<br>Offer
                                        </div>
                                        <h2 class="font-weight-bold mb-3" style="font-size: 2.2rem; line-height: 1.1;"><?php echo htmlspecialchars($offer->name); ?></h2>
                                        <p class="mb-4 d-none d-md-block" style="font-size: 1rem;"><?php echo htmlspecialchars($offer->description); ?></p>
                                        <p class="h3 font-weight-bold mb-4" style="color: var(--secondary-color);">$<?php echo number_format($offer->price, 2); ?></p>
                                        <div>
                                            <form action="cart_action.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $offer->id; ?>">
                                                <button type="submit" class="btn text-dark font-weight-bold px-4 py-2" style="background-color: var(--secondary-color); border: none; border-radius: 8px;"><i class="fas fa-shopping-cart mr-2"></i> Add to Cart</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="banner-image position-relative" style="width: 55%; min-height: 400px;">
                                        <img src="<?php echo htmlspecialchars($offer->image_url); ?>" alt="<?php echo htmlspecialchars($offer->name); ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static banner if no offers exist -->
                        <div class="carousel-item active h-100">
                            <div class="woolworths-banner d-flex h-100" style="min-height: 400px;">
                                <div class="banner-content text-white p-5 d-flex flex-column justify-content-center" style="width: 45%;">
                                    <h2 class="font-weight-bold mb-3" style="font-size: 2.5rem; line-height: 1.1;">Welcome to Moonlight</h2>
                                    <p class="mb-4" style="font-size: 1.1rem;">Discover fresh produce and daily essentials.</p>
                                    <div>
                                        <a href="products.php" class="btn text-dark font-weight-bold px-4 py-2" style="background-color: var(--secondary-color); border: none; border-radius: 8px;">Shop now</a>
                                    </div>
                                </div>
                                <div class="banner-image position-relative" style="width: 55%; min-height: 400px;">
                                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Groceries" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Pagination under the banner -->
            <div class="d-flex justify-content-between mt-3 text-muted align-items-center">
                <span class="font-weight-bold">Daily Featured Products</span>
                <div class="banner-controls d-flex align-items-center">
                    <a href="#woolworthsCarousel" role="button" data-slide="prev" class="text-dark text-decoration-none">
                        <i class="fas fa-chevron-left mr-3" style="cursor:pointer; font-size: 1.2rem;"></i>
                    </a>
                    <a href="#woolworthsCarousel" role="button" data-slide="next" class="text-dark text-decoration-none">
                        <i class="fas fa-chevron-right" style="cursor:pointer; font-size: 1.2rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Menu -->
        <div class="col-lg-4">
            <h3 class="font-weight-bold mb-2">Welcome to Moonlight</h3>
            <?php if(isLoggedIn()): ?>
                <p class="text-muted mb-3" style="font-size: 1.1rem;">Welcome back, <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</p>
            <?php else: ?>
                <p class="text-muted mb-3" style="font-size: 1.1rem;">Get the most out of your shop<br>
                <a href="login.php" class="text-success text-decoration-underline font-weight-bold">Log in or sign up</a></p>
            <?php endif; ?>
            
            <div class="quick-links d-flex flex-column gap-2 mt-4">
                <a href="products.php" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <div>
                        <span class="text-danger small font-weight-bold d-block" style="font-size: 0.75rem;">New</span>
                        <span class="font-weight-bold text-decoration-underline">Catalogue</span>
                    </div>
                    <i class="fas fa-book-open text-success fa-2x"></i>
                </a>
                
                <a href="products.php?offer=1" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <span class="font-weight-bold text-decoration-underline">All Specials & Offers</span>
                    <i class="fas fa-tag text-warning fa-2x"></i>
                </a>
                
                <a href="products.php" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <span class="font-weight-bold text-decoration-underline">Ways to Shop</span>
                    <i class="fas fa-shopping-basket text-success fa-2x"></i>
                </a>
                
                <a href="products.php?category=9" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <span class="font-weight-bold text-decoration-underline">Healthylife +Pharmacy</span>
                    <i class="fas fa-plus-square text-dark fa-2x"></i>
                </a>
                
                <a href="products.php?category=10" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <span class="font-weight-bold text-decoration-underline">Everyday Extra</span>
                    <i class="fas fa-star text-warning fa-2x"></i>
                </a>
                
                <a href="products.php?category=11" class="quick-link-card d-flex justify-content-between align-items-center p-3 mb-2 bg-white border rounded shadow-sm text-dark text-decoration-none">
                    <span class="font-weight-bold text-decoration-underline">Fresh Market Update</span>
                    <i class="fas fa-apple-alt text-danger fa-2x"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($dailyOffers)): ?>
<div class="container mt-5">
    <h2 class="font-weight-bold mb-4 text-center">Daily Offers</h2>
    <div id="offersCarousel" class="carousel slide shadow-lg rounded offers-carousel" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach($dailyOffers as $index => $offer): ?>
                <li data-target="#offersCarousel" data-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner rounded">
            <?php foreach($dailyOffers as $index => $offer): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="offers-slide d-flex flex-column flex-md-row">
                        <!-- Left Side: Content Panel -->
                        <div class="offers-content-panel p-4 p-md-5">
                            <span class="badge badge-warning text-dark mb-3 align-self-start px-3 py-2 font-weight-bold" style="border-radius: 30px; letter-spacing: 0.5px; font-size: 0.8rem;">
                                <i class="fas fa-percentage mr-1"></i> SPECIAL OFFER
                            </span>
                            <h2 class="font-weight-bold mb-3" style="font-size: 2rem; line-height: 1.2;"><?php echo htmlspecialchars($offer->name); ?></h2>
                            <p class="mb-4 d-none d-md-block" style="font-size: 0.95rem; opacity: 0.9; line-height: 1.6;"><?php echo htmlspecialchars($offer->description); ?></p>
                            <div class="d-flex align-items-center mb-4">
                                <span class="h2 font-weight-bold text-primary mb-0 mr-3">$<?php echo number_format($offer->price, 2); ?></span>
                                <span class="badge badge-outline-dark text-dark border border-dark px-2 py-1 small" style="opacity: 0.8;">Fresh Daily</span>
                            </div>
                            <form action="cart_action.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $offer->id; ?>">
                                <button type="submit" class="btn btn-warning text-dark font-weight-bold px-4 py-2 shadow-sm" style="border-radius: 30px;"><i class="fas fa-cart-plus mr-2"></i> Add to Cart</button>
                            </form>
                        </div>
                        <!-- Right Side: Image Panel -->
                        <div class="offers-image-panel">
                            <img src="<?php echo htmlspecialchars($offer->image_url); ?>" alt="<?php echo htmlspecialchars($offer->name); ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a class="carousel-control-prev" href="#offersCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#offersCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>
<?php endif; ?>

<div class="mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h2 class="font-weight-bold">Featured Products</h2>
        <a href="products.php" class="text-primary text-decoration-none font-weight-bold">View All <i class="fas fa-chevron-right ml-1"></i></a>
    </div>
    
    <div class="row">
        <?php foreach($featuredProducts as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($product->image_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name); ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($product->name); ?></h5>
                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product->description); ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="h5 mb-0 font-weight-bold text-primary">$<?php echo number_format($product->price, 2); ?></span>
                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 35px; height: 35px; padding: 0;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
