<?php

require 'session_check.php';

// Only allow Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch group details with category and subcategory
$group_sql = "
    SELECT itemgroup.*, subcategory.sub_category_id, subcategory.sub_category_name, category.category_id, category.category_name
    FROM itemgroup
    JOIN subcategory ON itemgroup.sub_category_id = subcategory.sub_category_id
    JOIN category ON subcategory.category_id = category.category_id
    WHERE group_id = $group_id
";
$group_result = $conn->query($group_sql);
$group = $group_result->fetch_assoc();

$feedback = "";
$feedback_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $conn->real_escape_string($_POST['groupName']);
    $category_id = intval($_POST['itemCategory']);
    $sub_category_id = intval($_POST['itemSubCategory']);

    $update_sql = "
        UPDATE itemgroup
        SET group_name = '$group_name', sub_category_id = $sub_category_id
        WHERE group_id = $group_id
    ";

    if ($conn->query($update_sql) === TRUE) {
        $feedback = "Group updated successfully!";
        $feedback_class = "success";
    } else {
        $feedback = "Error updating group: " . $conn->error;
        $feedback_class = "error";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Group</title>
    <link rel="stylesheet" href="styles.css">
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
            <h2>Update Group</h2>
            <?php if ($feedback): ?>
                <div class="feedback_<?= $feedback_class ?>">
                    <?= $feedback ?>
                </div>
            <?php endif; ?>

            <form action="update_group.php?id=<?= $group_id ?>" method="post">
                <div class="form-group">
                    <label for="groupName">Group Name</label>
                    <input type="text" id="groupName" name="groupName" value="<?= $group['group_name'] ?>">
                </div>

                <div class="form-group">
                  <label for="itemCategory">Category</label>
                  <select id="itemCategory" name="itemCategory">
                    <option value="<?= $group['category_id'] ?>"><?= $group['category_name'] ?></option>                    
                      <!-- Categories will be populated here from PHP -->
                  </select>
              </div>
              <div class="form-group">
                  <label for="itemSubCategory">Sub Category</label>
                  <select id="itemSubCategory" name="itemSubCategory">
                  <option value="<?= $group['sub_category_id'] ?>"><?= $group['sub_category_name'] ?></option>                    
                      <!-- Subcategories will be populated dynamically -->
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
    <script src="dynamic-dropdowns.js"></script>
</body>
</html>
