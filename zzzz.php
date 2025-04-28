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

// Fetch item details
$itemId = $_GET['id'];
$sql = "SELECT * FROM item WHERE item_id = '$itemId'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    echo "Item not found.";
    exit;
}

$item = $result->fetch_assoc();

// Handle form submission
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

    // Update the item in the database
    $sql = "UPDATE item 
            SET item_name = '$itemName', 
                category_id = '$itemCategory',
                sub_category_id = '$itemSubCategory',
                group_id = '$itemGroup',
                quantity = '$itemQuantity',
                unit = '$itemUnit',
                model = '$itemModel',
                serial_number = '$itemSerialNumber',
                flight_case_number = '$itemFlightCaseNumber',
                remarks = '$remarks',
                image_path = '$imagePath'
            WHERE item_id = '$itemId'";

    if ($conn->query($sql) === TRUE) {
        $feedback = "Item updated successfully.";
        $feedback_class = "success";  
        // Refresh item info
        $item = array_merge($item, $_POST);
        $item['image_path'] = $imagePath;
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="main-content">
    <div class="form-card">
        <h2>Edit Item</h2>

        <!-- Feedback Message -->
        <?php if ($feedback): ?>
            <div class="feedback_<?= $feedback_class; ?>">
                <?= $feedback; ?>
            </div>
        <?php endif; ?>

        <form action="zzzz.php?id=<?= $itemId ?>" method="post" enctype="multipart/form-data">
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
                <select id="itemCategory" name="itemCategory" required>
                    <option value=""></option>
                    <!-- Populate dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label for="itemSubCategory">Sub Category</label>
                <select id="itemSubCategory" name="itemSubCategory" required>
                    <option value=""></option>
                    <!-- Populate dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label for="itemGroup">Group</label>
                <select id="itemGroup" name="itemGroup" required>
                    <option value=""></option>
                    <!-- Populate dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label for="itemModel">Model</label>
                <input type="text" id="itemModel" name="itemModel" value="<?= htmlspecialchars($item['model']) ?>">
            </div>

            <div class="form-group">
                <label for="itemSerialNumber">Serial Number</label>
                <input type="text" id="itemSerialNumber" name="itemSerialNumber" value="<?= htmlspecialchars($item['serial_number']) ?>">
            </div>

            <div class="form-group">
                <label for="itemUnit">Unit</label>
                <select id="itemUnit" name="itemUnit">
                    <option value=""></option>
                    <option value="Sqm" <?= ($item['unit'] == 'Sqm') ? 'selected' : '' ?>>Sqm</option>
                    <option value="M" <?= ($item['unit'] == 'M') ? 'selected' : '' ?>>M</option>
                    <option value="Kg" <?= ($item['unit'] == 'Kg') ? 'selected' : '' ?>>Kg</option>
                    <option value="L" <?= ($item['unit'] == 'L') ? 'selected' : '' ?>>L</option>
                    <option value="PC" <?= ($item['unit'] == 'PC') ? 'selected' : '' ?>>PC</option>
                    <option value="Cartons" <?= ($item['unit'] == 'Cartons') ? 'selected' : '' ?>>Cartons</option>
                </select>
            </div>

            <div class="form-group">
                <label for="itemQuantity">Quantity</label>
                <input type="number" id="itemQuantity" name="itemQuantity" value="<?= htmlspecialchars($item['quantity']) ?>">
            </div>

            <div class="form-group">
                <label for="itemFlightCaseNumber">Flight Case Number</label>
                <input type="text" id="itemFlightCaseNumber" name="itemFlightCaseNumber" value="<?= htmlspecialchars($item['flight_case_number']) ?>">
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks"><?= htmlspecialchars($item['remarks']) ?></textarea>
            </div>

            <div class="button-container">
                <a href="inventory.php">
                    <button type="button" class="button-secondary">Cancel</button>
                </a>
                <button type="submit" class="button-primary">Update Item</button>
            </div>
        </form>
    </div>
</div>

<script src="dynamic-dropdowns.js"></script>
</body>
</html>
