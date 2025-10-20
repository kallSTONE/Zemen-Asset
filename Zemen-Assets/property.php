<?php
$pageTitle = "Property Details";
require_once 'includes/header.php';

$propertyId = $_GET['id'] ?? 0;
$property = getListingById($propertyId);

if (!$property) {
    header('Location: listings.php');
    exit;
}

$pageTitle = $property['title'];
$images = getListingImages($propertyId);
$reviews = getListingReviews($propertyId);

// Handle contact form submission
if ($_POST && $currentUser && $currentUser['role'] === 'customer') {
    $message = sanitizeInput($_POST['message']);
    
    if ($message) {
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO orders (customer_id, seller_id, listing_id, message) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$currentUser['id'], $property['user_id'], $propertyId, $message])) {
            $success = "Your inquiry has been sent successfully!";
        } else {
            $error = "Failed to send inquiry. Please try again.";
        }
    } else {
        $error = "Please enter a message.";
    }
}
?>

<section style="padding: 2rem 0;">
    <div class="container">
        <nav style="margin-bottom: 2rem;">
            <a href="listings.php" style="color: var(--primary-color); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Properties
            </a>
        </nav>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; align-items: start;">
            <!-- Property Details -->
            <div>
                <!-- Image Gallery -->
                <div class="property-gallery" style="margin-bottom: 2rem;">
                    <?php if ($images): ?>
                        <div class="gallery-main" style="margin-bottom: 1rem;">
                            <img src="<?= !empty($property['image_url']) ? $property['image_url'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=400' ?>" 
                                alt="<?= htmlspecialchars($property['title']) ?>"    
                                style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg);">
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                            <div class="gallery-thumbnails" style="display: flex; gap: 1rem; overflow-x: auto;">
                                <?php foreach ($images as $index => $image): ?>
                                    <img src="<?= SITE_URL . '/' . $image['image_path'] ?>" 
                                         alt="Gallery image <?= $index + 1 ?>"
                                         class="gallery-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                         style="width: 100px; height: 80px; object-fit: cover; border-radius: var(--radius-md); cursor: pointer; border: 2px solid <?= $index === 0 ? 'var(--primary-color)' : 'transparent' ?>;">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?= !empty($property['image_url']) ? $property['image_url'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=400' ?>" 
                             alt="<?= htmlspecialchars($property['title']) ?>"
                             style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg);">
                    <?php endif; ?>
                </div>
                
                <!-- Property Info -->
                <div class="property-info" style="background: var(--bg-primary); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h1 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($property['title']) ?></h1>
                            <p style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($property['location']) ?>
                            </p>
                        </div>
                        
                        <?php if ($currentUser && $currentUser['role'] === 'customer'): ?>
                            <button class="btn btn-outline favorite-btn" data-listing-id="<?= $property['id'] ?>">
                                <i class="far fa-heart"></i>
                                <span class="btn-text">Add to Favorites</span>
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
                        <div class="property-price" style="font-size: 2rem; margin: 0;">
                            <?= formatPrice($property['price']) ?>
                            <?= $property['listing_type'] === 'rent' ? '/month' : '' ?>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <span class="property-badge <?= $property['listing_type'] ?>" style="position: static;">
                                <?= $property['listing_type'] === 'sale' ? 'For Sale' : 'For Rent' ?>
                            </span>
                            <span class="property-category"><?= ucfirst($property['category']) ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 style="margin-bottom: 1rem;">Description</h3>
                        <p style="line-height: 1.6; color: var(--text-secondary);">
                            <?= nl2br(htmlspecialchars($property['description'])) ?>
                        </p>
                    </div>
                </div>
                
                <!-- Reviews Section -->
                <?php if ($reviews): ?>
                    <div class="reviews-section" style="background: var(--bg-primary); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-top: 2rem;">
                        <h3 style="margin-bottom: 1.5rem;">Reviews</h3>
                        
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item" style="border-bottom: 1px solid var(--border-light); padding-bottom: 1rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong><?= htmlspecialchars($review['reviewer_name']) ?></strong>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="color: <?= $i <= $review['rating'] ? 'var(--accent-color)' : 'var(--border-color)' ?>;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="color: var(--text-secondary); margin: 0;"><?= htmlspecialchars($review['comment']) ?></p>
                                <small style="color: var(--text-light);"><?= formatDate($review['created_at']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Contact Form -->
            <div>
                <div style="background: var(--bg-primary); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); position: sticky; top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Contact Seller</h3>
                    
                    <!-- Seller Info -->
                    <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <p><strong><?= htmlspecialchars($property['seller_name']) ?></strong></p>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0;">
                            <i class="fas fa-envelope"></i>
                            <?= htmlspecialchars($property['seller_email']) ?>
                        </p>
                        <?php if ($property['seller_phone']): ?>
                            <p style="color: var(--text-secondary); margin: 0;">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($property['seller_phone']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($currentUser && $currentUser['role'] === 'customer'): ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-error"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="message">Your Message *</label>
                                <textarea id="message" name="message" class="form-control textarea" required
                                          placeholder="I'm interested in this property. Please contact me with more details."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-paper-plane"></i>
                                Send Inquiry
                            </button>
                        </form>
                    <?php elseif ($currentUser): ?>
                        <p style="color: var(--text-secondary); text-align: center;">
                            Only customers can contact sellers.
                        </p>
                    <?php else: ?>
                        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 1rem;">
                            Please login to contact the seller.
                        </p>
                        <a href="auth/login.php" class="btn btn-primary w-full">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
window.userLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
</script>

<?php require_once 'includes/footer.php'; ?>