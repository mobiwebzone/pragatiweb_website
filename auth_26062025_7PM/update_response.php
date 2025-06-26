<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $response = trim($_POST['response']);

    if (!empty($response)) {
        $stmt = $conn->prepare("UPDATE mysql_enquiries SET response = ? WHERE id = ?");
        $stmt->bind_param("si", $response, $id);

        if ($stmt->execute()) {
            header("Location: enquiry_details.php");
        } else {
            echo "Failed to update response.";
        }
        $stmt->close();
    } else {
        echo "Response cannot be empty.";
    }
    $conn->close();
}
?>
