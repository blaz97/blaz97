<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/TeacherRegister.css">
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
        <a href="register.html">Register</a>
    </nav>
    <div class="container">
        <h1>Teacher Registration</h1>
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" required>

            <label for="hire_date">Hire Date:</label>
            <input type="date" id="hire_date" name="hire_date" required>
            
            <button type="submit">Register</button>
        </form>

        <?php
        // Include database connection
        include 'db_connection.php';

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Common user info
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password']; // Do not hash the password

            // Teacher-specific info
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $phone_number = $_POST['phone_number'];
            $hire_date = $_POST['hire_date'];

            // Insert common user info into users table
            $sql_user = "INSERT INTO Users (username, email, password, role) VALUES ('$username', '$email', '$password', 'teacher')";

            if ($conn->query($sql_user) === TRUE) {
                // Get the last inserted user ID
                $user_id = $conn->insert_id;

                // Insert teacher-specific info into teachers table
                $sql_teacher = "INSERT INTO Teachers (first_name, last_name, email, phone_number, hire_date, user_id) 
                                VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$hire_date', '$user_id')";

                if ($conn->query($sql_teacher) === TRUE) {
                    echo "<p style='color: green;'>New teacher registered successfully!</p>";
                } else {
                    echo "<p style='color: red;'>Error in teacher registration: " . $conn->error . "</p>";
                }
            } else {
                echo "<p style='color: red;'>Error in user registration: " . $conn->error . "</p>";
            }
        }

        $conn->close();
        ?>
    </div>
    <footer>
        &copy; 2024 Student Performance Management System
    </footer>
</body>
</html>
