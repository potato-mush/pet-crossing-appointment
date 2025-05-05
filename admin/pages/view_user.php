<?php
require_once(__DIR__ . '/../includes/db_connection.php');

if (!isset($_GET['id'])) {
    echo "<p>User ID is required.</p>";
    exit;
}

$userId = intval($_GET['id']);
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
    echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone']) . "</p>";
    echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
    echo "<p><strong>Joined Date:</strong> " . date('M d, Y', strtotime($row['created_at'])) . "</p>";
} else {
    echo "<p>User not found.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
