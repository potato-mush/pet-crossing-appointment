<?php
require_once(__DIR__ . '/../includes/db_connection.php');

// Check if connection exists
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Add error handling for the query
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard-container d-flex">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../../assets/images/logo.png" alt="Logo" class="logo">
                <h3>Admin Panel</h3>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="../index.php">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="appointments.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="services.php">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li>
                    <a href="users.php" class="active">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../../assets/images/avatar.jpg" alt="Admin" width="32" height="32" class="rounded-circle me-2">
                        <strong>Admin</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#" data-page="profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#" data-page="settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../includes/logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="content flex-grow-1">
            <div class="container-fluid">
                <h2 class="mb-4">Users Management</h2>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php 
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Joined Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="viewUser(<?php echo $row['id']; ?>)">View</button>
                                                <button class="btn btn-sm btn-danger" onclick="toggleUserStatus(<?php echo $row['id']; ?>)">
                                                    <?php echo $row['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center">No users found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewUserModalBody">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewUser(userId) {
            fetch(`view_user.php?id=${userId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('viewUserModalBody').innerHTML = data;
                    const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while fetching user details.");
                });
        }

        function toggleUserStatus(userId) {
            if (confirm("Are you sure you want to change the user's status?")) {
                fetch(`toggle_user_status.php?id=${userId}`, {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("User status updated successfully.");
                        location.reload();
                    } else {
                        alert("Failed to update user status.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating user status.");
                });
            }
        }
    </script>
</body>

<?php
// Close the database connection
mysqli_close($conn);
?>
