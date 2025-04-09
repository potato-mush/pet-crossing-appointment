<?php
require_once(__DIR__ . '/../includes/db_connection.php');

// Get total appointments
$query = "SELECT COUNT(*) as total FROM appointments";
$result = mysqli_query($conn, $query);
$total_appointments = mysqli_fetch_assoc($result)['total'];

// Get today's appointments
$query = "SELECT COUNT(*) as today FROM appointments WHERE DATE(appointment_date) = CURDATE()";
$result = mysqli_query($conn, $query);
$today_appointments = mysqli_fetch_assoc($result)['today'];

// Get total users
$query = "SELECT COUNT(*) as total FROM users";
$result = mysqli_query($conn, $query);
$total_users = mysqli_fetch_assoc($result)['total'];
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Appointments</h5>
                    <h2><?php echo $total_appointments; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Appointments</h5>
                    <h2><?php echo $today_appointments; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Pet Name</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM appointments ORDER BY appointment_date DESC LIMIT 5";
                                $result = mysqli_query($conn, $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['appointment_date'] . "</td>";
                                    echo "<td>" . $row['pet_name'] . "</td>";
                                    echo "<td>" . $row['service_type'] . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
