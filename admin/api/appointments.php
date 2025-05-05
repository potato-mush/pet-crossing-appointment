<?php
session_start();
require_once('../includes/db_connection.php');

// Improve headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'calendar_events') {
        try {
            $query = "SELECT 
                appointment_id,
                appointment_date,
                appointment_time,
                status,
                service_type,
                pet_name
                FROM appointments 
                WHERE status != 'cancelled'
                ORDER BY appointment_date ASC, appointment_time ASC";

            $result = mysqli_query($conn, $query);
            if (!$result) {
                throw new Exception(mysqli_error($conn));
            }

            $events = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $datetime = $row['appointment_date'] . ' ' . $row['appointment_time'];
                $events[] = [
                    'id' => $row['appointment_id'],
                    'title' => $row['pet_name'] . ' - ' . $row['service_type'],
                    'start' => date('Y-m-d\TH:i:s', strtotime($datetime)),
                    'backgroundColor' => getStatusColor($row['status']),
                    'borderColor' => getStatusColor($row['status']),
                    'textColor' => '#ffffff',
                    'allDay' => false
                ];
            }

            echo json_encode($events);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            error_log('Error fetching calendar events: ' . $e->getMessage());
            echo json_encode(['error' => 'Internal server error', 'details' => $e->getMessage()]);
            exit;
        }
    }
    if ($_GET['action'] === 'filter') {
        $filter = mysqli_real_escape_string($conn, $_GET['filter']);
        $whereClause = match ($filter) {
            'today' => "DATE(a.appointment_date) = CURDATE()",
            'pending' => "a.status = 'pending'", // Explicitly reference the `status` column from the `appointments` table
            'all' => "1",
            default => "1",
        };

        $query = "SELECT 
                    a.appointment_id, 
                    a.appointment_date, 
                    a.appointment_time, 
                    a.status,
                    a.service_type,
                    a.pet_name,
                    u.first_name,
                    u.last_name,
                    CASE 
                        WHEN a.status = 'pending' THEN 'warning'
                        WHEN a.status = 'confirmed' THEN 'info'
                        WHEN a.status = 'completed' THEN 'success'
                        WHEN a.status = 'cancelled' THEN 'danger'
                        ELSE 'secondary'
                    END AS status_color
                  FROM appointments a
                  JOIN users u ON a.user_id = u.id
                  WHERE $whereClause
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        try {
            $result = mysqli_query($conn, $query);
            if (!$result) {
                throw new Exception(mysqli_error($conn)); // Log database errors
            }

            $appointments = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $appointments[] = $row;
            }

            echo json_encode($appointments);
            exit();
        } catch (Exception $e) {
            error_log('Error in filter action: ' . $e->getMessage()); // Log the error
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error', 'details' => $e->getMessage()]);
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'view') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $query = "SELECT a.*, u.first_name, u.last_name, u.email 
                  FROM appointments a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE a.appointment_id = '$id'";
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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'update_status') {
        $id = mysqli_real_escape_string($conn, $data['id']);
        $status = mysqli_real_escape_string($conn, $data['status']);
        
        $query = "UPDATE appointments SET status = '$status' WHERE appointment_id = '$id'";
        $success = mysqli_query($conn, $query);
        
        echo json_encode(['success' => $success]);
    }
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
