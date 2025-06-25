<?php
require_once "conn_db.php";
session_start();

$username_or_email = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username_or_email) || empty($password)) {
    echo "error";
    exit;
}

// Query to fetch user with role
$query = "SELECT u.id, u.username, u.password_hash, r.role_name, r.id as role_id, u.email
          FROM users u
          JOIN roles r ON u.role_id = r.id
          WHERE u.username = ? OR u.email = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role'] = $user['role_name'];  // Add this for consistency

        // Respond with role name for frontend redirection
        echo $user['role_name']; // admin or user
    } else {
        echo "error";
    }
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
