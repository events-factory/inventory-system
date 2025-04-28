<?php
require 'session_check.php';

// Only allow Super Admin
if ($user_role != 1) {
    echo "❌ Access Denied!";
    exit;
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $data = json_decode(file_get_contents("php://input"), true);
    $category_id = $data["category_id"];

    if (!$category_id) {
        echo json_encode(["success" => false, "error" => "Missing category_id"]);
        exit;
    }

    // Step 1: Fetch all subcategories under the category
    $sub_query = $conn->prepare("SELECT sub_category_id FROM subcategory WHERE category_id = ?");
    $sub_query->bind_param("i", $category_id);
    $sub_query->execute();
    $sub_result = $sub_query->get_result();

    // Loop through each subcategory
    while ($sub = $sub_result->fetch_assoc()) {
        $sub_category_id = $sub['sub_category_id'];

        // Step 2: Fetch all group_ids under this subcategory
        $group_query = $conn->prepare("SELECT group_id FROM itemgroup WHERE sub_category_id = ?");
        $group_query->bind_param("i", $sub_category_id);
        $group_query->execute();
        $group_result = $group_query->get_result();

        while ($group = $group_result->fetch_assoc()) {
            $group_id = $group['group_id'];

            // Step 3: Delete all items under this group
            $delete_items = $conn->prepare("DELETE FROM item WHERE group_id = ?");
            $delete_items->bind_param("i", $group_id);
            $delete_items->execute();
        }

        // Step 4: Delete all groups under this subcategory
        $delete_groups = $conn->prepare("DELETE FROM itemgroup WHERE sub_category_id = ?");
        $delete_groups->bind_param("i", $sub_category_id);
        $delete_groups->execute();

        // Step 5: Delete the subcategory itself
        $delete_sub = $conn->prepare("DELETE FROM subcategory WHERE sub_category_id = ?");
        $delete_sub->bind_param("i", $sub_category_id);
        $delete_sub->execute();
    }

    // Step 6: Delete the category
    $delete_category = $conn->prepare("DELETE FROM category WHERE category_id = ?");
    $delete_category->bind_param("i", $category_id);
    $deleteSuccess = $delete_category->execute();

    echo json_encode(["success" => $deleteSuccess]);
}
?>
