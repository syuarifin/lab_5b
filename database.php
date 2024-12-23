<?php
// Database configuration
$host = 'localhost';     // Hostname (usually localhost)
$username = 'root';      // Your database username
$password = '';          // Your database password
$database = 'lab_5b';    // The database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful message (for debugging, remove in production)
echo "Connected successfully to the database.";
?>
