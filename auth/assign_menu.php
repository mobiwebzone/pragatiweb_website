<?php
require_once "conn_db.php";

$role_id = $_POST['role_id'];
$menu_ids = $_POST['menu_ids']; // array

$conn->query("DELETE FROM role_menu WHERE role_id = $role_id");

foreach ($menu_ids as $menu_id) {
    $stmt = $conn->prepare("INSERT INTO role_menu (role_id, menu_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $role_id, $menu_id);
    $stmt->execute();
}
echo "Menus assigned successfully.";
?>
