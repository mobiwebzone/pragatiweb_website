<?php
require_once "conn_db.php";
require_once "send_mail.php"; // include PHPMailer send function

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $message = "Please enter your email address.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $userId = $user['id'];
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $conn->query("DELETE FROM password_resets WHERE user_id = $userId");

            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $tokenHash, $expiry);
            $stmt->execute();

            $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
            $sendResult = sendPasswordResetEmail($email, $resetLink);

            $message = $sendResult === "success" ? "Password reset link has been sent to your email." : "Error sending email: $sendResult";
        } else {
            $message = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: linear-gradient(135deg, #2c3e50, #3498db); height: 100vh;">
<div class="container d-flex justify-content-center align-items-center" style="height:100%;">
  <div class="card p-4 shadow" style="width: 400px;">
    <h4 class="mb-3">Forgot Password</h4>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Enter your registered email</label>
        <input type="email" name="email" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
      <a href="login.html" class="btn btn-secondary w-100 mt-2">Back to Login</a>
    </form>
  </div>
</div>
</body>
</html>