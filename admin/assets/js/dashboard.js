document.addEventListener('DOMContentLoaded', function() {


    // Handle sidebar navigation
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            if(this.dataset.page) {
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

function loadPage(page) {
    fetch(`pages/${page}.php`)
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

    fetch('../../api/appointments.php', {
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
            // Close modal if open
            const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
            if (modal) modal.hide();
            
            // Refresh the page to show updated status
            loadPage('appointments');
            
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
