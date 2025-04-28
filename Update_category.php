<?php
require 'session_check.php';

if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch category
$category_sql = "SELECT * FROM category WHERE category_id = $category_id";
$result = $conn->query($category_sql);
$category = $result->fetch_assoc();

$feedback = "";
$feedback_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['categoryName']);

    $update_sql = "UPDATE category SET category_name = '$name' WHERE category_id = $category_id";

    if ($conn->query($update_sql) === TRUE) {
        $feedback = "Category updated successfully!";
        $feedback_class = "success";
    } else {
        $feedback = "Error updating category: " . $conn->error;
        $feedback_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Category</title>
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
            <h2>Update Category</h2>

            <?php if ($feedback): ?>
                <div class="feedback_<?= $feedback_class; ?>">
                    <?= $feedback; ?>
                </div>
            <?php endif; ?>

            <form action="update_category.php?id=<?= $category_id ?>" method="post">
                <div class="form-group">
                    <label for="categoryName">Category Name</label>
                    <input type="text" id="categoryName" name="categoryName" value="<?= $category['category_name'] ?>" required>
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
