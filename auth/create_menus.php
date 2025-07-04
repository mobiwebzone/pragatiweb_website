<?php
session_start();
$role_id = $_SESSION['role_id'] ?? 0;
$dashboard = ($role_id == 1) ? "admin_dashboard.php" : "dashboard.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create New Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: linear-gradient(135deg, #2c3e50, #3498db); min-height: 100vh;">
  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create New Menu</h4>
        <a href="<?= $dashboard ?>" class="btn btn-outline-secondary btn-sm">Back to Main Menu</a>
      </div>
      <div class="card-body">
        <div id="messageBox" class="alert d-none"></div>
        <form id="menuForm">
          <div class="mb-3">
            <label for="menu_name" class="form-label">Menu Name</label>
            <input type="text" class="form-control" name="menu_name" required>
          </div>
          <div class="mb-3">
            <label for="menu_link" class="form-label">Menu Link</label>
            <input type="text" class="form-control" name="menu_link" placeholder="Optional (e.g., create_user.php)">
          </div>
          <button type="submit" class="btn btn-primary">Create Menu</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("menuForm").addEventListener("submit", async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const response = await fetch("create_new_menus.php", {
        method: "POST",
        body: formData
      });
      const result = await response.text();
      const msgBox = document.getElementById("messageBox");
      msgBox.className = "alert mt-2";
      if (result.trim() === "success") {
        msgBox.classList.add("alert-success");
        msgBox.textContent = "Menu created successfully.";
        msgBox.classList.remove("d-none");
        this.reset();
        setTimeout(() => msgBox.classList.add("d-none"), 3000);
      } else {
        msgBox.classList.add("alert-danger");
        msgBox.textContent = "Error: " + result;
        msgBox.classList.remove("d-none");
      }
    });
  </script>
</body>
</html>
