<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$attendance_records = [];

// Fetch the student's attendance records
$sql = "SELECT c.course_name, a.attendance_date, a.status
        FROM attendance a
        JOIN courses c ON a.course_id = c.course_id
        JOIN students s ON a.student_id = s.student_id
        WHERE s.user_id = ?
        ORDER BY a.attendance_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance_records = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/ViewAttendance.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h1>View Attendance</h1>
        <?php if (!empty($attendance_records)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records found.</p>
        <?php endif; ?>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
