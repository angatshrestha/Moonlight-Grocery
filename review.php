<?php
// review.php
// Dedicated Feedback Portal for the Showcase Exhibition.
require_once __DIR__ . '/includes/header.php';

// Dynamically auto-create the reviews table if it does not exist (robust developer staging)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) DEFAULT NULL,
        rating INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
} catch (PDOException $e) {
    // Fail silently if table already exists or permissions issue
}

$success_msg = "";
$error_msg = "";

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');
    
    if (empty($name) || empty($comment) || $rating < 1 || $rating > 5) {
        $error_msg = "Please fill in all required fields and select a star rating.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (name, email, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $rating, $comment]);
            $success_msg = "Thank you so much for your valuable feedback! Your review is now live on our showcase board.";
        } catch (PDOException $e) {
            $error_msg = "Error submitting feedback: " . $e->getMessage();
        }
    }
}

// Query Active Reviews
$all_reviews = [];
$avg_rating = 5.0;
$total_reviews = 0;
try {
    $stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $all_reviews = $stmt->fetchAll();
    
    if (count($all_reviews) > 0) {
        $total_reviews = count($all_reviews);
        $sum_rating = 0;
        foreach ($all_reviews as $rev) {
            $sum_rating += $rev->rating;
        }
        $avg_rating = round($sum_rating / $total_reviews, 1);
    }
} catch (PDOException $e) {
    // Fallback if table query fails
}

// Mailto URL for business inquiries
$mailto_email = "angatshrestha2@gmail.com";
$mailto_subject = rawurlencode("Project Inquiry from Moonlight Showcase");
$mailto_body = rawurlencode("Hi Angat,\n\nI visited your e-commerce showcase today and was absolutely wowed by your Moonlight Grocery application! I'd love to chat about custom-building a similar digital retail product for our business.\n\nBest regards,\n[Your Name]\n[Company Name]");
$mailto_link = "mailto:$mailto_email?subject=$mailto_subject&body=$mailto_body";

// Public free Google Chart API to dynamically generate a clean QR Code of the mailto link!
$qr_code_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($mailto_link) . "&choe=UTF-8";
?>

<style>
/* Showcase & Review Layout Styles */
.showcase-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1e0b3b 100%);
    color: white;
    border-radius: 24px;
    padding: 3rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 15px 30px rgba(45, 20, 87, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.review-card {
    border-radius: 16px;
    border: 1px solid var(--border-color);
    background: white;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
}

.review-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
}

/* Interactive Star Rating Selector */
.star-rating-selector {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.star-rating-selector input {
    display: none;
}

.star-rating-selector label {
    font-size: 2rem;
    color: #cbd5e1;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    margin-right: 0.5rem;
}

.star-rating-selector label:hover,
.star-rating-selector label:hover ~ label,
.star-rating-selector input:checked ~ label {
    color: #ffc107;
    transform: scale(1.15);
}

/* QR Code Pulse Animation */
.qr-container {
    background: #f8fafc;
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px dashed #cbd5e1;
    display: inline-block;
    transition: all 0.3s ease;
}

.qr-container:hover {
    background: #f1f5f9;
    border-color: var(--primary-color);
}

.qr-image {
    width: 180px;
    height: 180px;
    object-fit: contain;
    border-radius: 8px;
}

.pulsing-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}
</style>

