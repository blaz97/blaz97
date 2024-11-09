<?php
session_start();

// Include database connection
include 'db_connection.php';

// Check if the teacher is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    die("You must be logged in as a teacher to access this page.");
}

// Get the logged-in user's ID
$teacher_id = $_SESSION['user_id'];

// Fetch the teacher's ID from the teachers table
$sql_teacher = "SELECT teacher_id FROM teachers WHERE user_id = ?";
$stmt_teacher = $conn->prepare($sql_teacher);
$stmt_teacher->bind_param("i", $teacher_id);
$stmt_teacher->execute();
$teacher_result = $stmt_teacher->get_result();

if ($teacher_result->num_rows > 0) {
    $teacher_row = $teacher_result->fetch_assoc();
    $teacher_id = $teacher_row['teacher_id'];

    // Fetch the courses taught by the logged-in teacher
    $sql_courses = "SELECT course_id, course_name FROM courses WHERE teacher_id = ?";
    $stmt_courses = $conn->prepare($sql_courses);
    $stmt_courses->bind_param("i", $teacher_id);
    $stmt_courses->execute();
    $courses_result = $stmt_courses->get_result();

    $courses = [];
    if ($courses_result->num_rows > 0) {
        while ($course = $courses_result->fetch_assoc()) {
            $courses[] = $course;
        }
    }
} else {
    die("Teacher ID not found in the database.");
}

// Handle grading form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id']) && isset($_POST['course_id']) && isset($_POST['grade'])) {
    foreach ($_POST['student_id'] as $index => $student_id) {
        $course_id = $_POST['course_id'];
        $grade = $_POST['grade'][$index];

        $sql_grade = "INSERT INTO grades (student_id, course_id, grade, grade_date) VALUES (?, ?, ?, NOW())";
        $stmt_grade = $conn->prepare($sql_grade);
        $stmt_grade->bind_param("iis", $student_id, $course_id, $grade);

        if ($stmt_grade->execute()) {
            $message = "Grade successfully assigned!";
        } else {
            $message = "Error assigning grade: " . $stmt_grade->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades - Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/ManageGrades.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="logout.html">Logout</a>
    </nav>
    <div class="container">
        <h1>Manage Grades</h1>
        
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
                
                <?php
                // Fetch students enrolled in the current course
                include 'db_connection.php'; // Reopen connection for nested query
                $sql_students = "SELECT s.student_id, s.first_name, s.last_name FROM enrollments e 
                                 JOIN students s ON e.student_id = s.student_id 
                                 WHERE e.course_id = ?";
                $stmt_students = $conn->prepare($sql_students);
                $stmt_students->bind_param("i", $course['course_id']);
                $stmt_students->execute();
                $students_result = $stmt_students->get_result();

                if ($students_result->num_rows > 0): ?>
                    <form action="ManageGrades.php" method="post">
                        <table>
                            <tr>
                                <th>Student Name</th>
                                <th>Assign Grade</th>
                            </tr>
                            <?php while ($student = $students_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td>
                                        <input type="hidden" name="student_id[]" value="<?php echo $student['student_id']; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                        <input type="text" name="grade[]" required>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                        <button type="submit">Submit Grades</button>
                    </form>
                <?php else: ?>
                    <p>No students enrolled in this course.</p>
                <?php endif; ?>
                
                <?php $conn->close(); // Close nested query connection ?>
                
            <?php endforeach; ?>
        <?php else: ?>
            <p>No courses assigned to you.</p>
        <?php endif; ?>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
