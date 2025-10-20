<?php
$pageTitle = "Seller Dashboard";
require_once '../includes/header.php';
requireRole('seller');

$db = getDBConnection();
$userId = $_SESSION['user_id'];

// Get seller statistics
$stats = [];

// Total listings
$stmt = $db->prepare("SELECT COUNT(*) as count FROM listings WHERE user_id = ?");
$stmt->execute([$userId]);
$stats['total'] = $stmt->fetch()['count'];

// Approved listings
$stmt = $db->prepare("SELECT COUNT(*) as count FROM listings WHERE user_id = ? AND status = 'approved'");
$stmt->execute([$userId]);
$stats['approved'] = $stmt->fetch()['count'];

// Pending listings
$stmt = $db->prepare("SELECT COUNT(*) as count FROM listings WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$userId]);
$stats['pending'] = $stmt->fetch()['count'];

// Total inquiries
$stmt = $db->prepare("SELECT COUNT(*) as count FROM orders WHERE seller_id = ?");
$stmt->execute([$userId]);
$stats['inquiries'] = $stmt->fetch()['count'];

// Get recent listings
$stmt = $db->prepare("
    SELECT l.*, 
           (SELECT image_path FROM listing_images WHERE listing_id = l.id LIMIT 1) as primary_image,
           COUNT(o.id) as inquiry_count
    FROM listings l 
    LEFT JOIN orders o ON l.id = o.listing_id
    WHERE l.user_id = ? 
    GROUP BY l.id
    ORDER BY l.created_at DESC 
    LIMIT 10
");
$stmt->execute([$userId]);
$listings = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title">Seller Dashboard</h1>
        <p class="dashboard-subtitle">Manage your properties and inquiries</p>
    </div>
</div>

<div class="container">
    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total'] ?></div>
            <div class="stat-label">Total Listings</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['approved'] ?></div>
            <div class="stat-label">Approved</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['pending'] ?></div>
            <div class="stat-label">Pending</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['inquiries'] ?></div>
            <div class="stat-label">Inquiries</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <a href="add-listing.php" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i>
            Add New Property
        </a>
        <a href="listings.php" class="btn btn-outline btn-lg">
            <i class="fas fa-building"></i>
            My Listings
        </a>
        <a href="inquiries.php" class="btn btn-outline btn-lg">
            <i class="fas fa-envelope"></i>
            View Inquiries
        </a>
    </div>
    
    <!-- Recent Listings -->
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
                                    <img src="<?= $listing['primary_image'] ? SITE_URL . '/' . $listing['primary_image'] : 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg?auto=compress&cs=tinysrgb&w=100' ?>" 
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
                            <td><?= formatPrice($listing['price']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $listing['status'] ?>">
                                    <?= ucfirst($listing['status']) ?>
                                </span>
                            </td>
                            <td><?= $listing['inquiry_count'] ?></td>
                            <td><?= formatDate($listing['created_at']) ?></td>
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

<?php require_once '../includes/footer.php'; ?>