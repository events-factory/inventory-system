<?php
require 'session_check.php';

if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$sub_category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch current subcategory
$subcat_sql = "
    SELECT s.*, c.category_name 
    FROM subcategory s
    JOIN category c ON s.category_id = c.category_id
    WHERE s.sub_category_id = $sub_category_id";
$result = $conn->query($subcat_sql);
$subcat = $result->fetch_assoc();

// Fetch all categories for dropdown
$cat_sql = "SELECT * FROM category";
$categories = $conn->query($cat_sql);

$feedback = "";
$feedback_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['subCategoryName']);
    $category_id = intval($_POST['category']);

    $update_sql = "
        UPDATE subcategory
        SET sub_category_name = '$name', category_id = $category_id
        WHERE sub_category_id = $sub_category_id";

    if ($conn->query($update_sql) === TRUE) {
        $feedback = "Subcategory updated successfully!";
        $feedback_class = "success";
    } else {
        $feedback = "Error updating subcategory: " . $conn->error;
        $feedback_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Subcategory</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>

        <!-- Sidebar (common) -->
    <div class="sidebar">
      <img src="red_logo.png" alt="Logo" class="logo" />
      <div class="nav-links">
        <a href="dashboard.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Dashboard</span>
          </div>
        </a>
        <a href="inventory.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Inventory</span>
          </div>
        </a>
        <a href="reports.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Reports</span>
          </div>
        </a>
        <a href="requisitions.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Requisitions</span>
          </div>
        </a>
        <a href="orders.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Orders</span>
          </div>
        </a>
        <a href="Manage_inventory.php" class="nav-link">
          <div class="nav-item active">
            <i class="icon"></i>
            <span>Manage Store</span>
          </div>
        </a>
        <a href="settings.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Settings</span>
          </div>
        </a>

                <a href="logout.php" class="nav-link">
          <div class="nav-item">
            <i class="icon"></i>
            <span>Log Out</span>
          </div>
        </a>
      </div>
    </div>

    <div class="main-content">
        <div class="form-card">
            <h2>Update Subcategory</h2>

            <?php if ($feedback): ?>
                <div class="feedback_<?= $feedback_class; ?>">
                    <?= $feedback; ?>
                </div>
            <?php endif; ?>

            <form action="update_sub.php?id=<?= $sub_category_id ?>" method="post">
                <div class="form-group">
                    <label for="subCategoryName">Subcategory Name</label>
                    <input type="text" id="subCategoryName" name="subCategoryName" value="<?= $subcat['sub_category_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="<?= $subcat['category_id'] ?>"><?= $subcat['category_name'] ?></option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <?php if ($cat['category_id'] != $subcat['category_id']): ?>
                                <option value="<?= $cat['category_id'] ?>"><?= $cat['category_name'] ?></option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="button-container">
                    <a href="Manage_Inventory.php">
                        <button type="button" class="button-secondary">Discard</button>
                    </a>
                    <button type="submit" class="button-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
