<?php
// Connection to the database
$servername = "localhost";
$user = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$dbname = "scream"; // Replace with your DB name

$conn = new mysqli($servername, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>