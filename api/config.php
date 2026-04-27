<?php
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com"; 
$port = 4000;
$user = "36Js8ra1yYJRUrz.root";    
$pass = "82CdEOFZFqBq1tXG";       
$db   = "digitour_db";        

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();

mysqli_ssl_set($conn, null, null, null, null, null);

$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $db,
    $port,
    null,
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Koneksi gagal: ' . mysqli_connect_error()
    ]));
}

mysqli_set_charset($conn, 'utf8mb4');
?>