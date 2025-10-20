<?php
require_once 'functions.php';
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Property Platform</title>
    <link rel="stylesheet" href="/property-platform/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-brand">
                    <a href="<?= SITE_URL ?>/index.php">
                        <img src="/property-platform/assets/images/icon/logo-icon-dark.png" alt="Zemen Assets" style="height:32px; vertical-align:middle; margin-right:8px;">
                        Zemen Assets
                    </a>
                </div>
                
                <div class="nav-menu">
                    <a href="<?= SITE_URL ?>/index.php">Home</a>
                    <a href="<?= SITE_URL ?>/listings.php">Properties</a>
                    
                    <?php if ($currentUser): ?>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <a href="<?= SITE_URL ?>/admin/dashboard.php">Admin Panel</a>
                        <?php elseif ($currentUser['role'] === 'seller'): ?>
                            <a href="<?= SITE_URL ?>/seller/dashboard.php">Dashboard</a>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/customer/dashboard.php">My Account</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="nav-auth">
                    <?php if ($currentUser): ?>
                        <div class="user-menu">
                            <span class="user-greeting">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($currentUser['name']) ?>
                            </span>
                            <a href="<?= SITE_URL ?>/auth/logout.php" class="btn btn-outline">Logout</a>
                        </div>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-outline">Login</a>
                        <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
                
                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="main-content">