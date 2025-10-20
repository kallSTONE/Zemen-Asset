<?php
$pageTitle = "Add New Property";
require_once '../includes/header.php';
requireRole('seller');

$error = '';
$success = '';

if ($_POST) {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $location = sanitizeInput($_POST['location']);
    $listingType = $_POST['listing_type'];
    
    if (!$title || !$description || !$price || !$location) {
        $error = 'Please fill all required fields.';
    } else {
        $db = getDBConnection();
        
        try {
            $db->beginTransaction();
            
            // Insert listing
            $stmt = $db->prepare("INSERT INTO listings (user_id, title, description, category, price, location, listing_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $price, $location, $listingType]);
            $listingId = $db->lastInsertId();
            
            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = '../uploads/listings/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . $_FILES['images']['name'][$key];
                        $filePath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($tmpName, $filePath)) {
                            $stmt = $db->prepare("INSERT INTO listing_images (listing_id, image_path, is_primary) VALUES (?, ?, ?)");
                            $stmt->execute([$listingId, 'uploads/listings/' . $fileName, $key === 0]);
                        }
                    }
                }
            }
            
            $db->commit();
            $success = 'Property listing submitted successfully! It will be reviewed by our admin team.';
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Failed to create listing. Please try again.';
        }
    }
}
?>

<div class="container" style="max-width: 800px; margin: 2rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Add New Property</h1>
        <a href="dashboard.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <div class="form-container" style="margin: 0; max-width: none;">
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="title">Property Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="house" <?= ($_POST['category'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                        <option value="apartment" <?= ($_POST['category'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                        <option value="commercial" <?= ($_POST['category'] ?? '') === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                        <option value="land" <?= ($_POST['category'] ?? '') === 'land' ? 'selected' : '' ?>>Land</option>
                        <option value="machinery" <?= ($_POST['category'] ?? '') === 'machinery' ? 'selected' : '' ?>>Machinery</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="form-control textarea" required
                          placeholder="Describe your property in detail..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="price">Price ($) *</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required
                           value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="listing_type">Listing Type *</label>
                    <select id="listing_type" name="listing_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="sale" <?= ($_POST['listing_type'] ?? '') === 'sale' ? 'selected' : '' ?>>For Sale</option>
                        <option value="rent" <?= ($_POST['listing_type'] ?? '') === 'rent' ? 'selected' : '' ?>>For Rent</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" required
                           value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="images">Property Images</label>
                <input type="file" id="images" name="images[]" class="form-control file-input" 
                       multiple accept="image/*">
                <small style="color: var(--text-secondary); font-size: 0.875rem;">
                    Upload up to 5 images. First image will be used as the main photo.
                </small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i>
                    Add Property
                </button>
                <a href="dashboard.php" class="btn btn-outline btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>