<?php
require_once "conn_db.php";

$successMsg = "";
$errorMsg = "";

// Insert or Update Mapping
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role_id = $_POST["role_id"];
    $menu_id = $_POST["menu_id"];
    $edit_mode = $_POST["edit_mode"] ?? "";
    $old_role_id = $_POST["old_role_id"] ?? "";
    $old_menu_id = $_POST["old_menu_id"] ?? "";

    if ($edit_mode === "true") {
        $stmt = $conn->prepare("UPDATE roles_menus SET role_id = ?, menu_id = ? WHERE role_id = ? AND menu_id = ?");
        $stmt->bind_param("iiii", $role_id, $menu_id, $old_role_id, $old_menu_id);
        if ($stmt->execute()) {
            $successMsg = "Mapping updated successfully.";
        } else {
            $errorMsg = "Error updating mapping.";
        }
    } else {
        // Check for duplicates
        $check = $conn->prepare("SELECT * FROM roles_menus WHERE role_id = ? AND menu_id = ?");
        $check->bind_param("ii", $role_id, $menu_id);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $errorMsg = "Mapping already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO roles_menus (role_id, menu_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $role_id, $menu_id);
            if ($stmt->execute()) {
                $successMsg = "Mapping inserted successfully.";
            } else {
                $errorMsg = "Error inserting mapping.";
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete_role']) && isset($_GET['delete_menu'])) {
    $role_id = $_GET['delete_role'];
    $menu_id = $_GET['delete_menu'];
    $del = $conn->prepare("DELETE FROM roles_menus WHERE role_id = ? AND menu_id = ?");
    $del->bind_param("ii", $role_id, $menu_id);
    if ($del->execute()) {
        $successMsg = "Mapping deleted successfully.";
    } else {
        $errorMsg = "Error deleting mapping.";
    }
}

// Fetch dropdown options
$roles = $conn->query("SELECT id, role_name FROM roles");
$menus = $conn->query("SELECT id, menu_name FROM menus");

// Fetch existing mappings
$mapQuery = "
    SELECT r.role_name, m.menu_name, rm.role_id, rm.menu_id
    FROM roles_menus rm
    JOIN roles r ON rm.role_id = r.id
    JOIN menus m ON rm.menu_id = m.id order by r.id
";
$mappings = $conn->query($mapQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Menu to Role</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #2c3e50, #3498db); min-height: 100vh;">
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h4>Assign Menu to Role</h4>
    </div>
    <div class="card-body">
      <?php if ($successMsg): ?>
        <div id="successAlert" class="alert alert-success"><?= $successMsg ?></div>
      <?php elseif ($errorMsg): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="edit_mode" id="edit_mode" value="false">
        <input type="hidden" name="old_role_id" id="old_role_id">
        <input type="hidden" name="old_menu_id" id="old_menu_id">

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Select Role</label>
            <select name="role_id" id="role_id" class="form-select" required>
              <option value="">-- Select Role --</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Select Menu</label>
            <select name="menu_id" id="menu_id" class="form-select" required>
              <option value="">-- Select Menu --</option>
              <?php foreach ($menus as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['menu_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Mapping</button>
        <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Main Menu</a>
      </form>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header bg-dark text-white">
      <h5>Existing Role-Menu Mappings</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-bordered mb-0">
        <thead class="table-dark">
          <tr>
            <th>Role</th>
            <th>Menu</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $mappings->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['role_name']) ?></td>
              <td><?= htmlspecialchars($row['menu_name']) ?></td>
              <td style="text-align:center ;">
                <button class="btn btn-sm btn-primary" onclick="editMapping(<?= $row['role_id'] ?>, <?= $row['menu_id'] ?>)">Edit</button>
                <a href="?delete_role=<?= $row['role_id'] ?>&delete_menu=<?= $row['menu_id'] ?>" class="btn btn-sm btn-danger ms-2" onclick="return confirm('Are you sure to delete this mapping?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function editMapping(role_id, menu_id) {
    document.getElementById("role_id").value = role_id;
    document.getElementById("menu_id").value = menu_id;
    document.getElementById("edit_mode").value = "true";
    document.getElementById("old_role_id").value = role_id;
    document.getElementById("old_menu_id").value = menu_id;
  }

  // Hide success message after 3 seconds
  window.onload = function () {
    const alertBox = document.getElementById("successAlert");
    if (alertBox) {
      setTimeout(() => alertBox.style.display = "none", 3000);
    }
  };
</script>
</body>
</html>
