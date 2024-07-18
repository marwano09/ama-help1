<?php
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL root password
$dbname = "hassan2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
