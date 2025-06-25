<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response type to JSON
header('Content-Type: application/json');

// Database configuration
$host = '103.25.174.53';
$dbname = 'pragatiweb_mysql';
$username = 'root';
$password = 'India@123#';

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare test data
    $test_data = [
        ':name' => 'Test Name',
        ':mobile_no' => '1234567890',
        ':email' => 'test@example.com',
        ':institution_name' => 'Test Institution',
        ':country_id' => '1', // Replace with a valid COUNTRYID from your COUNTRIES table
        ':country' => 'Test Country',
        ':product_type' => 'Test Product',
        ':city' => 'Test City',
        ':message' => 'Test Message',
        ':entry_date' => date('Y-m-d H:i:s')
    ];

    // Prepare and execute SQL query
    $sql = "INSERT INTO Mysql_enquiries (Name, Mobile_no, email, Institution_name, country_id, country, Product_type, city, Message, entry_date) 
            VALUES (:name, :mobile_no, :email, :institution_name, :country_id, :country, :product_type, :city, :message, :entry_date)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($test_data);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Test insert successful']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: Could not insert data']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'General Error: ' . $e->getMessage()]);
}
?>