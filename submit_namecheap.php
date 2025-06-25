<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = '127.0.0.1';
$dbname = 'mobizryn_mobiwebzone';
$username = 'mobizryn_admin';
$password = 'Mahima_1926#';

header('Content-Type: application/json'); // Set response type to JSON

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form data is received
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Error: Only POST method allowed']);
        exit;
    }

    // Get and validate form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobileno = trim($_POST['mobileno'] ?? '');
    $institution_name = trim($_POST['institution_name'] ?? '');
    $country_id = trim($_POST['country_id'] ?? ''); // New field
    $country_name = trim($_POST['country_name'] ?? ''); // New field
    $product_type = trim($_POST['product_type'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $timestamp = date('Y-m-d H:i:s');
        

    // Prepare and execute SQL query
    $sql = "INSERT INTO Mysql_enquiries (Name, Mobile_no, email, Institution_name, country_id, country, Product_type, city, Message, entry_date) 
            VALUES (:name, :mobile_no, :email, :institution_name, :country_id, :country, :product_type, :city, :message, :entry_date)";
    
    $stmt = $pdo->prepare($sql);
    
    $params = [
        ':name' => $name,
        ':mobile_no' => $mobileno,
        ':email' => $email,
        ':institution_name' => $institution_name,
        ':country_id' => $country_id, // New field
        ':country' => $country_name,  // New field
        ':product_type' => $product_type,
        ':city' => $city,
        ':message' => $message,
        ':entry_date' => $timestamp
    ];

    $result = $stmt->execute($params);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Form submitted successfully']);
        exit;
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error: Could not insert data']);
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'General Error: ' . $e->getMessage()]);
    exit;
}
?>