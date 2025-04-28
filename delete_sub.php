<?php
require 'session_check.php';

// Only allow Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$sub_category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch subcategory and parent category name
$subcat_sql = "
    SELECT subcategory.sub_category_name, category.category_name
    FROM subcategory
    JOIN category ON subcategory.category_id = category.category_id
    WHERE subcategory.sub_category_id = $sub_category_id";
$subcat_result = $conn->query($subcat_sql);
$subcat = $subcat_result->fetch_assoc();

// Count groups under this subcategory
$group_count_sql = "SELECT COUNT(*) as group_count FROM itemgroup WHERE sub_category_id = $sub_category_id";
$group_count = $conn->query($group_count_sql)->fetch_assoc();

// Count total items under this subcategory
$item_count_sql = "
    SELECT COUNT(*) as item_count 
    FROM item 
    WHERE group_id IN (SELECT group_id FROM itemgroup WHERE sub_category_id = $sub_category_id)";
$item_count = $conn->query($item_count_sql)->fetch_assoc();

// Fetch all groups under this subcategory
$groups_sql = "
    SELECT group_id, group_name, created_at, updated_at 
    FROM itemgroup 
    WHERE sub_category_id = $sub_category_id";
$groups_result = $conn->query($groups_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Delete Subcategory</title>
  <link rel="stylesheet" href="styles.css"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="navbar">
      <input type="text" class="search-bar" id="searchBar" placeholder="Search active table...">
    </div>

    <div class="content-card">
      <div class="card-header">
        <h1 id="itemTitle">DELETE SUB CATEGORY</h1>
        <div class="header-buttons">
          <a href="Manage_Inventory.php">
            <button class="edit-button">Back</button>
          </a>
          <button id="deleteButton" class="edit-button">Delete</button>
        </div>
      </div>

      <div class="content-links">
        <a href="#" id="link1" class="inventory-link">Details</a>
        <a href="#" id="link2" class="inventory-link">Groups</a>
      </div>

      <hr/>

      <!-- Table 1: Subcategory Details -->
      <div class="table-container" id="table1">
        <div class="row">
          <div class="">
                <p class="details_titles">Subcategory Details</p>
                <div class="row">
                    <p class="details_subject" style="margin-right: 10px">Category:</p>
                    <p class="details_answer"><?= $subcat['category_name'] ?></p>
                </div>                
                <div class="row">
                    <p class="details_subject" style="margin-right: 10px">Subcategory:</p>
                    <p class="details_answer"><?= $subcat['sub_category_name'] ?></p>
                </div>
          </div>
          <div class="">
                <p class="details_titles">Extra Details</p>
                <div class="row">
                    <p class="details_subject" style="margin-right: 10px">Total Groups:</p>
                    <p class="details_answer"><?= $group_count['group_count'] ?></p>
                </div>
                <div class="row">
                    <p class="details_subject" style="margin-right: 10px">Total Items:</p>
                    <p class="details_answer"><?= $item_count['item_count'] ?></p>
                </div>
          </div>          
        </div>
      </div>

      <!-- Table 2: Groups in Subcategory -->
      <div class="table-container" id="table2">
        <div class="table-header">
          <span>Groups</span>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Group Name</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($group = $groups_result->fetch_assoc()) { ?>
              <tr onclick="location.href='group_details.php?id=<?= $group['group_id'] ?>&group_name=<?= urlencode($group['group_name']) ?>'">
                <td><?= $group['group_id'] ?></td>
                <td><?= $group['group_name'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div id="table2Pagination" class="pagination"></div>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
          // Ensure the button exists before attaching event
          let deleteBtn = document.getElementById("deleteButton");
          if (deleteBtn) {
              deleteBtn.addEventListener("click", function () {
                  Swal.fire({
                    title: "Are you sure?",
                    text: "This Group and it's items will be permanently deleted!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Delete",
                    cancelButtonText: "Discard",
                    confirmButtonColor: "#FF4444",  // Red
                    cancelButtonColor: "#ffffff",  // White
                    customClass: {
                        popup: "custom-popup",
                        title: "custom-title",
                        htmlContainer: "custom-message",
                        confirmButton: "custom-confirm",
                        cancelButton: "custom-cancel"
                    }
                  }).then((result) => {
                      if (result.isConfirmed) {
                          // Fake sub_category ID for now (replace dynamically)
                          let sub_categoryId = <?= $sub_category_id ?>;  

                          // Send AJAX request
                          fetch("delete_sub_logic.php", {
                              method: "POST",
                              body: JSON.stringify({ sub_category_id: sub_categoryId }),
                              headers: { "Content-Type": "application/json" }
                          })
                          .then(response => response.json())
                          .then(data => {
                              if (data.success) {
                                  Swal.fire({
                                      title: "Deleted!",
                                      text: "The Sub_category has been removed.",
                                      icon: "success",
                                      timer: 3000,
                                      showConfirmButton: false
                                  });
                                  setTimeout(() => {
                                      window.location.href = "Manage_inventory.php";
                                  }, 3000);
                              } else {
                                  Swal.fire({
                                      title: "Error",
                                      text: "Something went wrong. Try again.",
                                      icon: "error"
                                  });
                              }
                          })
                          .catch(error => {
                              Swal.fire({
                                  title: "Error",
                                  text: "Could not connect to the server.",
                                  icon: "error"
                              });
                          });
                      }
                  });
              });
          } else {
              console.error("Delete button not found!");
          }
      });
    </script>
    <script>
        const table2 = document.getElementById('table2');     
        setupPagination(table2, document.getElementById('table2Pagination'));     
    </script>   
</body>
</html>
