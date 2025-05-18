<?php
$host = 'mysql-8.0.local';      
$db = 'StudMindCheck';   
$user = 'root';           
$pass = '';               

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}
?>