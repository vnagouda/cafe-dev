<?php
$servername = "cafe-db-cluster-instance-1.c1sz2xjfrj1w.us-east-1.rds.amazonaws.com";
$username = "admin"; // Replace with your RDS master username
$password = "Talent14#"; // Replace with your RDS master password
$dbname = "cafe_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
