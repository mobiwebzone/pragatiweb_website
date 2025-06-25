<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conn_db.php";

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$new_email = $_POST['email'] ?? '';

if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    echo "invalid";
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$check->bind_param("si", $new_email, $user_id);
$check->execute();
$check_result = $check->get_result();
if ($check_result->num_rows > 0) {
    echo "duplicate";
    exit;
}

$stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
$stmt->bind_param("si", $new_email, $user_id);

if ($stmt->execute()) {
    $_SESSION['email'] = $new_email;
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
