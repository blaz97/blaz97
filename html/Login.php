<?php
session_start();

// Include database connection
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from users table
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['password']) { // Replace with password_verify($password, $row['password']) if passwords are hashed
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] == 'admin') {
                header("Location: AdminDashboard.html");
            } elseif ($row['role'] == 'teacher') {
                header("Location: TeacherDashboard.html");
            } elseif ($row['role'] == 'student') {
                header("Location: StudentDashboard.html");
            }
            exit();
        } else {
            echo "<p style='color: red;'>Invalid password.</p>";
        }
    } else {
        echo "<p style='color: red;'>No user found with this email.</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/Login.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
        <a href="register.html">Register</a>
    </nav>
    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
