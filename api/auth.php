<?php
session_start();
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
$password = $_POST['password'];

$sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user   = mysqli_fetch_assoc($result);

// ===== DEBUG SEMENTARA =====
echo "<pre>";
echo "Email dicari: $email\n";
echo "User ditemukan: " . ($user ? "YA" : "TIDAK") . "\n";
if ($user) {
    echo "ID: " . $user['id'] . "\n";
    echo "Fullname: " . $user['fullname'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Password cocok: " . (password_verify($password, $user['password']) ? "YA" : "TIDAK") . "\n";
}
echo "</pre>";
die("--- STOP DEBUG ---");
// ===== END DEBUG =====
?>