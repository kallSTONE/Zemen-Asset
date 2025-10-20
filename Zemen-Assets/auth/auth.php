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

$register_error = '';
$register_success = '';
$login_error = '';

// Handle Register
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'customer';

    if (!$name || !$email || !$password) {
        $register_error = 'Please fill all required fields.';
    } elseif ($password !== $confirmPassword) {
        $register_error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $register_error = 'Password must be at least 6 characters long.';
    } else {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $register_error = 'Email address is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");

            if ($stmt->execute([$name, $email, $phone, $hashedPassword, $role])) {
                $register_success = 'Account created successfully! You can now login.';
            } else {
                $register_error = 'Failed to create account. Please try again.';
            }
        }
    }
}

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            $redirect = $user['role'] === 'admin' ? '/admin/dashboard.php' :
                       ($user['role'] === 'seller' ? '/seller/dashboard.php' : '/customer/dashboard.php');
            header('Location: ' . SITE_URL . $redirect);
            exit;
        } else {
            $login_error = 'Invalid email or password.';
        }
    } else {
        $login_error = 'Please fill all required fields.';
    }
}

$pageTitle = "Login/Register";
require_once '../includes/header.php';
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Dosis|Roboto:300,400">
<style>
/* Paste your Codemyui CSS here */
<?php include 'codemyui-auth-style.css'; ?>
</style>

<section> 
    <div class="container">
        <form class="signUp" method="POST">
            <h3>Create Your Account</h3>
            <p>Just enter your email address<br>and your password to join.</p>
            <?php if ($register_error): ?><div class="alert alert-error"><?= $register_error ?></div><?php endif; ?>
            <?php if ($register_success): ?><div class="alert alert-success"><?= $register_success ?></div><?php endif; ?>
            <input class="w100" type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autocomplete='off' />
            <input type="email" name="email" placeholder="Insert Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete='off' />
            <input type="tel" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" />
            <input type="password" name="password" placeholder="Insert Password" required />
            <input type="password" name="confirm_password" placeholder="Verify Password" required />

            <select name="role" required>
                <option value="customer" <?= ($_POST['role'] ?? 'customer') === 'customer' ? 'selected' : '' ?>>Customer/Buyer</option>
                <option value="seller" <?= ($_POST['role'] ?? '') === 'seller' ? 'selected' : '' ?>>Seller/Agent</option>
            </select>

            <input type="hidden" name="action" value="register" />

            <button class="form-btn sx log-in" type="button">Log In</button>
            <button class="form-btn dx" type="submit">Sign Up</button>
        </form>

        <form class="signIn" method="POST">
            <h3>Welcome<br>Back!</h3>
            <button class="fb" type="button">Log In With Facebook</button>
            <p>- or -</p>
            <?php if ($login_error): ?><div class="alert alert-error"><?= $login_error ?></div><?php endif; ?>
            <input type="email" name="email" placeholder="Insert Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete='off' required />
            <input type="password" name="password" placeholder="Insert Password" required />
            <input type="hidden" name="action" value="login" />
            <button class="form-btn sx back" type="button">Back</button>
            <button class="form-btn dx" type="submit">Log In</button>
        </form>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(".log-in").click(function(){
    $(".signIn").addClass("active-dx");
    $(".signUp").addClass("inactive-sx");
    $(".signUp").removeClass("active-sx");
    $(".signIn").removeClass("inactive-dx");
  });

  $(".back").click(function(){
    $(".signUp").addClass("active-sx");
    $(".signIn").addClass("inactive-dx");
    $(".signIn").removeClass("active-dx");
    $(".signUp").removeClass("inactive-sx");
  });
</script>

<?php require_once '../includes/footer.php'; ?>
