<?php
require_once(__DIR__ . '/../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Fetch current status
    $query = "SELECT status FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $newStatus = $row['status'] === 'active' ? 'inactive' : 'active';

        // Update status
        $updateQuery = "UPDATE users SET status = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'si', $newStatus, $userId);
        mysqli_stmt_execute($updateStmt);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($updateStmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

mysqli_close($conn);
?>
