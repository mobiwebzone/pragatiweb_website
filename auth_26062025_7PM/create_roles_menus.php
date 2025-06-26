<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['type']) && $_POST['type'] === 'assign') {
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
    $menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;

    if ($role_id <= 0 || $menu_id <= 0) {
        echo "Invalid role or menu selected.";
        exit;
    }

    // Check if already assigned
    $check = $conn->prepare("SELECT 1 FROM roles_menus WHERE role_id = ? AND menu_id = ?");
    if (!$check) {
        echo "Error in preparing check statement: " . $conn->error;
        exit;
    }

    $check->bind_param("ii", $role_id, $menu_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result && $check_result->num_rows > 0) {
        echo "This menu is already assigned to the selected role.";
        $check->close();
        exit;
    }
    $check->close();

    // Insert new assignment
    $stmt = $conn->prepare("INSERT INTO roles_menus (role_id, menu_id) VALUES (?, ?)");
    if (!$stmt) {
        echo "Error in preparing insert statement: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ii", $role_id, $menu_id);

    if ($stmt->execute()) {
        echo "Menu assigned to role successfully.";
    } else {
        echo "Error assigning menu to role: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
