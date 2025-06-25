<?php
require_once "conn_db.php";

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu_name = trim($_POST["menu_name"] ?? '');
    $menu_link = trim($_POST["menu_link"] ?? '');

    if (empty($menu_name)) {
        $message = "Menu name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO menus (menu_name, menu_link) VALUES (?, ?)");
        $stmt->bind_param("ss", $menu_name, $menu_link);
        if ($stmt->execute()) {
            $message = "Menu created successfully.";
        } else {
            $message = "Error creating menu.";
        }
        $stmt->close();
    }
}

// Fetch menus for grid
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
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4>Create Menu</h4>
        <a href="admin_dashboard.php" class="btn btn-light btn-sm">← Back to Main Form</a>
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
            }, 3000); // 3 seconds
          </script>
        <?php endif; ?>


        <form method="POST" action="create_menu_with_grid.php">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Menu Name</label>
              <input type="text" name="menu_name" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Menu Link</label>
              <input type="text" name="menu_link" class="form-control" />
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Create Menu</button>
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
              </tr>
            </thead>
            <tbody>
              <?php if ($menus && $menus->num_rows > 0): ?>
                <?php while ($row = $menus->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['menu_name']) ?></td>
                    <td><?= htmlspecialchars($row['menu_link']) ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center">No menus found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
