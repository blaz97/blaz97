<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$grades = [];

// Fetch the student's grades
$sql = "SELECT c.course_name, g.grade, g.grade_date 
        FROM grades g
        JOIN courses c ON g.course_id = c.course_id
        JOIN students s ON g.student_id = s.student_id
        WHERE s.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$grades = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grades - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/ViewGrades.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h1>View Grades</h1>
        <?php if (!empty($grades)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No grades found.</p>
        <?php endif; ?>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
