<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$username = $_SESSION['username'] ?? 'Unknown';
$email = $_SESSION['email'] ?? 'Not available';
$role_id = $_SESSION['role_id'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif, Arial;
      background: linear-gradient(135deg, #2c3e50, #3498db);
      margin: 0;
      height: 100vh;
      overflow: hidden;
      position: relative;
    }
    .main-content {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    .card-title {
      font-size: 24px;
      font-weight: bold;
      color: #007bff;
    }
    .user-toggle {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #fff;
      border: none;
      border-radius: 50px;
      padding: 8px 16px;
      font-weight: bold;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      cursor: pointer;
      z-index: 1100;
    }
    .sidebar {
      position: absolute;
      top: 0;
      right: -280px;
      height: 100%;
      width: 280px;
      background: #fff;
      padding: 30px;
      box-shadow: -2px 0 8px rgba(0,0,0,0.1);
      transition: right 0.3s ease;
      z-index: 1000;
    }
    .sidebar.open {
      right: 0;
    }
    .icon-btn {
      font-size: 16px;
      cursor: pointer;
      margin-left: 5px;
    }
    .save-message {
      font-size: 13px;
    }
  </style>
</head>
<body onclick="hideSidebar(event)">
  <button class="user-toggle" onclick="toggleSidebar(event)">👤 <?php echo htmlspecialchars($username); ?></button>
  <div id="sidebar" class="sidebar">
    <h5>User Settings</h5>
    <p><strong>Username:</strong><br><?php echo htmlspecialchars($username); ?></p>
    <div class="mb-3">
      <label class="form-label d-flex justify-content-between align-items-center">
        <strong>Email</strong>
        <span>
          <span id="editIcon" class="icon-btn" onclick="enableEmailEdit()">✏️</span>
          <span id="saveIcon" class="icon-btn d-none" onclick="updateEmail()">✅</span>
        </span>
      </label>
      <input type="email" id="emailInput" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
      <div id="emailMessage" class="mt-1 save-message d-none"></div>
    </div>
    <p><strong>role_id:</strong><br><?php echo htmlspecialchars($role_id); ?></p>
    <hr>
    <a href="logout.php" class="btn btn-outline-danger w-100 mt-2">Logout</a>
  </div>
  <div class="main-content">
    <div class="card text-center">
      <h1 class="card-title mb-3">Welcome to Pragatiweb</h1>
      <p class="card-text">Please contact System Admin!</p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar(event) {
      event.stopPropagation();
      document.getElementById("sidebar").classList.toggle("open");
    }
    function hideSidebar(event) {
      const sidebar = document.getElementById("sidebar");
      const toggleBtn = document.querySelector(".user-toggle");
      if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
        sidebar.classList.remove("open");
      }
    }
    function enableEmailEdit() {
      const input = document.getElementById("emailInput");
      input.removeAttribute("readonly");
      input.focus();
      document.getElementById("editIcon").classList.add("d-none");
      document.getElementById("saveIcon").classList.remove("d-none");
    }
    async function updateEmail() {
      const input = document.getElementById("emailInput");
      const msgBox = document.getElementById("emailMessage");
      const formData = new FormData();
      formData.append("email", input.value);
      const response = await fetch("update_email.php", {
        method: "POST",
        body: formData
      });
      const result = await response.text();
      const trimmedResult = result.trim();
      msgBox.classList.remove("d-none");

      if (trimmedResult === "success") {
        msgBox.textContent = "Email updated successfully!";
        msgBox.classList.remove("text-danger");
        msgBox.classList.add("text-success");
        input.setAttribute("readonly", true);
        document.getElementById("saveIcon").classList.add("d-none");
        document.getElementById("editIcon").classList.remove("d-none");
        setTimeout(() => msgBox.classList.add("d-none"), 2000);
      } else if (trimmedResult === "duplicate") {
        msgBox.textContent = "This email already exists.";
        msgBox.classList.remove("text-success");
        msgBox.classList.add("text-danger");
      } else if (trimmedResult === "invalid") {
        msgBox.textContent = "Invalid email address.";
        msgBox.classList.remove("text-success");
        msgBox.classList.add("text-danger");
      } else {
        msgBox.textContent = "Something went wrong.";
        msgBox.classList.remove("text-success");
        msgBox.classList.add("text-danger");
      }
    }
  </script>
</body>
</html>
