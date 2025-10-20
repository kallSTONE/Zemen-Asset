<?php
$pageTitle = "Admin Dashboard";
require_once '../includes/header.php';
requireRole('admin');

$db = getDBConnection();

// Get statistics
$stats = [];

// Total users
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
$stats['users'] = $stmt->fetch()['count'];

// Total listings
$stmt = $db->query("SELECT COUNT(*) as count FROM listings");
$stats['listings'] = $stmt->fetch()['count'];

// Pending listings
$stmt = $db->query("SELECT COUNT(*) as count FROM listings WHERE status = 'pending'");
$stats['pending'] = $stmt->fetch()['count'];

// Total orders
$stmt = $db->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $stmt->fetch()['count'];

// Get recent listings for approval
$stmt = $db->query("
    SELECT l.*, u.name as seller_name 
    FROM listings l 
    JOIN users u ON l.user_id = u.id 
    WHERE l.status = 'pending' 
    ORDER BY l.created_at DESC 
    LIMIT 10
");
$pendingListings = $stmt->fetchAll();

// Handle listing approval/rejection
if ($_POST && isset($_POST['action']) && isset($_POST['listing_id'])) {
    $action = $_POST['action'];
    $listingId = (int)$_POST['listing_id'];
    
    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $db->prepare("UPDATE listings SET status = ? WHERE id = ?");
        if ($stmt->execute([$action, $listingId])) {
            $success = "Listing has been " . $action . " successfully.";
            // Refresh the page to show updated data
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Failed to update listing status.";
        }
    }
}
?>

<div class="dashboard-header">
    <div class="container content-center">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <p class="dashboard-subtitle">Manage platform users, listings, and orders</p>
    </div>
</div>

<div class="container">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['users'] ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['listings'] ?></div>
            <div class="stat-label">Total Listings</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['pending'] ?></div>
            <div class="stat-label">Pending Approval</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['orders'] ?></div>
            <div class="stat-label">Total Inquiries</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <a href="users.php" class="btn btn-outline btn-lg">
            <i class="fas fa-users"></i>
            Manage Users
        </a>
        <a href="listings.php" class="btn btn-outline btn-lg">
            <i class="fas fa-building"></i>
            Manage Approved Listings
        </a>
        <a href="orders.php" class="btn btn-outline btn-lg">
            <i class="fas fa-envelope"></i>
            View Inquiries
        </a>
    </div>
    
    <!-- Pending Listings -->
    <?php if ($pendingListings): ?>
        <div class="table-container">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
                <h3 style="margin: 0;">Pending Listings Approval</h3>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Seller</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingListings as $listing): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($listing['title']) ?></strong><br>
                                <small style="color: var(--text-secondary);"><?= htmlspecialchars($listing['location']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($listing['seller_name']) ?></td>
                            <td>
                                <span class="property-category"><?= ucfirst($listing['category']) ?></span>
                            </td>
                            <td><?= formatPrice($listing['price']) ?></td>
                            <td><?= formatDate($listing['created_at']) ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                        <input type="hidden" name="action" value="approved">
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Approve this listing?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                        <input type="hidden" name="action" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this listing?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    
                                    <a href="<?= SITE_URL ?>/property.php?id=<?= $listing['id'] ?>" class="btn btn-sm btn-outline" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center" style="padding: 2rem; background: var(--bg-primary); border-radius: var(--radius-lg); margin: 2rem 0;">
            <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success-color); margin-bottom: 1rem;"></i>
            <h3>All caught up!</h3>
            <p style="color: var(--text-secondary);">No pending listings to review.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>