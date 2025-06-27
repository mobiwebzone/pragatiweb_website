<?php
require_once "conn_db.php";
session_start();

$message = "";

// Fetch doc_users for dropdown
$doc_users_result = $conn->query("SELECT DOC_USER_ID, DOC_USER_NAME FROM doc_users");

// Handle new submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "new") {
    $title = trim($_POST["title"]);
    $documentation = trim($_POST["documentation"]);
    $doc_user_id = intval($_POST["doc_user_id"]);

    if (!empty($title) && !empty($documentation) && $doc_user_id > 0) {
        $stmt = $conn->prepare("INSERT INTO admin_docs (title, content, created_at, doc_user_id) VALUES (?, ?, NOW(), ?)");
        if ($stmt) {
            $stmt->bind_param("ssi", $title, $documentation, $doc_user_id);
            $stmt->execute();
            $message = "<div class='alert alert-success'>Document saved successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error preparing statement.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>All fields are required.</div>";
    }
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "update") {
    $edit_id = intval($_POST["edit_id"]);
    $edit_title = trim($_POST["edit_title"]);
    $edit_documentation = trim($_POST["edit_documentation"]);
    $edit_doc_user_id = intval($_POST["edit_doc_user_id"]);

    if (!empty($edit_title) && !empty($edit_documentation) && $edit_doc_user_id > 0) {
        $stmt = $conn->prepare("UPDATE admin_docs SET title = ?, content = ?, doc_user_id = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssii", $edit_title, $edit_documentation, $edit_doc_user_id, $edit_id);
            $stmt->execute();
            $message = "<div class='alert alert-success'>Document updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error preparing update statement.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>All fields are required for update.</div>";
    }
}

$documents = $conn->query("SELECT * FROM admin_docs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        .card {
            padding: 20px;
        }
        .ck-editor__editable {
            min-height: 200px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow">
        <ul class="nav nav-tabs" id="docTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button">New Document</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button">Edit Document</button>
            </li>
        </ul>

        <div class="tab-content pt-2">
            <?= $message ?>
            <div class="tab-pane fade show active" id="new">
                <form method="POST">
                    <input type="hidden" name="action" value="new">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="doc_user_id" class="form-control" >
                            <option value="">-- Select User --</option>
                            <?php while ($u = $doc_users_result->fetch_assoc()): ?>
                                <option value="<?= $u['DOC_USER_ID'] ?>"><?= $u['DOC_USER_NAME'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documentation</label>
                        <textarea name="documentation" id="documentation" ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Main Form</a>
                </form>
            </div>

            <div class="tab-pane fade" id="edit">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <div class="mb-3">
                        <label class="form-label">Select Document</label>
                        <select class="form-control" name="edit_id" id="edit_id"  onchange="populateEditFields(this)">
                            <option value="">-- Select Document --</option>
                            <?php foreach ($documents as $doc): ?>
                                <option value="<?= $doc['id'] ?>"
                                    data-title="<?= htmlspecialchars($doc['title']) ?>"
                                    data-doc="<?= htmlspecialchars($doc['content']) ?>"
                                    data-user="<?= $doc['doc_user_id'] ?>">
                                    <?= htmlspecialchars($doc['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="edit_title" id="edit_title" >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="edit_doc_user_id" class="form-control" id="edit_doc_user_id" >
                            <option value="">-- Select User --</option>
                            <?php
                            $users_for_edit = $conn->query("SELECT DOC_USER_ID, DOC_USER_NAME FROM doc_users");
                            while ($user = $users_for_edit->fetch_assoc()):
                            ?>
                                <option value="<?= $user['DOC_USER_ID'] ?>"><?= $user['DOC_USER_NAME'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documentation</label>
                        <textarea name="edit_documentation" id="edit_documentation" ></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let docEditor1, docEditor2;

    ClassicEditor.create(document.querySelector('#documentation'))
        .then(editor => { docEditor1 = editor; })
        .catch(error => console.error(error));

    ClassicEditor.create(document.querySelector('#edit_documentation'))
        .then(editor => { docEditor2 = editor; })
        .catch(error => console.error(error));

    function populateEditFields(select) {
        const selected = select.options[select.selectedIndex];
        document.getElementById('edit_title').value = selected.getAttribute('data-title');
        document.getElementById('edit_doc_user_id').value = selected.getAttribute('data-user');

        const docContent = selected.getAttribute('data-doc');
        if (docEditor2) docEditor2.setData(docContent);
    }

    // Fade out message
    setTimeout(() => {
        const msg = document.querySelector('.alert');
        if (msg) msg.style.opacity = 0;
    }, 3000);
</script>

<script>
    document.getElementById("editForm").addEventListener("submit", function (e) {
        const title = document.getElementById("edit_title").value.trim();
        const content = editEditor.getData().trim(); // Get CKEditor content

        if (!title || !content) {
            e.preventDefault();
            alert("Please fill in both Title and Documentation fields.");
        } else {
            // Set hidden textarea content before submit
            document.getElementById("edit_documentation").value = content;
        }
    });
</script>

</body>
</html>
