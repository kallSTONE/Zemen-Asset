<?php
$pageTitle = "Manage Users";
require_once '../includes/header.php';
requireRole('admin');

$db = getDBConnection();

// Handle user role changes or deletions
if ($_POST) {
    if (isset($_POST['delete_user'])) {
        $userId = (int)$_POST['user_id'];
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        if ($stmt->execute([$userId])) {
            $success = "User deleted successfully.";
        } else {
            $error = "Failed to delete user.";
        }
    }
}

// Get all users
$stmt = $db->query("
    SELECT u.*, 
           COUNT(l.id) as listing_count,
           COUNT(o.id) as order_count
    FROM users u 
    LEFT JOIN listings l ON u.id = l.user_id
    LEFT JOIN orders o ON u.id = o.customer_id OR u.id = o.seller_id
    WHERE u.role != 'admin'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div class="container content-center">
        <h1 class="dashboard-title">Manage Users</h1>
        <p class="dashboard-subtitle">View and manage all platform users</p>
    </div>
</div>

<div class="container">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <p style="color: var(--text-secondary);">Total Users: <?= count($users) ?></p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    
    <?php if ($users): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User Details</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars($user['name']) ?></strong><br>
                                    <small style="color: var(--text-secondary);"><?= htmlspecialchars($user['email']) ?></small>
                                    <?php if ($user['phone']): ?>
                                        <br><small style="color: var(--text-light);"><?= htmlspecialchars($user['phone']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $user['role'] === 'seller' ? 'approved' : 'pending' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                    <?php if ($user['role'] === 'seller'): ?>
                                        <?= $user['listing_count'] ?> listings
                                    <?php endif; ?>
                                    <?php if ($user['order_count'] > 0): ?>
                                        <br><?= $user['order_count'] ?> inquiries
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center" style="padding: 4rem 0;">
            <i class="fas fa-users" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h3>No users found</h3>
            <p style="color: var(--text-secondary);">No users have registered yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>