<?php
session_start();
require_once('../includes/Database.php');
require_once('../includes/Appointment.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $database = new Database();
    $db = $database->connect();
    $appointment = new Appointment($db);

    // Validate inputs
    if (empty($_POST['service']) || empty($_POST['pet_name']) || 
        empty($_POST['appointment_date']) || empty($_POST['appointment_time'])) {
        throw new Exception('All fields are required');
    }

    // Get service details
    $stmt = $db->prepare("SELECT duration FROM services WHERE id = ?");
    $stmt->execute([$_POST['service']]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        throw new Exception('Invalid service selected');
    }

    // Check availability
    if (!$appointment->checkAvailability(
        $_POST['appointment_date'], 
        $_POST['appointment_time'], 
        $service['duration']
    )) {
        throw new Exception('This time slot is no longer available');
    }

    // Create appointment
    $success = $appointment->create(
        $_SESSION['user_id'],
        $_POST['service'],
        $_POST['pet_name'],
        $_POST['appointment_date'],
        $_POST['appointment_time'],
        $service['duration'],
        $_POST['notes'] ?? ''
    );

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Appointment booked successfully' : 'Failed to book appointment'
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
