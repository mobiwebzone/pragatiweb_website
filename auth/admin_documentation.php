<?php
require_once "conn_db.php";
session_start();
$message = "";
$documents = [];

// Fetch document titles
$result = $conn->query("SELECT id, title FROM admin_docs");
while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['fetchDoc'])) {
    $tab = $_POST["tab"] ?? "new";
    $title = trim($_POST["title"]);
    $documentation = trim($_POST["documentation"]);
    $doc_id = $_POST["doc_id"] ?? null;

    if (!empty($title) && !empty($documentation)) {
        if ($tab === "edit" && $doc_id) {
            $stmt = $conn->prepare("UPDATE admin_docs SET title = ?, content = ?, created_at = NOW() WHERE id = ?");
                if (!$stmt) {
                die("Prepare failed: " . $conn->error); // This will show you what's wrong
                }
            $stmt->bind_param("ssi", $title, $documentation, $doc_id);
            $stmt->execute();
            $message = "<div class='alert alert-success'>Document updated successfully!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO admin_docs (title, content, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $title, $documentation);
            $stmt->execute();
            $message = "<div class='alert alert-success'>Document saved successfully!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Please fill in both Title and Documentation fields.</div>";
    }
}

// Handle AJAX request for fetching document content
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fetchDoc"])) {
    $docId = intval($_POST["fetchDoc"]);
    $stmt = $conn->prepare("SELECT id, title, content FROM admin_docs WHERE id = ?");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    $res = $stmt->get_result();
    $doc = $res->fetch_assoc();
    echo json_encode($doc);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
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
        <h4 class="mb-4">Admin Documentation</h4>
        <?php if (!empty($message)): ?>
            <div id="messageBox"><?php echo $message; ?></div>
            <script>
                setTimeout(() => {
                    document.getElementById('messageBox').style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="docTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab">New Document</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">Edit Document</button>
            </li>
        </ul>

        <div class="tab-content" id="tabContent">
            <!-- New Document Tab -->
            <div class="tab-pane fade show active" id="new" role="tabpanel">
                <form method="POST">
                    <input type="hidden" name="tab" value="new">
                    <div class="mb-3">
                        <label for="title" class="form-label">Document Title</label>
                        <input type="text" class="form-control" name="title"  />
                    </div>
                    <div class="mb-3">
                        <label for="documentation" class="form-label">Documentation</label>
                        <textarea name="documentation" id="documentation" ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Main Form</a>
                </form>
            </div>

            <!-- Edit Document Tab -->
            <div class="tab-pane fade" id="edit" role="tabpanel">
                <form method="POST" id="editForm">
                    <input type="hidden" name="tab" value="edit">
                    <input type="hidden" name="doc_id" id="doc_id">
                    <div class="mb-3">
                        <label for="doc_select" class="form-label">Select Document</label>
                        <select class="form-select" id="doc_select">
                            <option value="">-- Choose Document --</option>
                            <?php foreach ($documents as $doc): ?>
                                <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Document Title</label>
                        <input type="text" class="form-control" name="title" id="edit_title"  />
                    </div>
                    <div class="mb-3">
                        <label for="edit_documentation" class="form-label">Documentation</label>
                        <textarea name="documentation" id="edit_documentation" ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Back to Main Form</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let newEditor, editEditor;

    ClassicEditor.create(document.querySelector('#documentation'))
        .then(editor => newEditor = editor)
        .catch(err => console.error(err));

    ClassicEditor.create(document.querySelector('#edit_documentation'))
        .then(editor => editEditor = editor)
        .catch(err => console.error(err));

    document.getElementById("doc_select").addEventListener("change", function () {
        const docId = this.value;
        if (!docId) return;

        const formData = new FormData();
        formData.append("fetchDoc", docId);

        fetch("", { method: "POST", body: formData })
            .then(res => res.json())
            .then(data => {
                document.getElementById("doc_id").value = data.id;
                document.getElementById("edit_title").value = data.title;
                editEditor.setData(data.content);
            });
    });
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
