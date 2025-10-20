<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Get featured listings
$featuredListings = getListings('approved', 6);
?>

<section class="hero hero-banner">
    <div class="container">
        <h1>Find Your Perfect Property</h1>
        <p>Discover thousands of properties for sale and rent. Connect with verified sellers and agents.</p>
        
    <section class="search-section">
        <div class="container">
            <div  class="heroLinks"style="display:flex;">
                <a href="<?= SITE_URL ?>/auth/register.php" >buy</a>
                <a href="<?= SITE_URL ?>/auth/register.php" >rent</a>
                <a href="<?= SITE_URL ?>/auth/register.php" >sell</a>
            </div>
            <form class="search-form" action="listings.php" method="GET">
                <div class="form-group">
                    <input type="text" id="location" name="location" class="form-control search-input" placeholder="Enter city or area" required>
                    <!-- Invisible submit button to make 'Enter' work -->
                    <button type="submit" style="display:none;"></button>
                </div>
            </form>
        </div>
    </section>
    </div>
</section>



<section style="padding: 2rem 0;">
    <div class="container">
        <div class="text-center" style="margin-bottom: 3rem;">
            <h2>Featured Properties</h2>
            <p style="color: var(--text-secondary);">Discover our hand-picked selection of premium properties</p>
        </div>
        
        <?php if ($featuredListings): ?>
            <div class="properties-grid">
                <?php foreach ($featuredListings as $listing): ?>
                    <div class="property-card">
                        <a href="property.php?id=<?= $listing['id'] ?>">
                            <div class="property-image">
                                    <img src="<?= !empty($listing['image_url']) ? $listing['image_url'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=400' ?>" 
                                        alt="<?= htmlspecialchars($listing['title']) ?>">   
                                
                                <div class="property-badge <?= $listing['listing_type'] ?>">
                                    <?= $listing['listing_type'] === 'sale' ? 'For Sale' : 'For Rent' ?>
                                </div>
                            </div>
                        </a>
                        
                        <div class="property-content">
                            <div class="property-title">
                                <a href="property.php?id=<?= $listing['id'] ?>">
                                    <?= htmlspecialchars($listing['title']) ?>
                                </a>
                            </div>
                            
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($listing['location']) ?>
                            </div>
                            
                            <div class="property-price">
                                <?= formatPrice($listing['price']) ?>
                                <?= $listing['listing_type'] === 'rent' ? '/month' : '' ?>
                            </div>
                            
                            <div class="property-features">
                                <span class="property-category"><?= ucfirst($listing['category']) ?></span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">
                                    by <?= htmlspecialchars($listing['seller_name']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center" style="margin-top: 3rem;">
                <a href="listings.php" class="btn btn-lg btn-outline">View All Properties</a>
            </div>
        <?php else: ?>
            <div class="text-center">
                <p>No properties available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section style="background: var(--bg-primary); padding: 4rem 0; margin-top: 2rem;">
    <div class="container">
        <div class="text-center" style="margin-bottom: 3rem;">
            <h2>Why Choose PropertyHub?</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div class="text-center">
                <div style="background: var(--primary-color); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Verified Listings</h3>
                <p style="color: var(--text-secondary);">All properties are verified by our team to ensure quality and authenticity.</p>
            </div>
            
            <div class="text-center">
                <div style="background: var(--secondary-color); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Direct Contact</h3>
                <p style="color: var(--text-secondary);">Connect directly with property owners and agents without any middleman.</p>
            </div>
            
            <div class="text-center">
                <div style="background: var(--accent-color); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Advanced Search</h3>
                <p style="color: var(--text-secondary);">Find exactly what you're looking for with our powerful search and filter options.</p>
            </div>
        </div>
    </div>
</section>

<script>
// Set user login status for JavaScript
window.userLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
</script>

<?php require_once 'includes/footer.php'; ?>