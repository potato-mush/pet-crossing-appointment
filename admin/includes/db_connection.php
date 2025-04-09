<?php
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
} catch (Exception $e) {
    error_log($e->getMessage());
    // Don't show detailed error messages in production
    die('Error connecting to database');
}
?>
