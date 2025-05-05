<?php
session_start();
require_once('../includes/db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'view') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $query = "SELECT a.*, u.first_name, u.last_name, u.email 
                  FROM appointments a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE a.id = '$id'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            echo json_encode(['error' => mysqli_error($conn)]);
            exit();
        }
        
        $data = mysqli_fetch_assoc($result);
        if (!$data) {
            echo json_encode(['error' => 'Appointment not found']);
            exit();
        }
        
        echo json_encode($data);
        exit();
    }

    if (isset($_GET['action']) && $_GET['action'] === 'calendar_events') {
        $query = "SELECT 
            a.id, 
            a.appointment_date, 
            a.appointment_time, 
            a.status, 
            a.service_type, 
            a.pet_name,
            u.first_name, 
            u.last_name 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => mysqli_error($conn)]);
            exit();
        }
        
        $events = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['appointment_date']) && !empty($row['appointment_time'])) {
                $color = match ($row['status']) {
                    'pending' => '#ffc107',
                    'confirmed' => '#0dcaf0',
                    'completed' => '#198754',
                    'cancelled' => '#dc3545',
                    default => '#6c757d',
                };

                $datetime = $row['appointment_date'] . ' ' . $row['appointment_time'];
                $endtime = date('Y-m-d H:i:s', strtotime($datetime . ' +1 hour'));

                $events[] = [
                    'id' => $row['id'],
                    'title' => "{$row['first_name']} {$row['last_name']} - {$row['pet_name']}",
                    'start' => date('c', strtotime($datetime)),
                    'end' => date('c', strtotime($endtime)),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'status' => $row['status'],
                        'service' => $row['service_type']
                    ]
                ];
            }
        }
        
        echo json_encode($events);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'update_status') {
        $id = mysqli_real_escape_string($conn, $data['id']);
        $status = mysqli_real_escape_string($conn, $data['status']);
        
        $query = "UPDATE appointments SET status = '$status' WHERE id = '$id'";
        $success = mysqli_query($conn, $query);
        
        echo json_encode(['success' => $success]);
    }
}
?>
