<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meal_ordering";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(" Database connection failed: " . $conn->connect_error);
} else {
    echo "Successfully connected to the database: " . htmlspecialchars($dbname);
}
?>
