<?php
require 'session_check.php';

// Only allow Operator & Super Admin
if ($user_role != 1 && $user_role != 2) {
    echo "❌ Access Denied!";
    exit;
}

include 'db.php';

$categories_result = $conn->query("SELECT category_id, category_name FROM category");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory Management</title>
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
      <div class="Page_with_navbar">
        
      <div class="inventory-card">
                <div class="link-container">
                    <a href="#" id="link1" class="inventory-link active-link">Items</a>
                    <a href="#" id="link2" class="inventory-link">Groups</a>
                    <a href="#" id="link3" class="inventory-link">Sub categories</a>
                    <a href="#" id="link4" class="inventory-link">Categories</a>
                    <div class="line"></div>
                </div>

                <!-- Items Table -->
                <div class="table-container" id="table1" >
                    <div class="table-header">
                        <span>Items</span>
                        <div class="header-buttons">
                            <div class="filter-section">
                                <select id="categoryFilter2">
                                    <option value="">Select Category</option>
                                    <?php
                                    $categories_result->data_seek(0);
                                    while ($row = $categories_result->fetch_assoc()) { ?>
                                        <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <select id="subcategoryFilter2" disabled>
                                    <option value="">Select Subcategory</option>
                                </select>
                                <select id="groupFilter2" disabled>
                                    <option value="">Select Group</option>
                                </select>
                            </div>
                            <a href="new_item.php" class="edit-button"> Add </a>
                            <!-- <button class="download-button">Download</button>                               -->
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Availability</th>
                                <th>Update</th>
                                <th>Delete</th>                                
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="table1Pagination" class="pagination"></div>                    
                </div>


                <!-- Groups Table -->             
                <div class="table-container" id="table2">
                    <div class="table-header">
                        <span>Groups</span>
                        <div class="header-buttons">
                            <div class="filter-section">
                                <select id="groupCategoryFilter">
                                    <option value="">Select Category</option>
                                    <?php
                                    $categories_result->data_seek(0); // Reset the pointer
                                    while ($row = $categories_result->fetch_assoc()) { ?>
                                        <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
                                    <?php } ?>
                                </select>

                                <select id="groupSubcategoryFilter" disabled>
                                    <option value="">Select Subcategory</option>
                                </select>                             
                            </div>

                            <a href="new_group.php" class="edit-button">Add</a>                            
                            <!-- <button class="download-button">Download</button>                               -->
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Group</th>
                                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>                                   
                                <th>Update</th>
                                <th>Delete</th>   
                                <?php endif; ?>   
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="table2Pagination" class="pagination"></div>
                </div>                
  


                <!-- Subcategory Table -->
                <div class="table-container" id="table3">
                    <div class="table-header">
                        <span>Subcategories</span>
                        <div class="header-buttons">
                            <div class="filter-section">
                                <select id="subCategoryCategoryFilter">
                                    <option value="">Select Category</option>
                                    <?php
                                    $categories_result->data_seek(0); // Reset pointer again
                                    while ($row = $categories_result->fetch_assoc()) { ?>
                                        <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <a href="new_subcategory.php" class="edit-button">Add</a>
                            <!-- <button class="download-button">Download</button> -->
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subcategory Name</th>
                                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>                                   
                                <th>Update</th>
                                <th>Delete</th>   
                                <?php endif; ?>   
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="table3Pagination" class="pagination"></div>
                </div>
           


                <!-- Category Table -->
                <div class="table-container" id="table4">
                    <div class="table-header">
                        <span>Categories</span>
                        <div class="header-buttons">
                            <a href="new_category.php" class="edit-button">Add</a>
                            <!-- <button class="download-button">Download</button> -->
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>                                   
                                <th>Update</th>
                                <th>Delete</th>   
                                <?php endif; ?>                

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $categories_result->data_seek(0);
                            while ($row = $categories_result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['category_id']}</td>
                                        <td>{$row['category_name']}</td>";

                                    if ($user_role == 1) {
                                        echo "<td><a href='Update_category.php?id={$row['category_id']}' class='btn'><button type='button'>Update</button></a></td>
                                            <td><a href='Delete_category.php?id={$row['category_id']}' class='btn'><button type='button'>Delete</button></a></td>";
                                    } else {
                                        echo "<td></td><td></td>";
                                    }

                                    echo "</tr>";
                            }
                            ?>
                        </tbody>

                    </table>
                    <div id="table4Pagination" class="pagination"></div>
                </div>
          


            </div>
      </div>
    </div>
    <script>
      const USER_ROLE = <?= $user_role ?>;
    </script>
    <script src="script.js"></script>
    <script src="script2.js"></script>
    <script>
        const table1 = document.getElementById('table1');
        const table2 = document.getElementById('table2');
        const table3 = document.getElementById('table3');
        const table4 = document.getElementById('table4');        

        setupPagination(table1, document.getElementById('table1Pagination'));
        setupPagination(table2, document.getElementById('table2Pagination'));
        setupPagination(table3, document.getElementById('table3Pagination'));
        setupPagination(table4, document.getElementById('table4Pagination'));        
    </script> 
</body>
</html>
