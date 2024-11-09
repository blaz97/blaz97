<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/AdminRegister.css">
    <script>
        function showAlert(message) {
            alert(message);
            window.location.href = 'RegisterChoice.html';
        }
    </script>
</head>
<body>
    <nav>
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
        <a href="register.html">Register</a>
    </nav>
    <div class="container">
        <h1>Admin Registration</h1>
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="Admin" readonly>

            <button type="submit">Register</button>
        </form>

        <?php
        // Include database connection
        include 'db_connection.php';

        // Check if an admin already exists
        $check_admin = "SELECT COUNT(*) AS admin_count FROM users WHERE role='admin'";
        $result = $conn->query($check_admin);
        $row = $result->fetch_assoc();

        if ($row['admin_count'] > 0) {
            echo "<script>showAlert('Unauthorized access! Admin already exists.');</script>";
        } else {
            // Handle form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = "Admin";

                // Insert data into users table
                $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

                if ($conn->query($sql) === TRUE) {
                    echo "<p style='color: green;'>New admin registered successfully!</p>";
                } else {
                    echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
                }
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
