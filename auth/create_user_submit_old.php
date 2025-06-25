<?php
require_once "conn_db.php";

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role_id = intval($_POST['role_id'] ?? 0);

if (empty($username) || empty($email) || empty($password) || $role_id === 0) {
    echo "All fields are required.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$res = $check->get_result();
if ($res->num_rows > 0) {
    echo "Username or email already exists.";
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $username, $email, $hash, $role_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error creating user.";
}

$stmt->close();
$conn->close();
?>
