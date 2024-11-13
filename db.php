<?php
$host = 'localhost';
$dbname = 'backend';
$username = 'root';
$password = ''; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
