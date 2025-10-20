<?php
$pageTitle = "My Account";
require_once '../includes/header.php';
requireLogin();

$db = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user statistics
$stats = [];

// Favorite listings
$stmt = $db->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
$stmt->execute([$userId]);
$stats['favorites'] = $stmt->fetch()['count'];

// Inquiries sent
$stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
$stmt->execute([$userId]);
$stats['inquiries'] = $stmt->fetch()['count'];

// Get recent favorites
$stmt = $db->prepare("
    SELECT l.*, u.name as seller_name,
           (SELECT image_path FROM listing_images WHERE listing_id = l.id LIMIT 1) as primary_image
    FROM favorites f
    JOIN listings l ON f.listing_id = l.id
    JOIN users u ON l.user_id = u.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
    LIMIT 6
");
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll();

// Get recent inquiries
$stmt = $db->prepare("
    SELECT o.*, l.title as listing_title, u.name as seller_name
    FROM orders o
    JOIN listings l ON o.listing_id = l.id
    JOIN users u ON o.seller_id = u.id
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$inquiries = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title">My Account</h1>
        <p class="dashboard-subtitle">Manage your favorites and inquiries</p>
    </div>
</div>

<div class="container">
    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['favorites'] ?></div>
            <div class="stat-label">Saved Properties</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['inquiries'] ?></div>
            <div class="stat-label">Inquiries Sent</div>
        </div>
        
        <?php if ($currentUser['role'] === 'customer'): ?>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-store"></i>
                </div>
                <div style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <a href="../auth/register.php?upgrade=seller" style="color: white; text-decoration: none;">
                        Become a Seller
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <a href="<?= SITE_URL ?>/listings.php" class="btn btn-outline btn-lg">
            <i class="fas fa-search"></i>
            Browse Properties
        </a>
        <a href="favorites.php" class="btn btn-outline btn-lg">
            <i class="fas fa-heart"></i>
            My Favorites
        </a>
        <a href="inquiries.php" class="btn btn-outline btn-lg">
            <i class="fas fa-envelope"></i>
            My Inquiries
        </a>
    </div>
    
    <!-- Favorite Properties -->
    <?php if ($favorites): ?>
        <div style="margin: 3rem 0;">
            <h3 style="margin-bottom: 1.5rem;">Recently Saved Properties</h3>
            <div class="properties-grid">
                <?php foreach ($favorites as $property): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <img src="<?= $property['primary_image'] ? SITE_URL . '/' . $property['primary_image'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=400' ?>" 
                                 alt="<?= htmlspecialchars($property['title']) ?>">
                            <div class="property-badge <?= $property['listing_type'] ?>">
                                <?= $property['listing_type'] === 'sale' ? 'For Sale' : 'For Rent' ?>
                            </div>
                        </div>
                        
                        <div class="property-content">
                            <div class="property-title">
                                <a href="<?= SITE_URL ?>/property.php?id=<?= $property['id'] ?>">
                                    <?= htmlspecialchars($property['title']) ?>
                                </a>
                            </div>
                            
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($property['location']) ?>
                            </div>
                            
                            <div class="property-price">
                                <?= formatPrice($property['price']) ?>
                                <?= $property['listing_type'] === 'rent' ? '/month' : '' ?>
                            </div>
                            
                            <div class="property-features">
                                <span class="property-category"><?= ucfirst($property['category']) ?></span>
                                <button class="btn btn-sm btn-outline favorite-btn favorited" data-listing-id="<?= $property['id'] ?>">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($stats['favorites'] > 6): ?>
                <div class="text-center" style="margin-top: 2rem;">
                    <a href="favorites.php" class="btn btn-outline">View All Favorites</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <!-- Recent Inquiries -->
    <?php if ($inquiries): ?>
        <div class="table-container">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
                <h3 style="margin: 0;">Recent Inquiries</h3>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Seller</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td>
                                <a href="<?= SITE_URL ?>/property.php?id=<?= $inquiry['listing_id'] ?>" 
                                   style="color: var(--primary-color); text-decoration: none;">
                                    <?= htmlspecialchars($inquiry['listing_title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($inquiry['seller_name']) ?></td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars(substr($inquiry['message'], 0, 100)) ?>
                                    <?= strlen($inquiry['message']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $inquiry['status'] ?>">
                                    <?= ucfirst($inquiry['status']) ?>
                                </span>
                            </td>
                            <td><?= formatDate($inquiry['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($stats['inquiries'] > 5): ?>
            <div class="text-center" style="margin-top: 1rem;">
                <a href="inquiries.php" class="btn btn-outline">View All Inquiries</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Empty States -->
    <?php if (!$favorites && !$inquiries): ?>
        <div class="text-center" style="padding: 4rem 0; background: var(--bg-primary); border-radius: var(--radius-lg);">
            <i class="fas fa-home" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h3>Welcome to PropertyHub!</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Start browsing properties to save your favorites and connect with sellers.
            </p>
            <a href="<?= SITE_URL ?>/listings.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search"></i>
                Browse Properties
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
window.userLoggedIn = true;
</script>

<?php require_once '../includes/footer.php'; ?>