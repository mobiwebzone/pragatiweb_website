<?php
require_once "conn_db.php";

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($username) || empty($email) || empty($password)) {
    echo "Please fill in all fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
}

// Check for duplicates
$check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "Username or email already exists.";
    exit;
}

// Proceed to insert user with default role_id = 2
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$default_role_id = 2;

$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $username, $email, $password_hash, $default_role_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Registration failed: " . $stmt->error;
}
?>
