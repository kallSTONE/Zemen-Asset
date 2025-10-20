<?php
$pageTitle = "Properties";
require_once 'includes/header.php';

// Get search filters
$filters = [
    'category' => $_GET['category'] ?? '',
    'location' => $_GET['location'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'listing_type' => $_GET['listing_type'] ?? ''
];
// Remove empty filters
$filters = array_filter($filters);

// Get listings based on filters
$listings = searchListings($filters);
?>

<section class="search-section">
    <div class="container">
        <form  class="search-form listpg" method="GET">
            <div class="form-group">
                <!-- <label for="location">Location</label> -->
                <input type="text" id="location" name="location" class="form-control search-input" 
                       placeholder="Enter city or area" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <!-- <label for="category">Category</label> -->
                <select id="category" name="category" class="form-control">
                    <option value="">Categories</option>
                    <option value="house" <?= ($_GET['category'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                    <option value="apartment" <?= ($_GET['category'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                    <option value="commercial" <?= ($_GET['category'] ?? '') === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                    <option value="land" <?= ($_GET['category'] ?? '') === 'land' ? 'selected' : '' ?>>Land</option>
                    <option value="machinery" <?= ($_GET['category'] ?? '') === 'machinery' ? 'selected' : '' ?>>Machinery</option>
                </select>
            </div>
            
            <div class="form-group">
                <!-- <label for="listing_type">Type</label> -->
                <select id="listing_type" name="listing_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="sale" <?= ($_GET['listing_type'] ?? '') === 'sale' ? 'selected' : '' ?>>For Sale</option>
                    <option value="rent" <?= ($_GET['listing_type'] ?? '') === 'rent' ? 'selected' : '' ?>>For Rent</option>
                </select>
            </div>
            
            <div class="form-group">
                <!-- <label for="min_price">Min Price</label> -->
                <input type="number" id="min_price" name="min_price" class="form-control" 
                       placeholder="Min_price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <!-- <label for="max_price">Max Price</label> -->
                <input type="number" id="max_price" name="max_price" class="form-control" 
                       placeholder="Max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </div>
        </form>
    </div>
</section>

<section style="padding: 3rem 0;">
    <div class="container">
        <div style="display: grid; margin-bottom: 2rem; width:fit-content;">
            <div>
                <p style="color: var(--text-secondary);">
                    Found <?= count($listings) ?> properties
                    <?php if (!empty($filters)): ?>
                        matching your criteria
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if (!empty($filters)): ?>
                <a href="listings.php" class="btn btn-outline btn-sm clearbtn">Clear Filters</a>
            <?php endif; ?>
        </div>
        
        <?php if ($listings): ?>
            <div class="properties-grid" id="searchResults">
                <?php foreach ($listings as $listing): ?>
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
                                <?php if ($currentUser): ?>
                                    <button class="btn btn-sm btn-outline favorite-btn" data-listing-id="<?= $listing['id'] ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        

            
            <?php else: ?>
            <div class="text-center" style="padding: 4rem 0;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3>No properties found</h3>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">Try adjusting your search criteria or browse all properties.</p>
                <a href="listings.php" class="btn btn-primary">View All Properties</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
window.userLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
</script>

<?php require_once 'includes/footer.php'; ?>