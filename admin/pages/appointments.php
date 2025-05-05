<?php

require_once(__DIR__ . '/../includes/db_connection.php');

$query = "SELECT 
    a.appointment_id, 
    a.appointment_date, 
    a.appointment_time, 
    a.status,
    a.service_type,
    a.pet_name,
    u.first_name,
    u.last_name,
    u.email 
    FROM appointments a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = mysqli_query($conn, $query);
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
                    <a href="appointments.php" class="active">
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
                <h2 class="mb-4">Appointment Management</h2>
                
                <div class="row">
                    <!-- Calendar Section -->
                    <div class="col-lg-7 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div id="calendar-loading-spinner" class="text-center my-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div id="calendar" style="min-height: 600px;"></div>
                                <p id="calendar-error" class="text-danger text-center mt-3" style="display: none;">
                                    Failed to load the calendar. Please try again later.
                                </p>
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
                                                    <?php if ($row['status'] === 'pending'): ?>
                                                        <button class="btn btn-sm btn-success w-100 mb-1" 
                                                                onclick="updateAppointmentStatus(<?php echo htmlspecialchars($row['appointment_id'], ENT_QUOTES, 'UTF-8'); ?>, 'confirmed')">
                                                            <i class="fas fa-check me-1"></i> Approve
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-danger w-100" 
                                                            onclick="updateAppointmentStatus(<?php echo htmlspecialchars($row['appointment_id'], ENT_QUOTES, 'UTF-8'); ?>, 'cancelled')">
                                                        <i class="fas fa-times me-1"></i> Cancel
                                                    </button>
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
        </main>
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

    <!-- Add Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Appointment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select id="newStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update</button>
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

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const loadingSpinner = document.getElementById('calendar-loading-spinner');
        const calendarError = document.getElementById('calendar-error');

        if (!calendarEl) {
            console.error('Calendar element not found');
            calendarError.style.display = 'block';
            return;
        }

        console.log('Initializing FullCalendar...');
        try {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/pet-crossing-appointment/admin/api/appointments.php?action=calendar_events', // Corrected path
                height: 600,
                themeSystem: 'bootstrap5',
                eventSourceSuccess: function(content) {
                    console.log('Fetched events:', content);
                },
                eventSourceFailure: function(error) {
                    console.error('Calendar event fetch error:', error);
                    calendarError.style.display = 'block';
                },
                loading: function(isLoading) {
                    if (isLoading) {
                        console.log('Calendar is loading...');
                        loadingSpinner.style.display = 'block';
                    } else {
                        console.log('Calendar finished loading.');
                        loadingSpinner.style.display = 'none';
                    }
                }
            });

            calendar.render();
            console.log('FullCalendar rendered successfully.');
        } catch (error) {
            console.error('Error initializing FullCalendar:', error);
            calendarError.style.display = 'block';
        }
    });

    function filterAppointments(filter) {
        fetch(`../api/appointments.php?action=filter&filter=${filter}`) // Corrected the path
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const appointmentsList = document.querySelector('.appointments-list');
                appointmentsList.innerHTML = '';
                data.forEach(appointment => {
                    const card = `
                        <div class="appointment-card mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">${appointment.first_name} ${appointment.last_name}</h6>
                                        <span class="badge bg-${appointment.status_color}">
                                            ${appointment.status}
                                        </span>
                                    </div>
                                    <div class="appointment-details">
                                        <p class="mb-1"><i class="fas fa-calendar me-2"></i>${appointment.appointment_date}</p>
                                        <p class="mb-1"><i class="fas fa-clock me-2"></i>${appointment.appointment_time}</p>
                                        <p class="mb-1"><i class="fas fa-paw me-2"></i>${appointment.pet_name}</p>
                                        <p class="mb-2"><i class="fas fa-tag me-2"></i>${appointment.service_type}</p>
                                    </div>
                                    <div class="appointment-actions mt-2">
                                        ${appointment.status === 'pending' ? `
                                            <button class="btn btn-sm btn-success w-100 mb-1" 
                                                    onclick="updateAppointmentStatus(${appointment.appointment_id}, 'confirmed')">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>` : ''}
                                        <button class="btn btn-sm btn-danger w-100" 
                                                onclick="updateAppointmentStatus(${appointment.appointment_id}, 'cancelled')">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    appointmentsList.innerHTML += card;
                });
            })
            .catch(error => console.error('Error fetching filtered appointments:', error));
    }
    </script>
    <script src="../assets/js/dashboard.js"></script>
</body>

