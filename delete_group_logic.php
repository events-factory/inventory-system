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
    $group_id = $data["group_id"];

    if (!$group_id) {
        echo json_encode(["success" => false]);
        exit;
    }

    // Delete items 
    $deleteitems = $conn->prepare("DELETE FROM item WHERE group_id = ?");
    $deleteitems->bind_param("i", $group_id);
    $deleteSuccess = $deleteitems->execute();

    // Delete group 
    $deletegroup = $conn->prepare("DELETE FROM itemgroup WHERE group_id = ?");
    $deletegroup->bind_param("i", $group_id);
    $deleteSuccess = $deletegroup->execute();

    echo json_encode(["success" => $deleteSuccess]);
}
?>
