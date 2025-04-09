<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
require_once('includes/db_connection.php');

// Update query to match the column name in database
$query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
$pending_count = mysqli_fetch_assoc($result)['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="assets/images/logo.png" alt="Logo" class="logo">
                <h2>Admin Panel</h2>
            </div>
            
            <ul class="nav-links">
                <li>
                    <a href="javascript:void(0)" data-page="dashboard" class="active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-page="appointments">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Appointments</span>
                        <?php if($pending_count > 0): ?>
                            <span class="badge bg-danger"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-page="services">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-page="users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/images/admin-avatar.png" alt="Admin" width="32" height="32" class="rounded-circle me-2">
                        <strong>Admin</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#" data-page="profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#" data-page="settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="includes/logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <main class="content">
            <div id="dashboard" class="page active">
                <?php include('pages/dashboard.php'); ?>
            </div>
            <div id="appointments" class="page"></div>
            <div id="services" class="page"></div>
            <div id="users" class="page"></div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
