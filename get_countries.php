<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = '103.25.174.53';
$dbname = 'pragatiweb_mysql';
$username = 'root';
$password = 'India@123#';

header('Content-Type: application/json'); // Set response type to JSON

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch countries where ISDELETED = 0
    $sql = "SELECT COUNTRYID, COUNTRY FROM COUNTRIES WHERE ISDELETED = 0 ORDER BY COUNTRY";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($countries);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'General Error: ' . $e->getMessage()]);
}
?>