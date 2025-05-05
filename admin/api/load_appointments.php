<?php
require_once(__DIR__ . '/../includes/db_connection.php'); // Ensure this path is correct

$query = "SELECT id, appointment_date, appointment_time, CONCAT(first_name, ' ', last_name) AS title 
          FROM appointments 
          JOIN users ON appointments.user_id = users.id 
          WHERE status != 'cancelled'";
$result = mysqli_query($conn, $query);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['appointment_date'] . 'T' . $row['appointment_time']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
