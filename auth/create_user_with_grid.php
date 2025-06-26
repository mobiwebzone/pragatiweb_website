<?php
require_once "conn_db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

// Handle deletion (soft delete)
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("UPDATE users SET isdeleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    header("Location: create_user_with_grid.php");
    exit;
}

// Handle user update
if (isset($_POST['update_user'])) {
    $id = (int) $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = (int) $_POST['role_id'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role_id=? WHERE id=?");
    $stmt->bind_param("ssii", $username, $email, $role_id, $id);
    $stmt->execute();
    header("Location: create_user_with_grid.php");
    exit;
}

// Handle new user creation
if (isset($_POST['save_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = (int) $_POST['role_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND isdeleted = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Email ID already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, role_id, password_hash, isdeleted) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssis", $username, $email, $role_id, $password);
        $stmt->execute();
        header("Location: create_user_with_grid.php");
        exit;
    }
}

// Fetch roles for dropdown
$roles = $conn->query("SELECT id, role_name FROM roles");

// Fetch users list
$users = $conn->query("SELECT u.id, u.username, u.email, r.role_name, u.role_id FROM users u JOIN roles r ON u.role_id = r.id WHERE u.isdeleted = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c3e50, #3498db);
      font-family: 'Poppins', sans-serif;
      padding: 40px;
    }
    .card {
      max-width: 800px;
      margin: auto;
    }
    #infoBox {
      transition: opacity 0.5s ease-in-out;
    }
  </style>
</head>
<body>
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h4>Create New User</h4>
    </div>
    <div class="card-body">
      <?php if (!empty($message)): ?>
        <div id="infoBox" class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="id" id="userId">
        <div class="row mb-3">
          <div class="col">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" id="username" required>
          </div>
          <div class="col">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col">
            <label for="role_id" class="form-label">Role</label>
            <select class="form-select" name="role_id" id="role_id" required>
              <option value="">Select Role</option>
              <?php while ($row = $roles->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['role_name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col" id="passwordContainer">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
          </div>
        </div>
        <div class="d-flex justify-content-between">
          <button type="submit" name="save_user" class="btn btn-primary" id="saveBtn">Create User</button>
          <button type="submit" name="update_user" class="btn btn-success d-none" id="updateBtn">Update User</button>
          <a href="admin_dashboard.php" class="btn btn-secondary">Back to Main Menu</a>
        </div>
      </form>
    </div>
  </div>

  <div class="container mt-5">
    <!-- <h5 class="mb-3">Users List</h5> -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role_name']) ?></td>
            <td>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" onclick="editUser(<?= $row['id'] ?>, '<?= $row['username'] ?>', '<?= $row['email'] ?>', <?= $row['role_id'] ?>)">Edit</button>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</a>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script>
    function editUser(id, username, email, roleId) {
      document.getElementById('userId').value = id;
      document.getElementById('username').value = username;
      document.getElementById('email').value = email;
      document.getElementById('role_id').value = roleId;
      document.getElementById('passwordContainer').style.display = 'none';
      document.getElementById('saveBtn').classList.add('d-none');
      document.getElementById('updateBtn').classList.remove('d-none');
    }

    window.onload = function () {
      const infoBox = document.getElementById("infoBox");
      if (infoBox) {
        setTimeout(() => {
          infoBox.style.display = 'none';
        }, 3000);
      }
    };
  </script>
</body>
</html>
