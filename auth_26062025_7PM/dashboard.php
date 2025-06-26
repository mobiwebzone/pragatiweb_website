<?php
require_once "conn_db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// Fetch menus based on assigned roles
$menu_query = "
    SELECT DISTINCT m.menu_name, m.menu_link
    FROM menus m
    JOIN roles_menus rm ON m.id = rm.menu_id
    JOIN users_roles ur ON rm.role_id = ur.role_id
    WHERE ur.user_id = ?
";
$stmt = $conn->prepare($menu_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$menus = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #2c3e50, #3498db);
      margin: 0;
      height: 100vh;
      overflow-x: hidden;
    }
    .sidebar {
      position: fixed;
      right: -300px;
      top: 0;
      width: 280px;
      height: 100%;
      background: #fff;
      padding: 30px;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
      transition: right 0.3s ease;
    }
    .sidebar.open {
      right: 0;
    }
    .main-content {
      padding: 30px;
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
    .card {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    .right_margin-menu {
      margin-right: 110px;
                  }

  </style>
</head>
<body onclick="hideSidebar(event)">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark right_margin-menu">
  <div class="container-fluid">
    <span class="navbar-brand">Welcome, <?php echo htmlspecialchars($username); ?></span>
    <ul class="navbar-nav ms-auto">
      <?php while ($menu = $menus->fetch_assoc()): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo htmlspecialchars($menu['menu_link']); ?>">
            <?php echo htmlspecialchars($menu['menu_name']); ?>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</nav>

<!-- Toggle user sidebar -->
<button class="user-toggle" onclick="toggleSidebar(event)">👤 <?php echo htmlspecialchars($username); ?></button>

<!-- Main content -->
<!-- <div class="main-content">
  <div class="card text-center">
    <h1 class="mb-3">User Dashboard</h1>
    <p>This dashboard shows only menus assigned to your role(s).</p>
  </div>
</div> -->

<!-- Sidebar for user info -->
<div id="sidebar" class="sidebar">
  <h5>User Info</h5>
  <p><strong>Username:</strong><br><?php echo htmlspecialchars($username); ?></p>
  <div class="mb-3">
    <label class="form-label"><strong>Email</strong></label>
    <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
  </div>
  <hr>
  <a href="logout.php" class="btn btn-outline-danger w-100 mt-2">Logout</a>
  <button class="btn btn-outline-secondary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
</div>

<!-- Modal for password change -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <h5 class="modal-title mb-3" id="changePasswordLabel">Change Password</h5>
      <form id="changePasswordForm">
        <div id="passwordMessage" class="alert mt-2 d-none" role="alert"></div>
        <div class="mb-3">
          <label for="currentPassword" class="form-label">Current Password</label>
          <input type="password" class="form-control" id="currentPassword" required>
        </div>
        <div class="mb-3">
          <label for="newPassword" class="form-label">New Password</label>
          <input type="password" class="form-control" id="newPassword" required>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Change</button>
        </div>
      </form>
    </div>
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

  document.getElementById("changePasswordForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append("currentPassword", document.getElementById("currentPassword").value);
    formData.append("newPassword", document.getElementById("newPassword").value);

    const response = await fetch("change_password.php", {
      method: "POST",
      body: formData
    });

    const result = await response.json();
    const msgBox = document.getElementById("passwordMessage");

    if (result.status === "success") {
      msgBox.className = "alert alert-success mt-2";
      msgBox.textContent = "Password changed successfully!";
      msgBox.classList.remove("d-none");
      document.getElementById("changePasswordForm").reset();
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById("changePasswordModal"));
        modal.hide();
        msgBox.classList.add("d-none");
      }, 1500);
    } else {
      msgBox.className = "alert alert-danger mt-2";
      msgBox.textContent = "Error: " + result.message;
      msgBox.classList.remove("d-none");
    }
  });
</script>

</body>
</html>
