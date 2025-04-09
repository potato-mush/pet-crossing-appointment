<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $slots = [];
    
    // Generate fixed time slots from 8 AM to 6 PM
    $start_hour = 8;
    $end_hour = 18;

    for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
        // Generate slots for each hour
        $time = sprintf('%02d:00:00', $hour);
        $timestamp = strtotime($time);
        
        $slots[] = [
            'time' => $time,
            'formatted_time' => date('h:i A', $timestamp)
        ];

        // Add half-hour slot except for the last hour
        if ($hour != $end_hour) {
            $time = sprintf('%02d:30:00', $hour);
            $timestamp = strtotime($time);
            
            $slots[] = [
                'time' => $time,
                'formatted_time' => date('h:i A', $timestamp)
            ];
        }
    }

    echo json_encode($slots);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load time slots']);
}
