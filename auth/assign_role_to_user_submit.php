<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? '';
    $role_id = $_POST["role_id"] ?? '';

    if (empty($user_id) || empty($role_id)) {
        echo "User or Role not selected.";
        exit;
    }

    // Check for existing assignment
    $check = $conn->prepare("SELECT 1 FROM users_roles WHERE user_id = ? AND role_id = ?");
    $check->bind_param("ii", $user_id, $role_id);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        echo "Role already assigned to this user.";
        exit;
    }

    // Insert role assignment
    $stmt = $conn->prepare("INSERT INTO users_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $role_id);

    if ($stmt->execute()) {
        echo "Role assigned successfully.";
    } else {
        echo "Error assigning role.";
    }

    $stmt->close();
    $conn->close();
}
?>
