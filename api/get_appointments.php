<?php
require_once('../includes/Database.php');
require_once('../includes/Appointment.php');

$database = new Database();
$db = $database->connect();

$appointment = new Appointment($db);

try {
    $appointments = $appointment->getBookedAppointments();
    echo json_encode($appointments);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
