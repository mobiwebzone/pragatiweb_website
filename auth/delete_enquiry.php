<?php
require_once "conn_db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM mysql_enquiries WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: enquiry_details.php");
    } else {
        echo "Failed to delete enquiry.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
