<?php
require_once "conn_db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu_name = trim($_POST["menu_name"] ?? '');
    $menu_link = trim($_POST["menu_link"] ?? ''); // Allow empty link

    if (empty($menu_name)) {
        echo "empty";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO menus (menu_name, menu_link) VALUES (?, ?)");
    $stmt->bind_param("ss", $menu_name, $menu_link);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