<!-- Showcase Header -->
<div class="showcase-hero text-center text-md-left">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge badge-warning text-dark font-weight-bold px-3 py-2 mb-3 pulsing-badge" style="font-size: 0.9rem; border-radius: 30px; letter-spacing: 0.5px;">
                <i class="fas fa-trophy mr-1"></i> LIVE EXHIBITION SHOWCASE
            </span>
            <h1 class="font-weight-bold text-white mb-2" style="font-size: 2.8rem; letter-spacing: -1px;">Exhibition Feedback & Staging</h1>
            <p class="lead mb-0 text-light opacity-90" style="font-size: 1.1rem; line-height: 1.6;">
                Welcome to our showcase page! Over **100+ visitors** are exploring our grocery solution today. Share your real-time review below, or scan the business QR code to draft a project inquiry directly to us!
            </p>
        </div>
        <div class="col-md-4 text-center mt-4 mt-md-0">
            <div class="bg-white p-3 d-inline-block rounded-circle shadow-lg" style="width: 140px; height: 140px;">
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-dark">
                    <h2 class="font-weight-bold mb-0 text-primary" style="font-size: 2.2rem;"><?php echo $avg_rating; ?></h2>
                    <div class="text-warning mb-1" style="font-size: 0.8rem;">
                        <?php 
                        $full_stars = floor($avg_rating);
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $full_stars) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <small class="text-muted font-weight-bold"><?php echo $total_reviews; ?> Reviews</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show p-4 shadow-sm border-0 mb-4" role="alert" style="border-radius: 16px; border-left: 5px solid #28a745 !important;">
        <h5 class="font-weight-bold text-success mb-1"><i class="fas fa-check-circle mr-2"></i> Feedback Submitted!</h5>
        <?php echo $success_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if(!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show p-4 shadow-sm border-0 mb-4" role="alert" style="border-radius: 16px; border-left: 5px solid #dc3545 !important;">
        <h5 class="font-weight-bold text-danger mb-1"><i class="fas fa-exclamation-circle mr-2"></i> Submission Failed</h5>
        <?php echo $error_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Left Column: Submit Review Form -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 no-hover" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <h3 class="font-weight-bold text-primary mb-3"><i class="fas fa-pen-nib mr-2"></i>Leave a Review</h3>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    Let us know what you think of our Moonlight Grocery design, categories, and shopping flow! Your feedback updates our dynamic board instantly.
                </p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="submit_review">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required placeholder="e.g. John Doe" style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Email / Company <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="email" class="form-control form-control-lg bg-light border-0" placeholder="e.g. Google / john@doe.com" style="border-radius: 12px; font-size: 0.95rem;">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold d-block">Overall Rating <span class="text-danger">*</span></label>
                        <div class="star-rating-selector">
                            <input type="radio" id="star5" name="rating" value="5" checked><label for="star5" title="Excellent"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="Good"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="Average"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="Below Average"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="Poor"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Your Comments & Suggestions <span class="text-danger">*</span></label>
                        <textarea name="comment" rows="4" class="form-control bg-light border-0" required placeholder="What did you love? Any improvements or suggestions?" style="border-radius: 12px; font-size: 0.95rem; line-height: 1.6;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold py-3 shadow" style="border-radius: 12px; letter-spacing: 0.5px;">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Live Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Business Inquiries & Mail QR Code -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 no-hover text-white text-center h-100" style="border-radius: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center align-items-center">
                <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-3 align-self-center small" style="border-radius: 30px;">
                    <i class="fas fa-code mr-1"></i> PARTNER WITH US
                </span>
                <h3 class="font-weight-bold text-white mb-3" style="font-size: 1.8rem;">Build Your Product With Us!</h3>
                <p class="text-light opacity-80 mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                    Loved the premium visual aesthetics, responsive database interfaces, and support chatbot features of our **Moonlight Grocery** project? 
                    <br><br>
                    Scan the QR code below using your phone camera to **instantly draft an email** straight to our team to discuss your customized business needs!
                </p>
                
                <div class="qr-container mb-4 shadow">
                    <img src="<?php echo $qr_code_url; ?>" alt="Scan to Email Us" class="qr-image">
                </div>
                
                <h6 class="font-weight-bold text-warning mb-1"><i class="fas fa-camera mr-1"></i> Scan with your phone</h6>
                <p class="small text-light opacity-60 mb-4">Drafts a pre-formatted project inquiry email straight to us.</p>
                
                <a href="<?php echo $mailto_link; ?>" class="btn btn-outline-light btn-block font-weight-bold py-2 mt-auto" style="border-radius: 10px; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-envelope mr-2"></i> Open Mail Direct
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Live Feedback Wall -->
<div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-weight-bold text-primary mb-0"><i class="fas fa-comments mr-2"></i> Showcase Feedback Wall</h3>
        <span class="badge badge-primary px-3 py-2 font-weight-bold" style="border-radius: 30px;"><?php echo $total_reviews; ?> Reviews Live</span>
    </div>
    
    <?php if (empty($all_reviews)): ?>
        <div class="card shadow-sm border-0 no-hover text-center py-5" style="border-radius: 20px; background: #f8fafc;">
            <div class="card-body">
                <i class="fas fa-heart fa-3x text-muted mb-3" style="opacity: 0.4;"></i>
                <h5 class="font-weight-bold text-secondary">Be the first to leave a review!</h5>
                <p class="text-muted small mb-0">Submitting the form above will display your feedback instantly here.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($all_reviews as $rev): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card review-card shadow-sm border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-weight-bold mr-3" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                    <?php echo strtoupper(substr($rev->name, 0, 1)); ?>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0"><?php echo htmlspecialchars($rev->name); ?></h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <?php echo !empty($rev->email) ? htmlspecialchars($rev->email) : 'Visitor'; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-warning mb-2" style="font-size: 0.9rem;">
                            <?php 
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $rev->rating) {
                                    echo '<i class="fas fa-star mr-1"></i>';
                                } else {
                                    echo '<i class="far fa-star mr-1"></i>';
                                }
                            }
                            ?>
                        </div>
                        
                        <p class="text-secondary mb-0 small" style="line-height: 1.6; font-style: italic;">
                            "<?php echo htmlspecialchars($rev->comment); ?>"
                        </p>
                        
                        <hr class="my-3" style="opacity: 0.4;">
                        
                        <div class="text-right">
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="far fa-clock mr-1"></i> <?php echo date('M d, Y h:i A', strtotime($rev->created_at)); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
