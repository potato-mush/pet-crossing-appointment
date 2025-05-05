<?php
require_once(__DIR__ . '/../includes/db_connection.php');

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get services
$query = "SELECT * FROM services ORDER BY name";
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
                    <a href="services.php" class="active">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
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
                <h2 class="mb-4">Services Management</h2>
                
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary mb-3" onclick="addService()">Add New Service</button>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['description']; ?></td>
                                        <td><?php echo $row['duration']; ?> mins</td>
                                        <td>₱<?php echo $row['price']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="editService(<?php echo $row['id']; ?>)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteService(<?php echo $row['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Service Modal -->
            <div class="modal fade" id="addServiceModal" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle me-2"></i>Add New Service
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addServiceForm" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="id" value="">
                                <div class="mb-3">
                                    <label class="form-label required">Service Name</label>
                                    <input type="text" class="form-control" name="name" required
                                           minlength="3" maxlength="100">
                                    <div class="invalid-feedback">
                                        Please enter a valid service name (3-100 characters)
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"
                                              maxlength="500" placeholder="Enter service description"></textarea>
                                    <div class="form-text">Maximum 500 characters</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Duration (minutes)</label>
                                        <input type="number" class="form-control" name="duration"
                                               required min="15" max="480">
                                        <div class="invalid-feedback">
                                            Duration must be between 15 and 480 minutes
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Price (₱)</label>
                                        <input type="number" class="form-control" name="price"
                                               required min="0" step="0.01">
                                        <div class="invalid-feedback">
                                            Please enter a valid price
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveService()">
                                <i class="fas fa-save me-2"></i>Save Service
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
