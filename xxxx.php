<?php

require 'session_check.php';

// Only allow Operator & Super Admin
if ($user_role != 1 && $user_role != 2) {
    echo "❌ Access Denied!";
    exit;
}

// Include the database connection file
include 'db.php';

// Get the item ID from the URL parameter
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch item details along with category, sub-category, group, and current event (if rented)
$item_sql = "
    SELECT 
        item.*, 
        category.category_name, 
        subcategory.sub_category_name, 
        itemgroup.group_name, 
        events.event_name AS current_event_name
    FROM item
    JOIN category ON item.category_id = category.category_id
    JOIN subcategory ON item.sub_category_id = subcategory.sub_category_id
    JOIN itemgroup ON item.group_id = itemgroup.group_id
    LEFT JOIN events ON item.event_id = events.event_id
    WHERE item.item_id = $item_id";
$item_result = $conn->query($item_sql);
$item = $item_result->fetch_assoc();


// Initialize variables for feedback
$feedback = "";
$feedback_class = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imagePath = $item['image_path']; // Default to current image

    // If a new image was uploaded
    if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['itemImage']['tmp_name'];
        $imageName = basename($_FILES['itemImage']['name']);
        $targetPath = $uploadDir . time() . '_' . $imageName;

        if (move_uploaded_file($imageTmp, $targetPath)) {
            // Delete the old image if exists
            if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                unlink($item['image_path']);
            }
            $imagePath = $targetPath;
        } else {
            $feedback = "Image upload failed.";
            $feedback_class = "error";
        }
    }

    // Get form inputs
    $item_name = $conn->real_escape_string($_POST['itemName']);
    $category_id = intval($_POST['itemCategory']);
    $sub_category_id = intval($_POST['itemSubCategory']);
    $group_id = intval($_POST['itemGroup']);
    $model = $conn->real_escape_string($_POST['itemModel']);
    $serial_number = $conn->real_escape_string($_POST['itemSerialNumber']);
    $unit = $conn->real_escape_string($_POST['itemUnit']);
    $availability = $conn->real_escape_string($_POST['itemAvailability']);
    $quantity = intval($_POST['itemQuantity']);
    $flight_case_number = $conn->real_escape_string($_POST['itemFlightCaseNumber']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // Update query
    $update_sql = "
        UPDATE item 
        SET 
            item_name = '$item_name',
            category_id = $category_id,
            sub_category_id = $sub_category_id,
            group_id = $group_id,
            model = '$model',
            serial_number = '$serial_number',
            unit = '$unit',
            availability = '$availability',
            quantity = $quantity,
            flight_case_number = '$flight_case_number',
            remarks = '$remarks',
            image_path = '$imagePath'            
        WHERE item_id = $item_id
    ";

    if ($conn->query($update_sql) === TRUE) {
        $feedback = "Item updated successfully!";
        $feedback_class = "success";

        // Refresh item info
        $item = array_merge($item, $_POST);
        $item['image_path'] = $imagePath;
        $item_result = $conn->query($item_sql); // you already have $item_sql prepared
        $item = $item_result->fetch_assoc();        

    } else {
        $feedback = "Error updating item: " . $conn->error;
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
    <title>Update Item</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>


    <div class="main-content">
        <div class="form-card">
            <h2>Update Item</h2>

                        <!-- Feedback Message -->
            <?php if ($feedback): ?>
                <div class="feedback_<?= $feedback_class; ?>">
                    <?= $feedback; ?>
                </div>
            <?php endif; ?>
            <form action="xxxx.php?id=<?= $item_id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="itemName">Item Name</label>
                    <input type="text" id="itemName" name="itemName" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                </div>

                <!-- Image upload field -->
                <div class="form-group">
                    <label>Current Image:</label><br>
                    <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                        <img src="<?= $item['image_path'] ?>" alt="Item Image" style="max-width:150px; margin-bottom:10px;"><br>
                    <?php else: ?>
                        No image uploaded.
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="itemImage">Upload New Image (optional)</label>
                    <input type="file" id="itemImage" name="itemImage" accept="image/*">
                </div>

                <!-- Rest of your fields -->                

                <div class="form-group">
                  <label for="itemCategory">Category</label>
                  <select id="itemCategory" name="itemCategory">
                    <option value=<?= $item['category_id'] ?>><?= $item['category_name'] ?></option>                    
                      <!-- Categories will be populated here from PHP -->
                  </select>
              </div>
              <div class="form-group">
                  <label for="itemSubCategory">Sub Category</label>
                  <select id="itemSubCategory" name="itemSubCategory">
                  <option value=<?= $item['sub_category_id'] ?>><?= $item['sub_category_name'] ?></option>                    
                      <!-- Subcategories will be populated dynamically -->
                  </select>
              </div>    
              <div class="form-group">
                  <label for="itemGroup">Group</label>
                  <select id="itemGroup" name="itemGroup">
                      <option value=<?= $item['group_id'] ?>><?= $item['group_name'] ?></option>
                      <!-- Groups will be populated dynamically -->
                  </select>
              </div>        
                <div class="form-group">
                  <label for="itemModel">Model</label>
                  <input type="text" id="itemModel" name="itemModel" value=<?= $item['model'] ?>>
                </div>   
                <div class="form-group">
                  <label for="itemSerialNumber">Serial Number</label>
                  <input type="text" id="itemSerialNumber" name="itemSerialNumber" value=<?= $item['serial_number'] ?>>
                </div>  
                <div class="form-group">
                  <label for="itemUnit">Unit</label>
                  <select id="itemUnit" name="itemUnit">
                    <option value= <?= $item['unit'] ?>><?= $item['unit'] ?></option>
                    <option value="Sqm">Sqm</option>
                    <option value="M">M</option>
                    <option value="Kg">Kg</option>
                    <option value="L">L</option>
                    <option value="PC">PC</option>   
                    <option value="Cartons">Cartons</option>                    
                  </select>                  
                </div>         
                <div class="form-group">
                  <label for="itemAvailability">Availability</label>
                  <select id="itemAvailability" name="itemAvailability">
                    <option value=<?= $item['availability'] ?>><?= $item['availability'] ?></option>
                    <option value="Available">Available</option>
                    <option value="Damaged">Damaged</option>      
                    <option value="Rented">Rented</option>
                    <option value="Lost">Lost</option>                                     
                  </select>                  
                </div>                          
                <div class="form-group">
                    <label for="itemQuantity">Quantity</label>
                    <input type="number" id="itemQuantity" name="itemQuantity" value=<?= $item['quantity'] ?>>
                </div>
    
                <div class="form-group">
                  <label for="itemFlightCaseNumber">Flight case number</label>
                  <input type="text" id="itemFlightCaseNumber" name="itemFlightCaseNumber" value=<?= $item['flight_case_number'] ?>>
                </div>                                                        
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks"><?= $item['remarks'] ?></textarea>
                </div>

                <div class="button-container">
                  <a href="Manage_Inventory.php" >
                    <button type="button" class="button-secondary">Discard</button>
                  </a>
                  <button type="submit" class="button-primary">Update</button>
                </div>
            </form>
    </div>
    <script src="dynamic-dropdowns.js"></script>
  </body>
</html>
