<?php 
// config/ConnectDB.php
$conn = new mysqli("localhost", "root", "", "cho_tot"); // Sửa lại tên DB của bạn nếu khác
mysqli_set_charset($conn, 'UTF8');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>