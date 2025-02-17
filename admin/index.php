<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
require_once('includes/db_connection.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/dashboard.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#" data-page="appointments">Appointments</a></li>
                <li><a href="#" data-page="customers">Customers</a></li>
                <li><a href="#" data-page="services">Services</a></li>
                <li><a href="includes/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <div id="appointments" class="page active">
                <!-- Appointments content will be loaded here -->
            </div>
            <div id="customers" class="page">
                <!-- Customers content will be loaded here -->
            </div>
            <div id="services" class="page">
                <!-- Services content will be loaded here -->
            </div>
        </main>
    </div>
</body>
</html>
