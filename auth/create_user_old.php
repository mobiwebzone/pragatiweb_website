<?php require_once "conn_db.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create New User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #2c3e50, #3498db);
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
    }
    .fade-out {
      transition: opacity 1s ease-in-out;
    }
    .fade-out.hide {
      opacity: 0;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create New User</h4>
        <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">⬅ Back to Dashboard</a>
      </div>
      <div class="card-body">
        <div id="messageBox" class="alert d-none"></div>

        <form id="createUserForm">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="mb-3">
            <label for="role_id" class="form-label">Assign Role</label>
            <select class="form-select" name="role_id" required>
              <option value="">-- Select Role --</option>
              <?php
              $roles = $conn->query("SELECT id, role_name FROM roles");
              while ($row = $roles->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['role_name']) . "</option>";
              }
              ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Create User</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('createUserForm');
    const messageBox = document.getElementById('messageBox');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      const response = await fetch("create_user_submit.php", {
        method: "POST",
        body: formData
      });

      const result = await response.text();

      if (result.trim() === "success") {
        messageBox.textContent = "User created successfully.";
        messageBox.className = "alert alert-success fade-out";
        messageBox.classList.remove("d-none");

        form.reset();

        setTimeout(() => {
          messageBox.classList.add("hide");
        }, 3000);
      } else {
        messageBox.textContent = result;
        messageBox.className = "alert alert-danger fade-out";
        messageBox.classList.remove("d-none");

        setTimeout(() => {
          messageBox.classList.add("hide");
        }, 4000);
      }
    });
  </script>
</body>
</html>
