<?php
// Start output buffering to prevent stray output
ob_start();

// Enable error reporting, log errors, but disable display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// ini_set('error_log', '/var/log/php_errors.log'); // Update with actual path

// Start session for token management
session_start();

header('Content-Type: application/json'); // Set response type to JSON

// Database configuration
$serverName = '103.25.174.53';
$database = 'pragatiweb';
$username = 'sa';
$password = 'India@123456#';

try {
    // Create database connection using SQLSRV
    $connectionInfo = [
        "Database" => $database,
        "UID" => $username,
        "PWD" => $password,
        "CharacterSet" => "UTF-8"
    ];
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {
        throw new Exception('Connection failed: ' . print_r(sqlsrv_errors(), true));
    }

    // Check if form data is received and method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Error: Only POST method allowed']);
        ob_end_flush();
        exit;
    }

    // Validate token to prevent duplicate submissions
    $token = $_POST['token'] ?? '';
    if (!isset($_SESSION['form_token']) || $_SESSION['form_token'] !== $token) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or duplicate submission']);
        ob_end_flush();
        exit;
    }
    // Clear token after validation
    unset($_SESSION['form_token']);

    // Get and validate form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobileno = trim($_POST['mobileno'] ?? '');
    $institution_name = trim($_POST['institution_name'] ?? '');
    $product_type = trim($_POST['product_type'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $timestamp = date('Y-m-d H:i:s');
    $ORG_ID = 2;

    // Check for duplicate email
    $checkSql = "SELECT COUNT(*) AS count FROM enquiries WHERE email = ?";
    $checkParams = [$email];
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);
    if ($checkStmt === false) {
        throw new Exception('Error checking for duplicates: ' . print_r(sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    if ($row['count'] > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error: Email already exists']);
        sqlsrv_free_stmt($checkStmt);
        sqlsrv_close($conn);
        ob_end_flush();
        exit;
    }
    sqlsrv_free_stmt($checkStmt);

    // Prepare SQL query for insertion
    $sql = "INSERT INTO enquiries (ORG_ID, Name, Mobile_no, email, Institution_name, Product_type, city, Message, entry_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $ORG_ID,
        $name,
        $mobileno,
        $email,
        $institution_name,
        $product_type,
        $city,
        $message,
        $timestamp
    ];

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt === false) {
        throw new Exception('Query preparation failed: ' . print_r(sqlsrv_errors(), true));
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        throw new Exception('Query execution failed: ' . print_r(sqlsrv_errors(), true));
    }

    // Free statement and close connection
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    // Clear output buffer
    ob_end_clean();

    // Return JSON response (temporary for debugging)
    echo json_encode(['success' => true, 'message' => 'Form submitted successfully']);
    // Uncomment the redirect once confirmed working
    // header('Location: success.html');
    exit;

} catch (Exception $e) {
    // Clear output buffer
    ob_end_clean();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    if (isset($stmt)) {
        sqlsrv_free_stmt($stmt);
    }
    if (isset($conn)) {
        sqlsrv_close($conn);
    }
    exit;
}
?>