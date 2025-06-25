<?php
require_once "conn_db.php";

$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role_name = trim($_POST["role_name"] ?? '');

    if (!empty($role_name)) {
        $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
        $stmt->bind_param("s", $role_name);

        if ($stmt->execute()) {
            $successMsg = "Role created successfully.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Role</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c3e50, #3498db);
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      padding: 30px;
    }
    .card {
      margin-top: 20px;
    }
    .success-message {
      display: <?php echo !empty($successMsg) ? 'block' : 'none'; ?>;
      background-color: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h4>Create New Role</h4>
    </div>
    <div class="card-body">
      <?php if (!empty($successMsg)): ?>
        <div id="successBox" class="success-message"><?php echo $successMsg; ?></div>
      <?php endif; ?>
      <form action="" method="POST">
        <div class="mb-3">
          <label for="role_name" class="form-label">Role Name</label>
          <input type="text" class="form-control" id="role_name" name="role_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Role</button>
        <a href="admin_dashboard.php" class="btn btn-secondary float-end">Back to Main Form</a>
      </form>
    </div>
  </div>

  <!-- Role Table -->
  <div class="card shadow mt-4">
    <div class="card-header bg-secondary text-white">
      <h5>Existing Roles</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-striped table-bordered m-0">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Role Name</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT id, role_name FROM roles ORDER BY id ASC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role_name']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No roles found</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  setTimeout(() => {
    const box = document.getElementById("successBox");
    if (box) box.style.display = "none";
  }, 3000);
</script>
</body>
</html>
