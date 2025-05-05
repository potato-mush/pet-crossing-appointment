<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pet_clinic';

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $username, $password, $database);
    // Set charset to utf8mb4
    mysqli_set_charset($conn, 'utf8mb4');
    
    // Test the connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    // Don't show detailed error messages in production
    die('Database connection error: ' . $e->getMessage());
}
?>
