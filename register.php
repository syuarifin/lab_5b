<?php
// Include database connection
include 'database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = $_POST['matric'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role']; // Get selected role

    // Check if matric already exists
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE matric = ?");
    $checkQuery->bind_param("s", $matric);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        $error = "Matric number is already registered.";
    } else {
        // Insert new user into the database
        $insertQuery = $conn->prepare("INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)");
        $insertQuery->bind_param("ssss", $matric, $name, $password, $role);

        if ($insertQuery->execute()) {
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }

    // Close statements
    $checkQuery->close();
    if (isset($insertQuery)) $insertQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .register-container {
            width: 300px;
            margin: 100px auto;
            border: 1px solid #ccc;
            padding: 20px;
            text-align: center;
        }
        .register-container input[type="text"],
        .register-container input[type="password"],
        .register-container select {
            width: 90%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-container input[type="submit"] {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .register-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .register-container a {
            text-decoration: none;
            color: #007BFF;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
        .message {
            margin: 10px 0;
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form action="register.php" method="POST">
            <label for="matric">Matric:</label><br>
            <input type="text" id="matric" name="matric" required><br>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <label for="role">Role:</label><br>
            <select id="role" name="role" required>
                <option value="" disabled selected>Please select</option>
                <option value="lecturer">Lecturer</option>
                <option value="student">Student</option>
            </select><br>
            <input type="submit" value="Submit">
        </form>

        <!-- Display success or error messages -->
        <?php
        if (isset($success)) {
            echo "<p class='message success'>$success</p>";
        }
        if (isset($error)) {
            echo "<p class='message'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
