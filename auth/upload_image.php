<?php
$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!empty($_FILES['file']['name'])) {
    $filename = basename($_FILES['file']['name']);
    $targetFile = $targetDir . time() . "_" . preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo json_encode(['location' => $targetFile]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Image upload failed.']);
    }
}
?>
