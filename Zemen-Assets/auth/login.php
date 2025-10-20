<?php
require_once '../includes/config.php';
include_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    $redirect = $role === 'admin' ? '/admin/dashboard.php' : 
               ($role === 'seller' ? '/seller/dashboard.php' : '/customer/dashboard.php');
    header('Location: ' . SITE_URL . $redirect);
    exit;
}

$error = '';

if ($_POST) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if ($email && $password) {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        //authentical user without hashing the password
        // if ($user && ($password == $user['password'])) {

        // authentical user with hashed password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            
            // Redirect based on role
            $redirect = $user['role'] === 'admin' ? '/admin/dashboard.php' : 
                       ($user['role'] === 'seller' ? '/seller/dashboard.php' : '/customer/dashboard.php');
            header('Location: ' . SITE_URL . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill all required fields.';
    }
}

$pageTitle = "Login";
require_once '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Login to Your Account</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Login</button>
        </div>
    </form>
    
    <div class="text-center" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-light);">
        <p>Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a></p>
        
        <div style="margin-top: 1rem;">
            <p style="font-size: 0.875rem; color: var(--text-secondary);">Demo Accounts:</p>
            <p style="font-size: 0.75rem; color: var(--text-light);">
                Admin: admin@property.com | Seller: john@property.com | Customer: mike@email.com<br>
                Password for all: admin123
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>