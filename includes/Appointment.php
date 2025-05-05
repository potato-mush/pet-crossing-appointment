<?php
class Appointment {
    private $conn;
    private $table = 'appointments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkAvailability($date, $time, $duration) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                 WHERE appointment_date = :date 
                 AND status != 'cancelled'
                 AND (
                     (appointment_time <= :time AND DATE_ADD(appointment_time, INTERVAL duration MINUTE) > :time)
                     OR
                     (appointment_time < DATE_ADD(:time, INTERVAL :duration MINUTE) AND appointment_time >= :time)
                 )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':duration', $duration);
        $stmt->execute();
        
        return $stmt->fetchColumn() == 0;
    }

    public function create($userId, $serviceId, $petName, $date, $time, $duration, $notes) {
        // Fetch the service name based on the service ID
        $serviceQuery = "SELECT name FROM services WHERE id = :service_id";
        $serviceStmt = $this->conn->prepare($serviceQuery);
        $serviceStmt->bindParam(':service_id', $serviceId);
        $serviceStmt->execute();
        $serviceName = $serviceStmt->fetchColumn();

        if (!$serviceName) {
            throw new Exception("Invalid service selected.");
        }

        $query = "INSERT INTO " . $this->table . "
                (user_id, service_type, pet_name, appointment_date, appointment_time, duration, notes, status)
                VALUES (:user_id, :service_type, :pet_name, :date, :time, :duration, :notes, 'pending')";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':service_type', $serviceName); // Insert service name
        $stmt->bindParam(':pet_name', $petName);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':notes', $notes);

        return $stmt->execute();
    }

    public function getBookedAppointments() {
        $query = "SELECT appointment_date, appointment_time, pet_name, service_type, user_id, status 
                  FROM " . $this->table . " 
                  WHERE status != 'cancelled'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
