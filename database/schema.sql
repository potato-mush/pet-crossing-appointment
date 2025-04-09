CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pet_name VARCHAR(100) NOT NULL,
    service_type INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration INT NOT NULL DEFAULT 30,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_type) REFERENCES services(id)
);

CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS time_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_time TIME NOT NULL,
    max_appointments INT DEFAULT 1,
    is_active BOOLEAN DEFAULT true,
    UNIQUE KEY unique_slot (slot_time)
);

-- Insert default time slots (8 AM to 5 PM, 30-minute intervals)
INSERT IGNORE INTO time_slots (slot_time) VALUES 
('08:00:00'), ('08:30:00'), ('09:00:00'), ('09:30:00'),
('10:00:00'), ('10:30:00'), ('11:00:00'), ('11:30:00'),
('13:00:00'), ('13:30:00'), ('14:00:00'), ('14:30:00'),
('15:00:00'), ('15:30:00'), ('16:00:00'), ('16:30:00');

-- Insert default services
INSERT INTO services (name, description, duration, price, status) VALUES 
('General Checkup', 'Comprehensive health examination for your pet', 30, 50.00, 'active'),
('Vaccination', 'Essential vaccines and immunizations', 30, 75.00, 'active'),
('Grooming', 'Professional pet grooming service', 60, 80.00, 'active'),
('Dental Cleaning', 'Complete dental care and cleaning', 45, 120.00, 'active');
