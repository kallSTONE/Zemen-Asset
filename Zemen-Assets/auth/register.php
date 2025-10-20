<?php
require_once '../includes/config.php';
include_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'customer';
    
    // Validate inputs
    if (!$name || !$email || !$password) {
        $error = 'Please fill all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $db = getDBConnection();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email address is already registered.';
        } else {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $phone, $hashedPassword, $role])) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}

$pageTitle = "Register";
require_once '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Create Your Account</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="role">Account Type *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="customer" <?= ($_POST['role'] ?? 'customer') === 'customer' ? 'selected' : '' ?>>
                    Customer/Buyer
                </option>
                <option value="seller" <?= ($_POST['role'] ?? '') === 'seller' ? 'selected' : '' ?>>
                    Seller/Agent
                </option>
            </select>
            <small style="color: var(--text-secondary); font-size: 0.875rem;">
                Choose "Seller/Agent" if you want to list properties
            </small>
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <small style="color: var(--text-secondary); font-size: 0.875rem;">
                Minimum 6 characters
            </small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
        </div>
    </form>
    
    <div class="text-center" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-light);">
        <p>Already have an account? <a href="login.php" style="color: var(--primary-color);">Login here</a></p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>