<?php
// get_product_types.php
header('Content-Type: application/json');

try {
    // Database connection
    $dbconn = pg_connect("host=103.25.174.53 dbname=mobiwebzone user=admin password=admin123");
    if ($dbconn === false) {
        throw new Exception('Failed to connect to PostgreSQL');
    }

    // Query to fetch product types
    $query = "SELECT code_dtl_id, code_dtl_desc 
              FROM public.mobiapp_code_details 
              ORDER BY code_dtl_id ASC";
    $result = pg_query($dbconn, $query);
    if ($result === false) {
        throw new Exception('Query failed: ' . pg_last_error($dbconn));
    }

    $product_types = [];
    while ($row = pg_fetch_assoc($result)) {
        $product_types[] = [
            'id' => $row['code_dtl_id'],
            'desc' => $row['code_dtl_desc']
        ];
    }

    // Clean up
    pg_free_result($result);
    pg_close($dbconn);

    echo json_encode($product_types);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>