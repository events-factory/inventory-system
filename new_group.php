<?php

require 'session_check.php';

// Only allow Operator & Super Admin
if ($user_role != 1 && $user_role != 2) {
    echo "❌ Access Denied!";
    exit;
}

// Include the database connection file
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $groupName = $_POST['groupName'];
    $itemCategory = $_POST['itemCategory'];
    $itemSubCategory = $_POST['itemSubCategory'];

    // Insert the new group into the database
    $sql = "INSERT INTO itemgroup (group_name, sub_category_id) 
            VALUES ('$groupName', '$itemSubCategory')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to inventory or show success message
        $feedback = "group created successfully.";
        $feedback_class = "success";  
    } else {
        // Handle error
        // echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
        $feedback = "Error creating group: " . $sql . "<br>" . $conn->error;
        $feedback_class = "error";            
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New group</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
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
          <div class="nav-item">
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
            <h2>New group</h2>

            <!-- Feedback Message -->
            <?php if (isset($feedback)): ?>
              <div class="feedback_<?= $feedback_class; ?>">
                  <?= $feedback; ?>
              </div>
            <?php endif; ?>  

            <form action="new_group.php" method="post">
                <div class="form-group">
                    <label for="groupName">group Name</label>
                    <input type="text" id="groupName" name="groupName">
                </div>

                <div class="form-group">
                  <label for="itemCategory">Category</label>
                  <select id="itemCategory" name="itemCategory">
                      <option value=""></option>                    
                      <!-- Categories will be populated here from PHP -->
                  </select>
                </div>
  
                <div class="form-group">
                    <label for="itemSubCategory">SubCategory</label>
                    <select id="itemSubCategory" name="itemSubCategory">
                        <option value=""></option>
                        <!-- Subcategories will be populated dynamically -->
                    </select>
                </div>
                
             
                  <select id="itemGroup" name="itemGroup" hidden>
                      <option value=""></option>
                      <!-- Groups will be populated dynamically -->
                  </select>
                   
 

                <div class="button-container">
                    <button type="submit" class="button-primary">Create</button>
                </div>
            </form>
    </div>
    <script src="dynamic-dropdowns.js"></script>
  </body>
</html>
