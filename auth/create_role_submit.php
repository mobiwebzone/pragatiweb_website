// Contents of create_role_submit.php
<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role_name = trim($_POST['role_name'] ?? '');

    if (empty($role_name)) {
        echo "invalid";
        exit;
    }

    // Check if role already exists
    $check = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
    $check->bind_param("s", $role_name);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        echo "exists";
        exit;
    }

    // Insert new role
    $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
    $stmt->bind_param("s", $role_name);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
