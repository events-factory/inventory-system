<?php
require 'session_check.php';

// Only allow Operator & Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}
// Include the database connection file
include 'db.php';

// Get the group ID from the URL parameter
$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch group details including category and sub-category
$group_sql = "
    SELECT 
        itemgroup.group_name, 
        itemgroup.created_at, 
        itemgroup.updated_at,
        subcategory.sub_category_name,
        category.category_name
    FROM itemgroup
    JOIN subcategory ON itemgroup.sub_category_id = subcategory.sub_category_id
    JOIN category ON subcategory.category_id = category.category_id
    WHERE itemgroup.group_id = $group_id";
$group_result = $conn->query($group_sql);
$group = $group_result->fetch_assoc();

// Fetch all items belonging to the group
$items_sql = "SELECT item_id, item_name, availability, model, serial_number FROM item WHERE group_id = $group_id";
$items_result = $conn->query($items_sql);

// Fetch counts for rented, damaged, and available items
$counts_sql = "
    SELECT 
        SUM(CASE WHEN availability = 'Rented' THEN 1 ELSE 0 END) AS rented_items,
        SUM(CASE WHEN availability = 'Damaged' THEN 1 ELSE 0 END) AS damaged_items,
        SUM(CASE WHEN availability = 'Available' THEN 1 ELSE 0 END) AS available_items,
        COUNT(*) AS total_items
    FROM item WHERE group_id = $group_id";
$counts_result = $conn->query($counts_sql);
$counts = $counts_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Delete Item</title>
    <link rel="stylesheet" href="styles.css" />

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

    <!-- Main content -->
    <div class="main-content">
      <!-- Navbar (common) -->
      <div class="navbar">
      <input type="text" class="search-bar" id="searchBar" placeholder="Search active table...">
      </div>

      <!-- White content card -->
      <div class="content-card">
        <!-- Title and buttons -->
        <div class="card-header">
          <h1 id="itemTitle">DELETE GROUP</h1>
          <div class="header-buttons">
            <a href="Manage_Inventory.php" >
              <button class="edit-button">Back</button>
            </a>            
            <button id="deleteButton" class="edit-button">Delete</button>
          </div>
        </div>

        <!-- Links for changing content (tabs) -->
        <div class="content-links">
          <a href="#" id="link1" class="inventory-link">Details</a>
          <a href="#" id="link2" class="inventory-link">Items</a>
        </div>

        <hr />

            <!-- Table 1: Group Details -->
            <div class="table-container" id="table1">
            <div class="row">
                <div class="">
                    <p class="details_titles">Group Details</p>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Total Items:</p>
                        <p class="details_answer"><?= $counts['total_items'] ?></p>
                    </div>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Rented Items:</p>
                        <p class="details_answer"><?= $counts['rented_items'] ?></p>
                    </div>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Damaged Items:</p>
                        <p class="details_answer"><?= $counts['damaged_items'] ?></p>
                    </div>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Available Items:</p>
                        <p class="details_answer"><?= $counts['available_items'] ?></p>
                    </div>
                </div>

                <div class="">
                    <p class="details_titles">Extra Group Details</p>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Category:</p>
                        <p class="details_answer"><?= $group['category_name'] ?></p>
                    </div>
                    <div class="row">
                        <p class="details_subject" style="margin-right: 10px">Sub-Category:</p>
                        <p class="details_answer"><?= $group['sub_category_name'] ?></p>
                    </div>
                </div>
            </div>
            </div>

            <!-- Table 2: Items in the Group -->
            <div class="table-container" id="table2">
            <div class="table-header">
                <span>Items</span>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>availability</th>
                    <th>Model</th>
                    <th>Serial Number</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($item = $items_result->fetch_assoc()) { ?>
                <tr
                    onclick="location.href='item_details.php?id=<?= $item['item_id'] ?>&item_name=<?= urlencode($item['item_name']) ?>'">
                    <td><?= $item['item_id'] ?></td>
                    <td><?= $item['item_name'] ?></td>
                    <td><?= $item['availability'] ?></td>
                    <td><?= $item['model'] ?></td>
                    <td><?= $item['serial_number'] ?></td>
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
                          // Fake group ID for now (replace dynamically)
                          let groupId = <?= $group_id ?>;  

                          // Send AJAX request
                          fetch("delete_group_logic.php", {
                              method: "POST",
                              body: JSON.stringify({ group_id: groupId }),
                              headers: { "Content-Type": "application/json" }
                          })
                          .then(response => response.json())
                          .then(data => {
                              if (data.success) {
                                  Swal.fire({
                                      title: "Deleted!",
                                      text: "The Group has been removed.",
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
