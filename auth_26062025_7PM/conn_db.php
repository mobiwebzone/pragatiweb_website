<?php
$servername = "103.25.174.53";
$username = "root";
$password = "India@123#";
$database = "pragatiweb_mysql";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
