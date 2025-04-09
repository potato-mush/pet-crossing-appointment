<?php
session_start();
require_once('../includes/db_connection.php');

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
        echo json_encode(mysqli_fetch_assoc($result));
    }

    if (isset($_GET['action']) && $_GET['action'] === 'calendar_events') {
        $query = "SELECT a.*, u.first_name, u.last_name 
                  FROM appointments a 
                  JOIN users u ON a.user_id = u.id";
        $result = mysqli_query($conn, $query);
        
        $events = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $color = '';
            switch($row['status']) {
                case 'pending': $color = '#ffc107'; break;
                case 'confirmed': $color = '#0dcaf0'; break;
                case 'completed': $color = '#198754'; break;
                case 'cancelled': $color = '#dc3545'; break;
            }
            
            $events[] = [
                'id' => $row['id'],
                'title' => $row['first_name'] . ' ' . $row['last_name'] . ' - ' . $row['service_type'],
                'start' => $row['appointment_date'] . 'T' . $row['appointment_time'],
                'backgroundColor' => $color,
                'borderColor' => $color
            ];
        }
        
        header('Content-Type: application/json');
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
