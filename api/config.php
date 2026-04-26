<?php
$host   = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$user   = '36Js8ra1yYJRUrz.root';
$pass   = '82CdEOFZFqBq1tXG';
$dbname = 'digitour_db';
$port   = 4000;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $connection = new PDO($dsn, $user, $pass, [
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA                => '/etc/ssl/certs/ca-certificates.crt',
        PDO::ATTR_ERRMODE                     => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT                     => 8,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die("DB Error: " . $e->getMessage());
}
?>