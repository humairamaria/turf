<?php 

$servername = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'turf';


$conn = new mysqli($servername, $USERNAME, $PASSWORD, $DATABASE);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



?>
