<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_register";

$conn = new mysqli($servername, $username, $password, $dbname);
//If error occurs while making a connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
