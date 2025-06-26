<?php
require_once "conn_db.php";

$result = $conn->query("SELECT * FROM mysql_enquiries ORDER BY id ");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enquiry Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: #f2f2f2;
    }
    .heading {
      background: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      border-bottom: 1px solid #ccc;
    }
    .heading h2 {
      margin: 0;
      font-size: 24px;
      color: #333;
    }
    .btn-back {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body>
  
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role_id = $_SESSION['role_id'] ?? 0;
$backLink = ($role_id == 1) ? 'admin_dashboard.php' : 'dashboard.php';
?>

<div class="heading">
  <h2>Enquiry Details</h2>
  <a href="<?php echo $backLink; ?>" class="btn btn-back">Back to Main Form</a>
</div>

  
  <div class="container">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          
          <th>Name</th>
          <th>Mobile No</th>
          <th>Email</th>
          <th>Institution</th>
          <th>Product</th>
          <th>City</th>
          <th>Message</th>
          <th>Entry Date</th>
          <th>Response</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
          <tr>
            
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['Mobile_no']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['Institution_name']) ?></td>
            <td><?= htmlspecialchars($row['Product_type']) ?></td>
            <td><?= htmlspecialchars($row['city']) ?></td>
            <td><?= htmlspecialchars($row['Message']) ?></td>
            <td><?= date('d-M-Y', strtotime($row['entry_date'])) ?></td>
            <td><?= htmlspecialchars($row['response']) ?></td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editResponse(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['response'])) ?>')">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="deleteEntry(<?= $row['id'] ?>)">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="editForm" class="modal-content" method="post" action="update_response.php">
        <div class="modal-header">
          <h5 class="modal-title">Edit Response</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editId">
          <div class="mb-3">
            <label for="editResponse" class="form-label">Response</label>
            <textarea name="response" id="editResponse" rows="4" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editResponse(id, response) {
      document.getElementById('editId').value = id;
      document.getElementById('editResponse').value = response;
      const modal = new bootstrap.Modal(document.getElementById('editModal'));
      modal.show();
    }

    function deleteEntry(id) {
      if (confirm("Are you sure you want to delete this enquiry?")) {
        window.location.href = 'delete_enquiry.php?id=' + id;
      }
    }
  </script>
</body>
</html>
