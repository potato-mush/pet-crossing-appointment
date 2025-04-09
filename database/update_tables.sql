ALTER TABLE appointments
CHANGE appointment_status status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending';
