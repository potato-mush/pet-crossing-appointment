<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once('includes/Database.php');
$database = new Database();
$db = $database->connect();

// Get available services
$query = "SELECT * FROM services WHERE status = 'active'";
$stmt = $db->prepare($query);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Pet Crossing</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/appointment.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="appointment-container">
            <div class="calendar-section">
                <div id="calendar"></div>
            </div>
            <div class="booking-form">
                <h2>Book an Appointment</h2>
                <form id="appointmentForm" method="POST">
                    <input type="hidden" id="user_id" value="<?= $_SESSION['user_id'] ?>"> <!-- Add this line -->
                    <div class="form-group">
                        <label for="service">Service</label>
                        <select name="service" id="service" required>
                            <option value="">Select a service</option>
                            <?php foreach($services as $service): ?>
                                <option value="<?= $service['id'] ?>" 
                                        data-duration="<?= $service['duration'] ?>">
                                    <?= $service['name'] ?> (<?= $service['duration'] ?> mins)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pet_name">Pet Name</label>
                        <input type="text" name="pet_name" id="pet_name" required>
                    </div>
                    <div class="form-group">
                        <label for="appointment_date">Date</label>
                        <input type="date" 
                               name="appointment_date" 
                               id="appointment_date" 
                               required
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    <div class="form-group">
                        <label for="appointment_time">Time</label>
                        <select name="appointment_time" id="appointment_time" required disabled>
                            <option value="">Select a date first</option>
                            <?php
                            $times = [];
                            for ($hour = 8; $hour <= 18; $hour++) {
                                $ampm = $hour >= 12 ? 'PM' : 'AM';
                                $hour12 = $hour > 12 ? $hour - 12 : $hour;
                                $time = sprintf('%02d:00', $hour);
                                echo "<option value='$time:00'>$hour12:00 $ampm</option>";
                                
                                if ($hour != 18) {
                                    $time = sprintf('%02d:30', $hour);
                                    echo "<option value='$time:00'>$hour12:30 $ampm</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes"></textarea>
                    </div>
                    <button type="submit">Book Appointment</button>
                </form>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="assets/js/appointment.js"></script>
</body>
</html>
