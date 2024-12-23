<?php
// Include database connection
include 'database.php';

// Start session to check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle delete action
if (isset($_GET['delete'])) {
    $matricToDelete = $_GET['delete'];
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE matric = ?");
    $deleteQuery->bind_param("s", $matricToDelete);
    if ($deleteQuery->execute()) {
        header("Location: display.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $deleteQuery->close();
}

// Fetch data from the database
$query = "SELECT matric, name, role FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 1.5em;
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-button {
            background-color:rgb(227, 218, 217);
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color:rgb(232, 227, 227);
        }
    </style>
</head>
<body>
    <table>
        <caption>List of Users</caption>
        <tr>
            <th>Matric</th>
            <th>Name</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['matric']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "<td>
                        <a href='update_form.php?matric=" . htmlspecialchars($row['matric']) . "'>Update</a> | 
                        <a href='display.php?delete=" . htmlspecialchars($row['matric']) . "' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found</td></tr>";
        }
        ?>
    </table>

    <!-- Back button to redirect to login.php -->
    <button class="back-button" onclick="window.location.href='login.php';">Back to Login</button>
</body>
</html>
