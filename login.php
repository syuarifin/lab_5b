<?php
// Include database connection
include 'database.php';

// Start session
session_start();

$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = $_POST['matric'];
    $password = $_POST['password'];

    // Prepare and execute the query to check the matric and password
    $query = $conn->prepare("SELECT * FROM users WHERE matric = ?");
    $query->bind_param("s", $matric);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct; set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['matric'] = $user['matric'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect to display.php
            header("Location: display.php");
            exit;
        } else {
            // Incorrect password
            $error = "Invalid matric or password. Please <a href='login.php'>login</a> again.";
        }
    } else {
        // Matric not found
        $error = "Matric not found. Please <a href='login.php'>login</a> again.";
    }

    $query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .login-container {
            width: 300px;
            margin: 100px auto;
            border: 1px solid #ccc;
            padding: 20px;
            text-align: center;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 90%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container input[type="submit"] {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .login-container a {
            text-decoration: none;
            color: #007BFF;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="login.php" method="POST">
            <label for="matric">Matric:</label><br>
            <input type="text" id="matric" name="matric" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <p>
            <a href="register.php">Register</a> here if you have not.
        </p>
        <?php
        // Display error message if there is one
        if (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
