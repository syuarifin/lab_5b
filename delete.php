<?php
// Include database connection
include 'database.php';

// Start session to check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if the 'delete' parameter exists
if (isset($_GET['delete'])) {
    $matricToDelete = $_GET['delete'];

    // Prepare the delete query
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE matric = ?");
    $deleteQuery->bind_param("s", $matricToDelete);

    // Execute the delete query
    if ($deleteQuery->execute()) {
        // Redirect to display.php after successful deletion
        header("Location: display.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    // Close the prepared statement
    $deleteQuery->close();
} else {
    // If 'delete' parameter is not set, redirect to display.php
    header("Location: display.php");
    exit;
}
?>
