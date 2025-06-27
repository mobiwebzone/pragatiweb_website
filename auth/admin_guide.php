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
      background-color: #ffffff;
    }
    .sidebar {
      height: 100vh;
      width: 250px;
      position: fixed;
      background-color: #0b63ce;
      padding-top: 20px;
      overflow-y: auto;
    }
    .sidebar h4 {
      color: white;
      text-align: center;
      margin-bottom: 20px;
    }
    .sidebar a {
      padding: 10px 20px;
      display: block;
      color: white;
      text-decoration: none;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #074aa1;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
    }
   .doc-container {
            background-color: #e9f2fb; /* Very light blue */
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            min-height: 500px;
            }

    .doc-title {
      margin-bottom: 20px;
      color: #0b63ce;
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
  <h4>📚 Admin Guide</h4>
  <?php foreach ($docs as $index => $doc): ?>
    <a href="#" class="doc-link <?= $index === 0 ? 'active' : '' ?>" data-id="<?= $doc['id'] ?>">
      <?= htmlspecialchars($doc['title']) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="content">
  <div class="doc-container" id="docContent">
    <?php if ($selected_doc): ?>
      <h3 class="doc-title"><?= htmlspecialchars($selected_doc['title']) ?></h3>
      <div><?= $selected_doc['content'] ?></div>
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
          <div>${selected.content}</div>
        `;
      }
    });
  });
</script>

</body>
</html>
