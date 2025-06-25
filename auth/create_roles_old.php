<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role_name = trim($_POST['role_name'] ?? '');

    if (empty($role_name)) {
        echo "Role name is required.";
        exit;
    }

    $check = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
    $check->bind_param("s", $role_name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "Role already exists.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
    $stmt->bind_param("s", $role_name);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error while creating role.";
    }

    $stmt->close();
    $check->close();
    $conn->close();
}
?>
