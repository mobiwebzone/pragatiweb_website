<?php
require_once "conn_db.php";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? '';
    $role_id = $_POST["role_id"] ?? '';

    if (!empty($user_id) && !empty($role_id)) {
        // Prevent duplicates
        $check = $conn->prepare("SELECT * FROM users_roles WHERE user_id = ? AND role_id = ?");
        $check->bind_param("ii", $user_id, $role_id);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO users_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $role_id);
            $stmt->execute();
            $successMsg = "Role assigned to user successfully.";
        }
    }
}


session_start();
$role_id = $_SESSION['role_id'] ?? 0;
$dashboard = ($role_id == 1) ? "admin_dashboard.php" : "dashboard.php";
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
      min-height: 100vh;
      padding: 30px;
    }
    .card {
      max-width: 600px;
      margin: auto;
    }
    .fade-out {
      transition: opacity 1s ease-out;
      opacity: 1;
    }
    .fade-out.hidden {
      opacity: 0;
    }
  </style>
</head>
<body>
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white">
      <h4>Assign Role to User</h4>
    </div>
    <div class="card-body">
      <?php if ($successMsg): ?>
        <div id="successBox" class="alert alert-success fade-out"><?php echo $successMsg; ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Select User</label>
          <select name="user_id" class="form-select" required>
            <option value="">-- Select User --</option>
            <?php
              $users = $conn->query("SELECT id, username FROM users");
              while ($u = $users->fetch_assoc()) {
                echo "<option value='{$u['id']}'>" . htmlspecialchars($u['username']) . "</option>";
              }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Select Role</label>
          <select name="role_id" class="form-select" required>
            <option value="">-- Select Role --</option>
            <?php
              $roles = $conn->query("SELECT id, role_name FROM roles");
              while ($r = $roles->fetch_assoc()) {
                echo "<option value='{$r['id']}'>" . htmlspecialchars($r['role_name']) . "</option>";
              }
            ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Assign</button>
        <!-- <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Main Form</a> -->
         <a href="<?= $dashboard ?>" class="btn btn-secondary ms-2">Back to Main Menu</a>
      </form>
    </div>
  </div>

  <div class="mt-4 container">
    <div class="card shadow">
      <div class="card-header bg-secondary text-white">Assigned Roles to Users</div>
      <div class="card-body p-0">
        <table class="table table-striped m-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Role</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $result = $conn->query("
                SELECT u.username, r.role_name 
                FROM users_roles ur
                JOIN users u ON ur.user_id = u.id
                JOIN roles r ON ur.role_id = r.id
              ");
              $counter = 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$counter}</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['role_name']) . "</td>
                      </tr>";
                $counter++;
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    setTimeout(() => {
      const msg = document.getElementById("successBox");
      if (msg) msg.classList.add("hidden");
    }, 3000);
  </script>
</body>
</html>
