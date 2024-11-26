<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Student Performance Management System</title>
    <link rel="stylesheet" href="../css/EditUsers.css">
</head>
<body>
    <nav>
        <a href="ManageUsers.php">Back to Manage Users</a>
        <a href="logout.html">Logout</a>
    </nav>
    <div class="container">
        <h1>Edit User</h1>
        <?php
        include 'db_connection.php';

        $type = $_GET['type'];
        $id = $_GET['id'];

        if ($type == 'teacher') {
            $sql = "SELECT u.username, u.email, t.first_name, t.last_name, t.phone_number, t.hire_date, t.teacher_id
                    FROM users u
                    JOIN teachers t ON u.user_id = t.user_id
                    WHERE t.teacher_id = $id";
        } elseif ($type == 'student') {
            $sql = "SELECT u.username, u.email, s.first_name, s.last_name, s.date_of_birth, s.gender, s.enrollment_date, s.student_id
                    FROM users u
                    JOIN students s ON u.user_id = s.user_id
                    WHERE s.student_id = $id";
        }

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        if ($type == 'teacher') {
        ?>
            <form action="" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $row['first_name']; ?>" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $row['last_name']; ?>" required>
                
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo $row['phone_number']; ?>">
                
                <label for="hire_date">Hire Date:</label>
                <input type="date" id="hire_date" name="hire_date" value="<?php echo $row['hire_date']; ?>" required>
                
                <button type="submit" name="update_teacher">Update Teacher</button>
            </form>
        <?php
        } elseif ($type == 'student') {
        ?>
            <form action="" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $row['first_name']; ?>" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $row['last_name']; ?>" required>
                
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $row['date_of_birth']; ?>" required>
                
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php if ($row['gender'] == 'male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if ($row['gender'] == 'female') echo 'selected'; ?>>Female</option>
                    <option value="other" <?php if ($row['gender'] == 'other') echo 'selected'; ?>>Other</option>
                </select>
                
                <label for="enrollment_date">Enrollment Date:</label>
                <input type="date" id="enrollment_date" name="enrollment_date" value="<?php echo $row['enrollment_date']; ?>" required>
                
                <button type="submit" name="update_student">Update Student</button>
            </form>
        <?php
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];

            if ($type == 'teacher') {
                $phone_number = $_POST['phone_number'];
                $hire_date = $_POST['hire_date'];

                // Update teacher details
                $sql = "UPDATE users u
                        JOIN teachers t ON u.user_id = t.user_id
                        SET u.username = '$username', u.email = '$email', t.first_name = '$first_name',
                            t.last_name = '$last_name', t.phone_number = '$phone_number', t.hire_date = '$hire_date'
                        WHERE t.teacher_id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "Teacher details updated successfully";
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } elseif ($type == 'student') {
                $date_of_birth = $_POST['date_of_birth'];
                $gender = $_POST['gender'];
                $enrollment_date = $_POST['enrollment_date'];

                // Update student details
                $sql = "UPDATE users u
                        JOIN students s ON u.user_id = s.user_id
                        SET u.username = '$username', u.email = '$email', s.first_name = '$first_name',
                            s.last_name = '$last_name', s.date_of_birth = '$date_of_birth', s.gender = '$gender',
                            s.enrollment_date = '$enrollment_date'
                        WHERE s.student_id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "Student details updated successfully";
                } else {
                    echo "Error updating record: " . $conn->error;
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
