<?php
require_once "conn_db.php";

// Fetch all documentation records
$docs = [];
$result = $conn->query("SELECT id, title, content FROM admin_docs ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $docs[] = $row;
}
$selected_doc = $docs[0] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Guide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f1f1f1;
    }
    .sidebar {
      height: 100vh;
      width: 240px;
      position: fixed;
      background-color: #0b63ce;
      overflow-y: auto;
    }
    .sidebar h4 {
      color: #fff;
      font-size: 20px;
      padding: 15px;
      border-bottom: 1px solid #074aa1;
      margin: 0;
      background-color: #074aa1;
    }
    .sidebar a {
      display: block;
      padding: 12px 16px;
      color: white;
      text-decoration: none;
      border-bottom: 1px solid #0e71dd;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #0855b0;
    }
    .content {
      margin-left: 240px;
      padding: 20px 30px;
      background-color: #ffffff;
      min-height: 100vh;
    }
    .top-bar {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 15px;
    }
    .doc-container {
      border-left: 6px solid #0b63ce;
      background-color: #f9fbff;
      padding: 20px 25px;
      border-radius: 4px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }
    .doc-title {
      font-size: 24px;
      font-weight: 600;
      color: #0b63ce;
      margin-bottom: 20px;
    }
    .doc-content {
      font-size: 16px;
      color: #333;
    }
    @media screen and (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      .content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4>📘 Admin Guide</h4>
  <?php foreach ($docs as $index => $doc): ?>
    <a href="#" class="doc-link <?= $index === 0 ? 'active' : '' ?>" data-id="<?= $doc['id'] ?>">
      <?= htmlspecialchars($doc['title']) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="content">
  <div class="top-bar">
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Main Form</a>
  </div>

  <div class="doc-container" id="docContent">
    <?php if ($selected_doc): ?>
      <h3 class="doc-title"><?= htmlspecialchars($selected_doc['title']) ?></h3>
      <div class="doc-content"><?= $selected_doc['content'] ?></div>
    <?php else: ?>
      <p>No documentation available.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  const docs = <?= json_encode($docs); ?>;

  document.querySelectorAll(".doc-link").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      document.querySelectorAll(".doc-link").forEach(a => a.classList.remove("active"));
      el.classList.add("active");

      const docId = el.getAttribute("data-id");
      const selected = docs.find(d => d.id == docId);

      if (selected) {
        document.getElementById("docContent").innerHTML = `
          <h3 class="doc-title">${selected.title}</h3>
          <div class="doc-content">${selected.content}</div>
        `;
      }
    });
  });
</script>

</body>
</html>
