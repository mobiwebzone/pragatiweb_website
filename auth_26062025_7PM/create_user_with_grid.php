<?php
require_once "conn_db.php";

// Handle AJAX submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajax"])) {
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $role_id = $_POST["role_id"] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($role_id)) {
        echo "empty";
        exit;
    }

    // Check if user/email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        echo "exists";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $password_hash, $role_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create User with Grid</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c3e50, #3498db);
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }
    .card {
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }
    .alert {
      display: none;
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New User</h2>
    <a href="admin_dashboard.php" class="btn btn-light">← Back to Main Menu</a>
  </div>

  <div id="messageBox" class="alert alert-success"></div>

  <div class="card mb-4">
    <div class="card-body bg-light text-dark">
      <form id="createUserForm">
        <div class="row mb-3">
          <div class="col-md-3">
            <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
          <div class="col-md-3">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
          <div class="col-md-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="col-md-2">
            <select class="form-select" name="role_id" required>
              <option value="">Select Role</option>
              <?php
              $roles = $conn->query("SELECT id, role_name FROM roles");
              while ($row = $roles->fetch_assoc()) {
                  echo "<option value='{$row['id']}'>" . htmlspecialchars($row['role_name']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <h4>Existing Users</h4>
  <table class="table table-bordered table-striped table-light">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $users = $conn->query("SELECT u.username, u.email, r.role_name FROM users u JOIN roles r ON u.role_id = r.id");
      $sn = 1;
      while ($user = $users->fetch_assoc()) {
          echo "<tr>
                  <td>{$sn}</td>
                  <td>" . htmlspecialchars($user['username']) . "</td>
                  <td>" . htmlspecialchars($user['email']) . "</td>
                  <td>" . htmlspecialchars($user['role_name']) . "</td>
                </tr>";
          $sn++;
      }
      ?>
    </tbody>
  </table>
</div>

<script>
  document.getElementById("createUserForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append("ajax", "1");

    const response = await fetch("create_user_with_grid.php", {
      method: "POST",
      body: formData
    });

    const result = await response.text();
    const messageBox = document.getElementById("messageBox");

    if (result === "success") {
      messageBox.textContent = "User created successfully!";
      messageBox.className = "alert alert-success";
      messageBox.style.display = "block";
      setTimeout(() => location.reload(), 1500);
    } else if (result === "exists") {
      messageBox.textContent = "Username or email already exists.";
      messageBox.className = "alert alert-warning";
      messageBox.style.display = "block";
    } else if (result === "empty") {
      messageBox.textContent = "Please fill all fields.";
      messageBox.className = "alert alert-danger";
      messageBox.style.display = "block";
    } else {
      messageBox.textContent = "Something went wrong!";
      messageBox.className = "alert alert-danger";
      messageBox.style.display = "block";
    }

    setTimeout(() => messageBox.style.display = "none", 3000);
  });
</script>
</body>
</html>
