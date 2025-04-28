<?php
require 'session_check.php';

// Only allow Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php'; // Your database connection file

    $data = json_decode(file_get_contents("php://input"), true);
    $sub_category_id = $data["sub_category_id"];

    if (!$sub_category_id) {
        echo json_encode(["success" => false]);
        exit;
    }

    // Delete items 
    $deleteitems = $conn->prepare("DELETE FROM item WHERE sub_category_id = ?");
    $deleteitems->bind_param("i", $sub_category_id);
    $deleteSuccess = $deleteitems->execute();

    // Delete group 
    $deletegroup = $conn->prepare("DELETE FROM itemgroup WHERE sub_category_id = ?");
    $deletegroup->bind_param("i", $sub_category_id);
    $deleteSuccess = $deletegroup->execute();

    // Delete sub category 
    $deletesub_category = $conn->prepare("DELETE FROM subcategory WHERE sub_category_id = ?");
    $deletesub_category->bind_param("i", $sub_category_id);
    $deleteSuccess = $deletesub_category->execute();

    echo json_encode(["success" => $deleteSuccess]);
}
?>
