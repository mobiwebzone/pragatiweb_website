<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$email = $_SESSION['email'];
$role_id = $_SESSION['role_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
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

    .navbar {
      z-index: 1050;
    }

    
    .right-menu_margin {
      margin-right: 110px;
                  }

  </style>
</head>
<body onclick="hideSidebar(event)">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Admin Dashboard</a>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">
  
  
    <!-- School Management -->
 <li class="nav-item dropdown">
    
    <a class="nav-link dropdown-toggle " href="#" id="enquiryMgmtDropdown" role="button" data-bs-toggle="dropdown">
      School
    </a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="http://103.25.174.53:8080/pragatiweb/backoffice/index.html#!/login" target="_blank">School Management</a></li>
    </ul>
  </li>

  <!-- User Management -->
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="userMgmtDropdown" role="button" data-bs-toggle="dropdown">
      User Management
    </a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="create_user_with_grid.php">Create New User</a></li>
      <li><a class="dropdown-item" href="create_roles.php">Create New Role</a></li>
      <li><a class="dropdown-item" href="create_menu_with_grid.php">Create New Menu</a></li>
      <li><a class="dropdown-item" href="assign_menu_to_role.php">Assign Menus to Role</a></li>
      <li><a class="dropdown-item" href="assign_role_to_user.php">Assign Role to User</a></li>
    </ul>
  </li>

  <!-- 🔹 Enquiry Management Menu -->
  <li class="nav-item dropdown">
    
    <a class="nav-link dropdown-toggle " href="#" id="enquiryMgmtDropdown" role="button" data-bs-toggle="dropdown">
      Enquiry Management
    </a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="enquiry_details.php">Enquiry Details</a></li>
    </ul>
  </li>


  <!-- 🔹 Support Menu -->
  <!-- <li class="nav-item dropdown">
    
    <a class="nav-link dropdown-toggle right-menu_margin" href="#" id="supportMgmtDropdown" role="button" data-bs-toggle="dropdown">
      Support
    </a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="#">Admin Documentation</a></li>
      <li><a class="dropdown-item" href="#">Admin Guide</a></li>
    </ul>
  </li> -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle right-menu_margin" href="#" id="supportMgmtDropdown" role="button" data-bs-toggle="dropdown">
        Support
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="admin_documentation.php">Admin Documentation</a></li>
        <li><a class="dropdown-item" href="admin_guide.php">Admin Guide</a></li>
      </ul>
    </li>


</ul>

      </div>
      <button class="user-toggle" onclick="toggleSidebar(event)">👤 <?php echo htmlspecialchars($username); ?></button>
    </div>
  </nav>

  <!-- <div class="main-content">
    <div class="card text-center">
      <h1 class="card-title mb-3">Welcome to Admin Panel</h1>
      <p class="card-text">Manage roles, users, and menus here.</p>
    </div>
  </div> -->

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
      <div id="emailMessage" class="text-success mt-1 save-message d-none">Email updated</div>
    </div>
    <!-- <p><strong>Role:</strong><br><?php echo htmlspecialchars($role_id); ?></p> -->
    <hr>
    <a href="logout.php" class="btn btn-outline-danger w-100 mt-2">Logout</a>
    <button class="btn btn-outline-secondary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
  </div>

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
      if (result.trim() === "success") {
        msgBox.classList.remove("d-none");
        input.setAttribute("readonly", true);
        document.getElementById("saveIcon").classList.add("d-none");
        document.getElementById("editIcon").classList.remove("d-none");
        setTimeout(() => msgBox.classList.add("d-none"), 2000);
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