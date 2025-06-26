<?php
require_once "conn_db.php";

$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role_id = $_POST["role_id"] ?? '';
    $menu_id = $_POST["menu_id"] ?? '';

    if (!empty($role_id) && !empty($menu_id)) {
        $check = $conn->prepare("SELECT * FROM roles_menus WHERE role_id = ? AND menu_id = ?");
        $check->bind_param("ii", $role_id, $menu_id);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO roles_menus (role_id, menu_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $role_id, $menu_id);
            $stmt->execute();
            $successMsg = "Menu assigned to role successfully.";
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
  <meta charset="UTF-8" />
  <title>Assign Menu to Role</title>
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
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h4>Assign Menu to Role</h4>
    </div>
    <div class="card-body">
      <?php if ($successMsg): ?>
        <div id="successBox" class="alert alert-success fade-out"><?php echo $successMsg; ?></div>
      <?php endif; ?>

      <form method="POST">
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

        <div class="mb-3">
          <label class="form-label">Select Menu</label>
          <select name="menu_id" class="form-select" required>
            <option value="">-- Select Menu --</option>
            <?php
              $menus = $conn->query("SELECT id, menu_name FROM menus");
              while ($m = $menus->fetch_assoc()) {
                echo "<option value='{$m['id']}'>" . htmlspecialchars($m['menu_name']) . "</option>";
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
      <div class="card-header bg-secondary text-white">Assigned Menus to Roles</div>
      <div class="card-body p-0">
        <table class="table table-striped m-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Role</th>
              <th>Menu</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $result = $conn->query("
                SELECT r.role_name, m.menu_name 
                FROM roles_menus rm
                JOIN roles r ON rm.role_id = r.id
                JOIN menus m ON rm.menu_id = m.id
              ");
              $counter = 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$counter}</td>
                        <td>" . htmlspecialchars($row['role_name']) . "</td>
                        <td>" . htmlspecialchars($row['menu_name']) . "</td>
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
    // Hide success message after 3 seconds
    setTimeout(() => {
      const msg = document.getElementById("successBox");
      if (msg) msg.classList.add("hidden");
    }, 3000);
  </script>
</body>
</html>
