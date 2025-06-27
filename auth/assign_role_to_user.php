<?php
require_once "conn_db.php";
session_start();

$successMsg = '';
$edit_id = null;
$edit_role = '';
$edit_user = '';

// Handle insert
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['assign'])) {
    $user_id = $_POST['user_id'];
    $role_id = $_POST['role_id'];

    $check = $conn->prepare("SELECT * FROM users_roles WHERE user_id = ? AND role_id = ?");
    $check->bind_param("ii", $user_id, $role_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO users_roles (user_id, role_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $role_id);
        $insert->execute();
        $successMsg = "Mapping added successfully!";
    } else {
        $successMsg = "Mapping already exists!";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = $_GET['user_id'];
    $role_id = $_GET['role_id'];
    $del = $conn->prepare("DELETE FROM users_roles WHERE user_id = ? AND role_id = ?");
    $del->bind_param("ii", $user_id, $role_id);
    $del->execute();
    $successMsg = "Mapping deleted successfully!";
}

// Fetch mappings
$sql = "SELECT ur.user_id, ur.role_id, u.username, r.role_name
        FROM users_roles ur
        JOIN users u ON ur.user_id = u.id
        JOIN roles r ON ur.role_id = r.id";
$result = $conn->query($sql);

// Get users and roles for dropdown
$users = $conn->query("SELECT id, username FROM users WHERE isdeleted = 0");
$roles = $conn->query("SELECT id, role_name FROM roles");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Role to User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #2c3e50, #3498db);
      font-family: 'Poppins', sans-serif;
      padding-top: 30px;
    }
    .container {
      max-width: 800px;
    }
    .fade {
      transition: opacity 0.5s ease-out;
      opacity: 0;
    }
  </style>
</head>
<body>
  <div class="container bg-white p-4 rounded shadow">
    <h3 class="mb-4">Assign Role to User</h3>

    <?php if ($successMsg): ?>
      <div id="successAlert" class="alert alert-success"><?= $successMsg ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
      <div class="col-md-5">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select" required>
          <option value="">Select User</option>
          <?php while ($u = $users->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">Role</label>
        <select name="role_id" class="form-select" required>
          <option value="">Select Role</option>
          <?php while ($r = $roles->fetch_assoc()): ?>
            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" name="assign" class="btn btn-primary w-100">Assign</button>
      </div>
    </form>

    <h5>Assigned Roles</h5>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>User</th>
          <th>Role</th>
          <th style="width: 150px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['role_name']) ?></td>
            <td style="text-align: center;">
              <!-- You can implement edit later if needed -->
              <a href="?delete=1&user_id=<?= $row['user_id'] ?>&role_id=<?= $row['role_id'] ?>" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Delete this mapping?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Main Menu</a>
  </div>

  <script>
    window.onload = function () {
      const alertBox = document.getElementById("successAlert");
      if (alertBox) {
        setTimeout(() => {
          alertBox.classList.add("fade");
          setTimeout(() => alertBox.remove(), 500);
        }, 3000);
      }
    };
  </script>
</body>
</html>
