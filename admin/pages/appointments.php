<?php

require_once(__DIR__ . '/../includes/db_connection.php');

$query = "SELECT a.*, u.first_name, u.last_name, u.email 
          FROM appointments a 
          JOIN users u ON a.user_id = u.id 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid">
    <h2 class="mb-4">Appointment Management</h2>
    
    <div class="row">
        <!-- Calendar Section -->
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        
        <!-- Appointments List -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Appointments</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-light btn-sm" onclick="filterAppointments('today')">Today</button>
                        <button class="btn btn-outline-light btn-sm" onclick="filterAppointments('pending')">Pending</button>
                        <button class="btn btn-outline-light btn-sm" onclick="filterAppointments('all')">All</button>
                    </div>
                </div>
                <div class="card-body appointments-list" style="height: 600px; overflow-y: auto;">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="appointment-card mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></h6>
                                        <span class="badge bg-<?php echo getStatusColor($row['status']); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </div>
                                    <div class="appointment-details">
                                        <p class="mb-1"><i class="fas fa-calendar me-2"></i><?php echo date('F d, Y', strtotime($row['appointment_date'])); ?></p>
                                        <p class="mb-1"><i class="fas fa-clock me-2"></i><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></p>
                                        <p class="mb-1"><i class="fas fa-paw me-2"></i><?php echo $row['pet_name']; ?></p>
                                        <p class="mb-2"><i class="fas fa-tag me-2"></i><?php echo $row['service_type']; ?></p>
                                    </div>
                                    <div class="appointment-actions mt-2">
                                        <?php if($row['status'] === 'pending'): ?>
                                            <button class="btn btn-sm btn-success w-100 mb-1" onclick="updateStatus(<?php echo $row['id']; ?>, 'confirmed')">
                                                <i class="fas fa-check me-1"></i> Approve Appointment
                                            </button>
                                        <?php endif; ?>
                                        <div class="btn-group w-100">
                                            <button class="btn btn-sm btn-primary" onclick="viewAppointment(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="updateStatus(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-edit me-1"></i> Update
                                            </button>
                                            <?php if($row['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="updateStatus(<?php echo $row['id']; ?>, 'cancelled')">
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetails">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch($status) {
        case 'pending': return 'warning';
        case 'confirmed': return 'info';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,dayGridDay'
        },
        events: 'api/appointments.php?action=calendar_events',
        eventClick: function(info) {
            viewAppointment(info.event.id);
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        },
        height: 650
    });
    calendar.render();
});
</script>
