<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../includes/db_connection.php');

try {
    $query = "SELECT 
        a.appointment_id, 
        a.appointment_date, 
        a.appointment_time, 
        a.status,
        a.service_type,
        a.pet_name,
        CONCAT(u.first_name, ' ', u.last_name) as client_name
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.status != 'cancelled'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $start = $row['appointment_date'] . ' ' . $row['appointment_time'];
        
        $events[] = [
            'id' => $row['appointment_id'],
            'title' => "{$row['client_name']} - {$row['pet_name']}",
            'start' => date('Y-m-d\TH:i:s', strtotime($start)),
            'backgroundColor' => getStatusColor($row['status']),
            'borderColor' => getStatusColor($row['status'])
        ];
    }
    
    echo json_encode($events);

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getStatusColor($status) {
    return match ($status) {
        'pending' => '#ffc107',
        'confirmed' => '#0dcaf0',
        'completed' => '#198754',
        'cancelled' => '#dc3545',
        default => '#6c757d',
    };
}
?>
