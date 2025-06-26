<?php
require_once "conn_db.php";

// Message feedback
$message = "";
$edit_id = $_GET['edit_id'] ?? null;


session_start();
$role_id = $_SESSION['role_id'] ?? 0;
$dashboard = ($role_id == 1) ? "admin_dashboard.php" : "dashboard.php";



// Edit mode fetch
$edit_menu_name = $edit_menu_link = "";
if ($edit_id) {
    $stmt = $conn->prepare("SELECT menu_name, menu_link FROM menus WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $stmt->bind_result($edit_menu_name, $edit_menu_link);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu_name = trim($_POST["menu_name"] ?? '');
    $menu_link = trim($_POST["menu_link"] ?? '');
    $id = $_POST['menu_id'] ?? '';

    if (!empty($menu_name)) {
        if (!empty($id)) {
            $stmt = $conn->prepare("UPDATE menus SET menu_name = ?, menu_link = ? WHERE id = ?");
            $stmt->bind_param("ssi", $menu_name, $menu_link, $id);
            $stmt->execute();
            $message = "Menu updated successfully.";
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO menus (menu_name, menu_link) VALUES (?, ?)");
            $stmt->bind_param("ss", $menu_name, $menu_link);
            $stmt->execute();
            $message = "Menu created successfully.";
            $stmt->close();
        }
    } else {
        $message = "Menu name is required.";
    }

    // Redirect to avoid resubmission
    header("Location: create_menu_with_grid.php");
    exit;
}

// Delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM menus WHERE id = $delete_id");
    header("Location: create_menu_with_grid.php");
    exit;
}

// Fetch menus
$menus = $conn->query("SELECT id, menu_name, menu_link FROM menus");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Menus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: linear-gradient(135deg, #2c3e50, #3498db); min-height: 100vh;">
  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4>Create Menu</h4>
        <!-- <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">← Back to Main Form</a> -->
         <a href="<?= $dashboard ?>" class="btn btn-secondary btn-sm">Back to Main Menu</a>
      </div>
      <div class="card-body">
        <?php if (!empty($message)): ?>
          <div id="alertMessage" class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <script>
            setTimeout(() => {
              const alertBox = document.getElementById('alertMessage');
              if (alertBox) {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(alertBox);
                alertInstance.close();
              }
            }, 3000);
          </script>
        <?php endif; ?>

        <form method="POST" action="create_menu_with_grid.php">
          <input type="hidden" name="menu_id" value="<?= htmlspecialchars($edit_id ?? '') ?>">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Menu Name</label>
              <input type="text" name="menu_name" class="form-control" required value="<?= htmlspecialchars($edit_menu_name) ?>" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Menu Link</label>
              <input type="text" name="menu_link" class="form-control" value="<?= htmlspecialchars($edit_menu_link) ?>" />
            </div>
          </div>
          <button type="submit" class="btn btn-primary"><?= $edit_id ? "Update Menu" : "Create Menu" ?></button>
        </form>
      </div>
    </div>

    <div class="card mt-4 shadow">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Existing Menus</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Menu Name</th>
                <th>Menu Link</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $menus->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['id']) ?></td>
                  <td><?= htmlspecialchars($row['menu_name']) ?></td>
                  <td><?= htmlspecialchars($row['menu_link']) ?></td>
                  <td>
                    <a href="create_menu_with_grid.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="create_menu_with_grid.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this menu?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
