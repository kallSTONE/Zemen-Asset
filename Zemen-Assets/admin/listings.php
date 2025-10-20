<?php
$pageTitle = "Properties";
require_once '../includes/header.php';

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

<section style="padding: 2rem 0; background: var(--bg-primary);">
    <div class="container content-center">
        <h1>All Properties</h1>
        <p style="color: var(--text-secondary);">Browse our complete collection of properties</p>
    </div>
</section>

<section class="search-section">
    <div class="container">
        <form id="searchForm" class="search-form" method="GET">
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control search-input" 
                       placeholder="Enter city or area" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="house" <?= ($_GET['category'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                    <option value="apartment" <?= ($_GET['category'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                    <option value="commercial" <?= ($_GET['category'] ?? '') === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                    <option value="land" <?= ($_GET['land'] ?? '') === 'land' ? 'selected' : '' ?>>Land</option>
                    <option value="machinery" <?= ($_GET['category'] ?? '') === 'machinery' ? 'selected' : '' ?>>Machinery</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="listing_type">Type</label>
                <select id="listing_type" name="listing_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="sale" <?= ($_GET['listing_type'] ?? '') === 'sale' ? 'selected' : '' ?>>For Sale</option>
                    <option value="rent" <?= ($_GET['listing_type'] ?? '') === 'rent' ? 'selected' : '' ?>>For Rent</option>
                </select>
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

<section style="padding: 2rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
            <div>
                <p style="color: var(--text-secondary);">
                    Found <?= count($listings) ?> properties
                    <?php if (!empty($filters)): ?>
                        matching your criteria
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if (!empty($filters)): ?>
                <a href="listings.php" class="btn btn-outline btn-sm">Clear Filters</a>
            <?php endif; ?>
        </div>
        
          <?php if ($listings): ?>
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
                    <h3 style="margin: 0;">Recent Listings</h3>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Inquiries</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="<?= !empty($listing['image_url']) ? $listing['image_url'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=400' ?>" 
                                             alt="<?= htmlspecialchars($listing['title']) ?>"
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-sm);">
                                        <div>
                                            <strong><?= htmlspecialchars($listing['title']) ?></strong><br>
                                            <small style="color: var(--text-secondary);"><?= htmlspecialchars($listing['location']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="property-category"><?= ucfirst($listing['category']) ?></span>
                                </td>
                                <td>
                                    <?= formatPrice($listing['price']) ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $listing['status'] ?>">
                                        <?= ucfirst($listing['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $listing['inquiry_count'] ?>
                                </td>
                                <td>
                                    <?= formatDate($listing['created_at']) ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?= SITE_URL ?>/property.php?id=<?= $listing['id'] ?>" class="btn btn-sm btn-outline" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($listing['status'] === 'pending'): ?>
                                            <a href="edit-listing.php?id=<?= $listing['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center" style="padding: 4rem 0; background: var(--bg-primary); border-radius: var(--radius-lg);">
            <i class="fas fa-building" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h3>No listings yet</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Start by adding your first property listing.</p>
            <a href="add-listing.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i>
                Add Your First Property
            </a>
        </div>
    <?php endif; ?>  
    </div>
</section>

<script>
window.userLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
</script>

<?php require_once '../includes/footer.php'; ?>