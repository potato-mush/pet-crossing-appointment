document.addEventListener('DOMContentLoaded', function() {
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not defined. Ensure the library is loaded correctly.');
        return;
    }

    // Initialize the calendar
    initializeCalendar();

    // Event listener for sidebar navigation
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.dataset.page) {
                e.preventDefault();
                // Remove active class from all links
                document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
                // Load the page content via AJAX
                loadPage(this.dataset.page);
            }
        });
    });
});

let calendar; // Declare the calendar globally

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }

    const loadingSpinner = document.getElementById('calendar-loading-spinner');
    const calendarError = document.getElementById('calendar-error');

    console.log('Initializing FullCalendar...');
    try {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '../api/appointments.php?action=calendar_events', // Corrected path
            height: 600,
            themeSystem: 'bootstrap5',
            loading: function(isLoading) {
                if (isLoading) {
                    console.log('Calendar is loading...');
                    loadingSpinner.style.display = 'block';
                } else {
                    console.log('Calendar finished loading.');
                    loadingSpinner.style.display = 'none';
                }
            },
            eventSourceSuccess: function(content) {
                console.log('Fetched events:', content);
            },
            eventSourceFailure: function(error) {
                console.error('Error fetching events:', error);
                calendarError.style.display = 'block';
            }
        });

        calendar.render();
        console.log('FullCalendar rendered successfully.');
    } catch (error) {
        console.error('Error initializing FullCalendar:', error);
        calendarError.style.display = 'block';
    }
}

function loadPage(page) {
    fetch(`${page}.php`)
        .then(response => response.text())
        .then(html => {
            // Hide all pages
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            
            // Show and update selected page
            const targetPage = document.getElementById(page);
            if (targetPage) {
                targetPage.innerHTML = html;
                targetPage.classList.add('active');
            }
        })
        .catch(error => console.error('Error loading page:', error));
}

function viewAppointment(id) {
    fetch(`api/appointments.php?action=view&id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Display appointment details in a modal
            // Implementation depends on your UI framework
        });
}

let currentAppointmentId = null;

function openStatusUpdateModal(button) {
    currentAppointmentId = button.dataset.id;
    const currentStatus = button.dataset.status;
    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    document.getElementById('newStatus').value = currentStatus;
    modal.show();
}

function submitStatusUpdate() {
    if (!currentAppointmentId) return;
    
    const newStatus = document.getElementById('newStatus').value;
    updateAppointmentStatus(currentAppointmentId, newStatus);
}

function updateAppointmentStatus(id, status) {
    if (!id || !status) {
        console.error('Missing appointment ID or status');
        return;
    }

    fetch('../api/appointments.php', { // Corrected the path
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'update_status',
            id: id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the appointments list dynamically
            filterAppointments('all'); // Reload all appointments to reflect the updated status
            
            // Reload the calendar
            calendar.refetchEvents();

            // Show success message
            showToast('Success', 'Appointment status updated successfully');
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error', error.message);
    });
}

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
                if (filter === 'pending' && appointment.status !== 'pending') return; // Ensure only pending appointments are shown
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

            // Reload the calendar
            calendar.refetchEvents();
        })
        .catch(error => console.error('Error fetching filtered appointments:', error));
}

function viewAppointmentDetails(id) {
    if (!id) return;
    fetch(`api/appointments.php?action=view&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            document.getElementById('appointmentDetails').innerHTML = `
                <p><strong>Client:</strong> ${data.first_name} ${data.last_name}</p>
                <p><strong>Pet:</strong> ${data.pet_name}</p>
                <p><strong>Service:</strong> ${data.service_type}</p>
                <p><strong>Date:</strong> ${data.appointment_date}</p>
                <p><strong>Time:</strong> ${data.appointment_time}</p>
                <p><strong>Status:</strong> ${data.status}</p>
                <p><strong>Notes:</strong> ${data.notes || 'No notes'}</p>
            `;
            modal.show();
        });
}

function addService() {
    const modal = new bootstrap.Modal(document.getElementById('addServiceModal'));
    modal.show();
}

function saveService() {
    const form = document.getElementById('addServiceForm');
    
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);
    
    fetch('api/services.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addServiceModal'));
            modal.hide();
            form.reset();
            form.classList.remove('was-validated');
            loadPage('services');
            showToast('Success', 'Service added successfully!', 'success');
        } else {
            showToast('Error', data.message || 'Failed to add service', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error', 'An unexpected error occurred', 'error');
    });
}

function editService(id) {
    fetch(`api/services.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('addServiceForm');
            form.querySelector('[name="action"]').value = 'edit';
            form.querySelector('[name="id"]').value = data.id;
            form.querySelector('[name="name"]').value = data.name;
            form.querySelector('[name="description"]').value = data.description;
            form.querySelector('[name="duration"]').value = data.duration;
            form.querySelector('[name="price"]').value = data.price;
            
            const modal = new bootstrap.Modal(document.getElementById('addServiceModal'));
            modal.show();
        });
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('api/services.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPage('services');
                showToast('Success', 'Service deleted successfully!', 'success');
            } else {
                showToast('Error', data.message || 'Failed to delete service', 'error');
            }
        });
    }
}

function showToast(title, message, type = 'info') {
    // You can implement your preferred toast notification here
    alert(`${title}: ${message}`);
}
