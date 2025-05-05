document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const userId = document.getElementById('user_id').value; // Assume user ID is available in a hidden input field

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        selectConstraint: {
            start: new Date(new Date().setDate(new Date().getDate() + 1)).toISOString(),
        },
        validRange: {
            start: new Date(new Date().setDate(new Date().getDate() + 1)).toISOString()
        },
        select: function(info) {
            document.getElementById('appointment_date').value = info.startStr;
            updateTimeSlots();
        },
        events: async function(fetchInfo, successCallback, failureCallback) {
            try {
                const response = await fetch('api/get_appointments.php');
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                const events = data.map(appointment => ({
                    title: `${appointment.pet_name} (${appointment.service_type})`,
                    start: `${appointment.appointment_date}T${appointment.appointment_time}`,
                    color: appointment.user_id != userId ? 'gray' : (appointment.status === 'pending' ? 'orange' : 'green')
                }));
                successCallback(events);
            } catch (error) {
                console.error('Error fetching appointments:', error);
                failureCallback(error);
            }
        }
    });
    calendar.render();

    // Form elements
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const appointmentForm = document.getElementById('appointmentForm');

    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateInput.min = tomorrow.toISOString().split('T')[0];

    // Event listeners
    serviceSelect.addEventListener('change', updateTimeSlots);
    dateInput.addEventListener('change', updateTimeSlots);

    appointmentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form
        const serviceSelect = document.getElementById('service');
        const serviceId = serviceSelect.value; // Get service ID
        const petName = document.getElementById('pet_name').value;
        const date = document.getElementById('appointment_date').value;
        const time = document.getElementById('appointment_time').value;

        if (!serviceId || !petName || !date || !time) {
            showError('Please fill in all required fields');
            return;
        }

        // Validate date is not in past
        const selectedDate = new Date(`${date}T${time}`);
        if (selectedDate < new Date()) {
            showError('Cannot book appointments in the past');
            return;
        }

        try {
            const formData = new FormData(this);
            formData.set('service', serviceId); // Send service ID
            const response = await fetch('api/book_appointment.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccess(result.message);
                calendar.refetchEvents();
                this.reset();
            } else {
                showError(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showError('An error occurred while booking the appointment');
        }
    });
});

async function updateTimeSlots() {
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');

    timeSelect.innerHTML = '<option value="">Select a time</option>';
    timeSelect.disabled = true;

    if (!serviceSelect.value || !dateInput.value) {
        return;
    }

    try {
        const url = `api/get_available_slots.php?date=${dateInput.value}&service_id=${serviceSelect.value}`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        if (!Array.isArray(data) || data.length === 0) {
            timeSelect.innerHTML = '<option value="">No available slots</option>';
            return;
        }

        timeSelect.disabled = false;
        data.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.time;
            option.textContent = slot.formatted_time;
            timeSelect.appendChild(option);
        });
    } catch (error) {
        timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        showError(error.message || 'Failed to load available time slots');
        console.error('Error:', error);
    }
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = message;
    
    const form = document.getElementById('appointmentForm');
    form.insertBefore(errorDiv, form.firstChild);
    
    setTimeout(() => errorDiv.remove(), 5000);
}

function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success';
    successDiv.textContent = message;
    
    const form = document.getElementById('appointmentForm');
    form.insertBefore(successDiv, form.firstChild);
    
    setTimeout(() => successDiv.remove(), 5000);
}
