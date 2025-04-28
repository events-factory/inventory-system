<?php
require 'session_check.php';

// Only allow Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch category name
$category_sql = "SELECT category_name FROM category WHERE category_id = $category_id";
$category_result = $conn->query($category_sql);
$category = $category_result->fetch_assoc();

// Count subcategories under this category
$subcat_count_sql = "SELECT COUNT(*) AS subcategory_count FROM subcategory WHERE category_id = $category_id";
$subcat_count = $conn->query($subcat_count_sql)->fetch_assoc();

// Count groups under this category
$group_count_sql = "
    SELECT COUNT(*) AS group_count 
    FROM itemgroup 
    WHERE sub_category_id IN (
        SELECT sub_category_id FROM subcategory WHERE category_id = $category_id
    )";
$group_count = $conn->query($group_count_sql)->fetch_assoc();

// Count items under this category
$item_count_sql = "
    SELECT COUNT(*) AS item_count 
    FROM item 
    WHERE group_id IN (
        SELECT group_id FROM itemgroup 
        WHERE sub_category_id IN (
            SELECT sub_category_id FROM subcategory WHERE category_id = $category_id
        )
    )";
$item_count = $conn->query($item_count_sql)->fetch_assoc();

// Get list of subcategories in this category
$subcategories_sql = "
    SELECT sub_category_id, sub_category_name, created_at, updated_at 
    FROM subcategory 
    WHERE category_id = $category_id";
$subcategories_result = $conn->query($subcategories_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Delete Category</title>
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
        <h1 id="itemTitle">DELETE CATEGORY</h1>
        <div class="header-buttons">
          <a href="Manage_Inventory.php">
            <button class="edit-button">Back</button>
          </a>
          <button id="deleteButton" class="edit-button">Delete</button>
        </div>
      </div>

      <div class="content-links">
        <a href="#" id="link1" class="inventory-link">Details</a>
        <a href="#" id="link2" class="inventory-link">Subcategories</a>
      </div>

      <hr/>

      <!-- Table 1: Category Details -->
      <div class="table-container" id="table1">
        <div class="row">
          <div class="">
            <p class="details_titles">Category Details</p>
            <div class="row">
              <p class="details_subject" style="margin-right: 10px">Category:</p>
              <p class="details_answer"><?= $category['category_name'] ?></p>
            </div>
            <div class="row">
              <p class="details_subject" style="margin-right: 10px">Total Subcategories:</p>
              <p class="details_answer"><?= $subcat_count['subcategory_count'] ?></p>
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

      <!-- Table 2: Subcategories under Category -->
      <div class="table-container" id="table2">
        <div class="table-header">
          <span>Subcategories</span>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Subcategory Name</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($subcat = $subcategories_result->fetch_assoc()) { ?>
              <tr>
                <td><?= $subcat['sub_category_id'] ?></td>
                <td><?= $subcat['sub_category_name'] ?></td>
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
                          let categoryId = <?= $category_id ?>;  

                          // Send AJAX request
                          fetch("delete_category_logic.php", {
                              method: "POST",
                              body: JSON.stringify({ category_id: categoryId }),
                              headers: { "Content-Type": "application/json" }
                          })
                          .then(response => response.json())
                          .then(data => {
                              if (data.success) {
                                  Swal.fire({
                                      title: "Deleted!",
                                      text: "The Category has been removed.",
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
