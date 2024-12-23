<?php
// Include database connection
include 'database.php';

// Start session to check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch data for the user to update
if (isset($_GET['matric'])) {
    $matric = $_GET['matric'];
    $query = "SELECT matric, name, role FROM users WHERE matric = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists, fetch their data
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found!";
        exit;
    }
    $stmt->close();
}

// Variable for success or error message
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matric = $_POST['matric'];
    $name = $_POST['name'];
    $role = $_POST['role'];

    // Validate input
    if (empty($name) || empty($role)) {
        $message = "Please fill in all fields.";
    } else {
        // Update query
        $updateQuery = "UPDATE users SET matric = ?, name = ?, role = ? WHERE matric = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssss", $matric, $name, $role, $user['matric']);

        if ($updateStmt->execute()) {
            $message = "User information updated successfully!";
        } else {
            $message = "Error updating record: " . $conn->error;
        }

        $updateStmt->close();
    }
}

// Handle cancel action
if (isset($_POST['cancel'])) {
    header("Location: display.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-button {
            background-color: #45a049;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Update User Information</h2>

    <?php
    // Display success or error message if there is one
    if ($message) {
        $messageType = strpos($message, "Error") === false ? 'success' : 'error';
        echo "<div class='message $messageType'>" . $message . "</div>";
    }
    ?>

    <form method="POST" action="update_form.php?matric=<?php echo htmlspecialchars($matric); ?>">
        <label for="matric">Matric:</label>
        <input type="text" id="matric" name="matric" value="<?php echo htmlspecialchars($user['matric']); ?>" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="role">Access Level:</label>
        <select id="role" name="role" required>
            <option value="lecturer" <?php echo ($user['role'] == 'lecturer') ? 'selected' : ''; ?>>Lecturer</option>
            <option value="student" <?php echo ($user['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
        </select>

        <input type="submit" value="Update User">
        <input type="submit" name="cancel" value="Cancel" onclick="return confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')">
    </form>

    <!-- Back Button to display.php -->
    <a href="display.php" class="back-button">Back to Display</a>

</body>
</html>
