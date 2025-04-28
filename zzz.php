<?php
require 'session_check.php';

// Only allow Operator & Super Admin
if ($user_role != 1 && $user_role != 2) {
    echo "❌ Access Denied!";
    exit;
}

// Include the database connection file
include 'db.php';

$feedback = "";
$feedback_class = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // File upload settings
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imagePath = '';
    if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['itemImage']['tmp_name'];
        $imageName = basename($_FILES['itemImage']['name']);
        $targetPath = $uploadDir . time() . '_' . $imageName;

        // Move uploaded file
        if (move_uploaded_file($imageTmp, $targetPath)) {
            $imagePath = $targetPath;
        } else {
            $feedback = "Image upload failed.";
            $feedback_class = "error";
        }
    }

    // Get form values
    $itemName = $_POST['itemName'];
    $itemCategory = $_POST['itemCategory'];
    $itemSubCategory = $_POST['itemSubCategory'];
    $itemGroup = $_POST['itemGroup'];
    $itemModel = $_POST['itemModel'];
    $itemSerialNumber = $_POST['itemSerialNumber'];
    $itemQuantity = $_POST['itemQuantity'];
    $itemUnit = $_POST['itemUnit'];
    $itemFlightCaseNumber = $_POST['itemFlightCaseNumber'];
    $remarks = $_POST['remarks'];

    // Insert the new item into the database
    $sql = "INSERT INTO item (item_name, category_id, sub_category_id, group_id, quantity, unit, model, serial_number, flight_case_number, remarks, availability, image_path) 
            VALUES ('$itemName', '$itemCategory', '$itemSubCategory', '$itemGroup', '$itemQuantity', '$itemUnit', '$itemModel', '$itemSerialNumber', '$itemFlightCaseNumber', '$remarks', 'Available', '$imagePath')";

    if ($conn->query($sql) === TRUE) {
        $feedback = "Item created successfully.";
        $feedback_class = "success";  
    } else {
        $feedback = "Error creating item: " . $sql . "<br>" . $conn->error;
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
    <title>New Item</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="main-content">
        <div class="form-card">
            <h2>New Item</h2>

            <!-- Feedback Message -->
            <?php if ($feedback): ?>
              <div class="feedback_<?= $feedback_class; ?>">
                  <?= $feedback; ?>
              </div>
            <?php endif; ?>  

            <form action="zzz.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="itemName">Item Name</label>
                    <input type="text" id="itemName" name="itemName" required>
                </div>

                <!-- Add the image upload field -->
                <div class="form-group">
                    <label for="itemImage">Item Image</label>
                    <input type="file" id="itemImage" name="itemImage" accept="image/*" required>
                </div>

                <!-- Rest of your form -->
                <div class="form-group">
                  <label for="itemCategory">Category</label>
                  <select id="itemCategory" name="itemCategory" required>
                      <option value=""></option>                    
                      <!-- Populate from DB -->
                  </select>
              </div>
              <div class="form-group">
                  <label for="itemSubCategory">Sub Category</label>
                  <select id="itemSubCategory" name="itemSubCategory" required>
                      <option value=""></option>
                  </select>
              </div>    
              <div class="form-group">
                  <label for="itemGroup">Group</label>
                  <select id="itemGroup" name="itemGroup" required>
                      <option value=""></option>
                  </select>
              </div>        
                <div class="form-group">
                  <label for="itemModel">Model</label>
                  <input type="text" id="itemModel" name="itemModel">
                </div>   
                <div class="form-group">
                  <label for="itemSerialNumber">Serial Number</label>
                  <input type="text" id="itemSerialNumber" name="itemSerialNumber">
                </div>  
                <div class="form-group">
                  <label for="itemUnit">Unit</label>
                  <select id="itemUnit" name="itemUnit">
                    <option value=""></option>
                    <option value="Sqm">Sqm</option>
                    <option value="M">M</option>
                    <option value="Kg">Kg</option>
                    <option value="L">L</option>
                    <option value="PC">PC</option>   
                    <option value="Cartons">Cartons</option>                    
                  </select>                  
                </div>                 
                <div class="form-group">
                    <label for="itemQuantity">Quantity</label>
                    <input type="number" id="itemQuantity" name="itemQuantity">
                </div>
    
                <div class="form-group">
                  <label for="itemFlightCaseNumber">Flight case number</label>
                  <input type="text" id="itemFlightCaseNumber" name="itemFlightCaseNumber">
                </div>                                                        

                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks"></textarea>
                </div>

                <div class="button-container">
                  <a href="inventory.php" >
                    <button type="button" class="button-secondary">Discard</button>
                  </a>
                    <button type="submit" class="button-primary">Add Item</button>
                </div>
            </form>
    </div>
    <script src="dynamic-dropdowns.js"></script>
  </body>
</html>
