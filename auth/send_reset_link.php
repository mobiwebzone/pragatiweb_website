<?php
require_once "conn_db.php";
require_once "send_mail.php"; // Uses PHPMailer

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+4 hour"));

        // Remove any existing reset tokens for this email
        $conn->query("DELETE FROM password_resets WHERE email = '$email'");

        // Insert new token into password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, token_expiry) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expiry);

        if ($stmt->execute()) {
            // Prepare reset link and send email
            $reset_link = "http://localhost/pragatiweb_website/auth/reset_password.php?token=$token";
            $subject = "Password Reset Request";
            $body = "Click the following link to reset your password:\n\n$reset_link\n\nThis link is valid for 1 hour.";

            if (sendMail($email, $subject, $body)) {
                echo "sent";
            } else {
                echo "Failed to send email.";
            }
        } else {
            echo "Error inserting token.";
        }
    } else {
        echo "Email not found.";
    }
}
?>
