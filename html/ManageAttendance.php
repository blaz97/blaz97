<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$courses = [];
$students = [];

// Fetch the teacher's courses
$sql = "SELECT c.course_id, c.course_name FROM courses c
        JOIN teachers t ON c.teacher_id = t.teacher_id
        WHERE t.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($courses)) {
    $course_id = $courses[0]['course_id'];

    // Fetch students enrolled in the teacher's course
    $sql = "SELECT s.student_id, s.first_name, s.last_name FROM students s
            JOIN enrollments e ON s.student_id = e.student_id
            WHERE e.course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_ids = $_POST['student_id'];
    $attendance_statuses = $_POST['attendance_status'];
    $course_id = $_POST['course_id'];

    for ($i = 0; $i < count($student_ids); $i++) {
        $student_id = $student_ids[$i];
        $attendance_status = $attendance_statuses[$i];

        // Insert attendance record
        $sql = "INSERT INTO attendance (student_id, course_id, attendance_date, status, created_at, updated_at) 
                VALUES (?, ?, NOW(), ?, NOW(), NOW()) 
                ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = VALUES(updated_at)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $student_id, $course_id, $attendance_status);
        $stmt->execute();
    }

    $_SESSION['message'] = "Attendance submitted successfully.";
    header("Location: ManageAttendance.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/TeacherDashboard.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h1>Manage Attendance</h1>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <?php if (!empty($courses)): ?>
            <h2><?php echo $courses[0]['course_name']; ?></h2>
            <?php if (!empty($students)): ?>
                <form action="ManageAttendance.php" method="post">
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['first_name'] . " " . $student['last_name']; ?></td>
                                    <td>
                                        <input type="hidden" name="student_id[]" value="<?php echo $student['student_id']; ?>">
                                        <select name="attendance_status[]" required>
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <button type="submit">Submit Attendance</button>
                </form>
            <?php else: ?>
                <p>No students are enrolled in this course.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>You are not assigned to any course.</p>
        <?php endif; ?>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
